<?php

namespace App\Livewire\Menu;

use App\Imports\ImportUploadAssy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class AssyUpload extends Component
{
    use WithFileUploads;

    public $uploadId;
    public $prosesUploading = false;
    public $file;
    public $periode;
    public $nik;

    protected $rules = [
        'file' => 'required|mimes:xls,xlsx|max:80',  // max 20MB
    ];

    public function mount($nik)
    {
        $this->nik = $nik;
    }

    public function uploadFile($bulan, $tahun)
    {
        $date                  = new \DateTime("$tahun-$bulan-01");
        $this->uploadId        = uniqid('assy_', true);
        $this->prosesUploading = true;
        $this->validate();

        $ex        = Excel::toArray(new ImportUploadAssy(), $this->file);
        // hapus row yang kebaca tapi spasi tok isinya, marai error
        $clean     = array_filter(
            $ex[0],
            function ($row) {
                foreach ($row as $v) {
                    if (trim((string) $v) !== '')
                        return true;
                }
                return false;
            }
        );
        $totalHari = $date->format("t");
        $totalData = count($clean) * $totalHari;

        Cache::store('file')->put("upload-assy-data:{$this->uploadId}", $clean);
        Cache::store('file')->put("upload-assy-progress:{$this->uploadId}", [
            'index'     => 0,
            'processed' => 0,
            'totalHari' => $totalHari,
            'totalData' => $totalData,
            'bulan'     => $bulan,
            'tahun'     => $tahun,
        ]);
        $this->dispatch('upload-started', uploadId: $this->uploadId);
        return $this->uploadId;
    }

    public function partialUpload()
    {
        if (!$this->prosesUploading || !$this->uploadId) {
            return 'idle';
        }

        $rows  = Cache::store('file')->get("upload-assy-data:{$this->uploadId}", []);
        $state = Cache::store('file')->get("upload-assy-progress:{$this->uploadId}");

        if (!$state || empty($rows)) {
            $this->isProcessing = false;
            return 'done';
        }

        $index     = $state['index'];
        $processed = $state['processed'];
        $totalHari = $state['totalHari'];
        $totalData = $state['totalData'];
        $bulan     = $state['bulan'];
        $tahun     = $state['tahun'];

        $berapaBaris = 1;  // iki chunk proses ee, ojo akeh2 ben gak abot banget, 1 /2 wae. soale 1 = 1bulan (30 x insert)
        $rowsCount   = count($rows);

        for ($c = 0; $c < $berapaBaris && $index < $rowsCount; $c++, $index++) {
            $row = $rows[$index];

            $tanggal = [];
            for ($i = 1; $i <= $totalHari; $i++) {
                if (array_key_exists($i, $row)) {
                    $tanggal[$i] = $row[$i];
                    unset($row[$i]);
                }
            }

            foreach ($tanggal as $k => $value) {
                // cek kalo ada yang kosong di skip, biar gak error, dobel cek, atas wis tak cek tapi kadang lolos
                if ($row['smh'] === null && $row['remain'] === null && $row['line_cd'] === null && $row['partno'] === null && $row['dc'] === null && $row['cust'] === null)
                    continue;

                $this->importAssy(
                    $tahun . $bulan,      // @periode varchar(20),		-- 202411
                    $row['line_cd'],      // @line_cd varchar(20),		-- xhn1
                    $row['partno'],       // @product_no varchar(100),	-- 32110-6HA-J700
                    $row['dc'],           // @dc varchar(20),			-- 00-01-02
                    $row['cust'],         // @customer varchar(50),		-- HPM
                    $row['smh'],          // @smh decimal(10,6),			-- smh
                    $k,                   // @tanggal varchar(20),		-- 21
                    $value,               // @qty varchar(20),			-- 60
                    $row['remain'] ?? 0,  // @remain numeric,			-- 0
                    $this->nik,           // @user varchar(50)			-- demo
                );
                //
                // DB::connection('it')->statement(
                //     "EXEC sp_Assy_daily_Import_Plan ?, ?, ?, ?, ?, ?, ?, ?, ?, ?",
                //     [
                //     ]
                // );

                $processed++;
            }
        }

        DB::connection('it')
            ->table('PRS_Master_Product')
            ->insert([
                'product_no' => $row['partno'],
                'dc'         => $row['dc'],
                'smh'        => $row['smh'],
                'created_by' => $this->nik,
            ]);
        Cache::store('file')->put("upload-assy-progress:{$this->uploadId}", [
            'index'     => $index,
            'processed' => $processed,
            'totalHari' => $totalHari,
            'totalData' => $totalData,
            'bulan'     => $bulan,
            'tahun'     => $tahun,
        ]);
        $percent = $totalData > 0 ? (int) ($processed / $totalData * 100) : 0;

        // kalau sudah selesai, di hapus cache ne
        if ($index >= $rowsCount) {
            $this->isProcessing = false;

            Cache::store('file')->forget("upload-assy-data:{$this->uploadId}");
            Cache::store('file')->forget("upload-assy-progress:{$this->uploadId}");

            $this->dispatch('done-upload', text: "Selesai {$processed} data");

            return 'done';
        }

        return "Telah diproses {$processed} dari {$totalData} data ({$percent}%)";
    }

    public function getLineCode()
    {
        $line = DB::connection("it")->table("PRS_Master_Line")->select("Line")->get();
        return $line;
    }

    public function searching($productNo, $line, $y, $b)
    {
        $periode = $y . $b;
        $data    = DB::connection('it')->select(
            "SET NOCOUNT ON;
            EXEC sp_Assy_daily_Plan ?, ?, ?, ?, ?, ?, ?, ?, ?",
            [
                'pivot',
                0,
                $periode,
                $line,
                $productNo,
                '',
                '',
                0,
                ''
            ]
        );
        return $data;
    }

    public function render()
    {
        return view('livewire.menu.assy-upload');
    }

    public function importAssy(
        $periode,
        $lineCd,
        $productNo,
        $dc,
        $customer,
        $smh,
        $tanggal,
        $qty,
        $remain,
        $user
    ) {
        return DB::connection('it')->transaction(function () use (
            $periode,
            $lineCd,
            $productNo,
            $dc,
            $customer,
            $smh,
            $tanggal,
            $qty,
            $remain,
            $user
        ) {
            $periode = trim($periode);
            $tahun   = (int) substr($periode, 0, 4);  // 2024
            $bulan   = (int) substr($periode, 4, 2);  // 11
            $hari    = (int) ltrim($tanggal, '0');    // "01" -> 1, "21" -> 21

            $tanggalDate = Carbon::createFromDate($tahun, $bulan, $hari)->startOfDay();

            $isFirstDay = $tanggalDate->day === 1;

            $qty = trim((string) $qty);
            if ($qty === '') {
                $qty = 0;
            } else {
                $qty = (int) $qty;
            }

            // remain pekso int
            $remain = $remain === null || $remain === '' ? 0 : (int) $remain;

            // decimal/float, dan ilangi spasi & koma
            $smh = $smh === null || $smh === ''
                       ? null
                       : (float) str_replace(',', '.', trim((string) $smh));

            $conn   = DB::connection('it');
            $lineId = $conn->table('PRS_Master_Line')
                           ->where('Line', trim($lineCd))
                           ->orderBy('id')
                           ->value('id');

            $lineId = $lineId ?? 0;

            $productId = $conn->table('PRS_Master_Product')
                              ->where('product_no', trim($productNo))
                              ->where('dc', trim($dc))
                              ->whereNotNull('smh')
                              ->orderBy('id')
                              ->value('id');

            $productId = $productId ?? 0;

            // nek beda db ganti koneksi
            $customerId = DB::connection('it')->table('gm_customer')
                                              ->where('customer', trim($customer))
                                              ->orderBy('id')
                                              ->value('id');

            $customerId = $customerId ?? 0;

            $cekPlanId = $conn->table('PRS_Assy_Daily_Rev1_Plan')
                              ->where('line_id', $lineId)
                              ->whereDate('tanggal', $tanggalDate->toDateString())
                              ->where('product_id', $productId)
                              ->where('cust_id', $customerId)
                              ->orderBy('id')
                              ->value('id');

            $cekPlanId = $cekPlanId ?? 0;

            if ($productId != 0) {
                // produk sudah ada
                if ($isFirstDay) {
                    // ======= JIKA TANGGAL 1 =======

                    if ($cekPlanId != 0) {
                        // plan sudah ada -> update
                        if ($qty == 0) {
                            $conn->table('PRS_Assy_Daily_Rev1_Plan')
                                 ->where('id', $cekPlanId)
                                 ->update([
                                     'updated_at' => now(),
                                     'updated_by' => $user,
                                     'planning'   => 0,
                                     'remain'     => $remain,
                                 ]);
                        } else {
                            $conn->table('PRS_Assy_Daily_Rev1_Plan')
                                 ->where('id', $cekPlanId)
                                 ->update([
                                     'updated_at' => now(),
                                     'updated_by' => $user,
                                     'product_id' => $productId,
                                     'cust_id'    => $customerId,
                                     'planning'   => $qty,
                                     'remain'     => $remain,
                                     'tanggal'    => $tanggalDate,
                                 ]);
                        }
                    } else {
                        // plan belum ada -> insert
                        $conn->table('PRS_Assy_Daily_Rev1_Plan')->insert([
                            'line_id'    => $lineId,
                            'tanggal'    => $tanggalDate,
                            'product_id' => $productId,
                            'cust_id'    => $customerId,
                            'planning'   => $qty,
                            'remain'     => $remain,
                            'created_at' => now(),
                            'created_by' => $user,
                        ]);
                    }
                } else {
                    // ======= JIKA TANGGAL SELAIN 1 =======

                    if ($cekPlanId != 0) {
                        // plan sudah ada
                        if ($qty == 0) {
                            $conn->table('PRS_Assy_Daily_Rev1_Plan')
                                 ->where('id', $cekPlanId)
                                 ->update([
                                     'updated_at' => now(),
                                     'updated_by' => $user,
                                     'planning'   => null,
                                 ]);
                        } else {
                            $conn->table('PRS_Assy_Daily_Rev1_Plan')
                                 ->where('id', $cekPlanId)
                                 ->update([
                                     'updated_at' => now(),
                                     'updated_by' => $user,
                                     'product_id' => $productId,
                                     'cust_id'    => $customerId,
                                     'planning'   => $qty,
                                     'tanggal'    => $tanggalDate,
                                 ]);
                        }
                    } else {
                        // plan belum ada -> insert
                        $conn->table('PRS_Assy_Daily_Rev1_Plan')->insert([
                            'line_id'    => $lineId,
                            'tanggal'    => $tanggalDate,
                            'product_id' => $productId,
                            'cust_id'    => $customerId,
                            'planning'   => $qty,
                            'created_at' => now(),
                            'created_by' => $user,
                        ]);
                    }
                }

                $conn->table('PRS_Master_Product')
                     ->where('id', $productId)
                     ->update([
                         'smh'        => $smh,
                         'updated_at' => now(),
                         'updated_by' => $user,
                     ]);
            } else {
                // ======= PRODUK BELUM ADA =======

                $conn->table('PRS_Master_Product')
                     ->where('product_no', $productNo)
                     ->update([
                         'active' => 0,
                     ]);

                $conn->table('PRS_Master_Product')->insert([
                    'product_no' => $productNo,
                    'dc'         => $dc,
                    'smh'        => $smh,
                    'active'     => 1,
                    'created_at' => now(),
                    'created_by' => $user,
                ]);
            }

            return true;
        });
    }
}
