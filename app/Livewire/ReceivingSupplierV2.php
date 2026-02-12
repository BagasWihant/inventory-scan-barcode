<?php

namespace App\Livewire;

use App\Exports\ReceivingSupplierReport;
use App\Models\abnormalMaterial;
use App\Models\itemIn;
use App\Models\PaletRegister;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorPNG;
use DB;
use Storage;

class ReceivingSupplierV2 extends Component
{
    public $po;
    public $input_setup_by;
    public $sws_code;
    public $line_code;
    public $userId;

    public function mount()
    {
        $this->userId = auth()->user()->id;
    }

    public function searchDropdown($key)
    {
        return DB::table('material_setup_mst_supplier')
            ->selectRaw('kit_no, kit_no as id')
            ->where('kit_no', 'like', "%$key%")
            ->groupBy('kit_no')
            ->limit(10)
            ->get();
    }

    public function selectDropdown($item)
    {
        $this->po = $item['id'];
        $data = $this->scanMaterial($this->po);
        $this->dispatch('init-material-data', data: $data);
        $this->dispatch(
            'po-selected',
            po: $this->po
        );
    }

    public function scanMaterial($po)
    {
        $joinCondition = function ($join) {
            $join
                ->on('a.material_no', '=', 'b.material_no')
                ->on('a.kit_no', '=', 'b.kit_no');
            // ->where('b.pallet_no', $paletCode);
        };

        $groupByColumns = [
            'a.material_no',
            'a.kit_no',
            'a.line_c',
            'a.setup_by',
            'a.picking_qty',
            'scanned_time',
            'm.loc_cd',
            'd.trucking_id',
            'm.matl_nm',
            'm.qty',
            'm.iss_min_lot'
        ];

        $productsQuery = DB::table('material_setup_mst_supplier as a')
            // ->whereIn('a.material_no', $material_no_list)
            ->where('a.kit_no', $po)
            ->selectRaw("
                    a.material_no,
                    '' AS supplier_code,
                    a.picking_qty,
                    count(a.picking_qty) as pax,
                    a.kit_no, sum(b.picking_qty) as stock_in,
                    a.line_c,
                    a.setup_by,
                    m.loc_cd as location_cd_ori,
                    m.matl_nm,
                    d.trucking_id,  
                    m.qty,
                    m.iss_min_lot")
            ->leftJoin('material_in_stock as b', $joinCondition)
            ->leftJoin('material_mst as m', 'a.material_no', '=', 'm.matl_no')
            ->leftJoin('delivery_mst as d', 'a.kit_no', '=', 'd.pallet_no')
            ->groupBy($groupByColumns)
            ->orderByDesc('scanned_time')
            ->orderBy('a.material_no');
        $value = $productsQuery->get();

        // tambahkan supplier_code disini untuk yang cnc
        // tipe 0 = cnc , otomatis yang punya qr ( slash 2 ) // , walaupun location dipilih assy tapi kalo // tetap tipe 0
        // tipe 1 assy, punya slash 1 di qr nya (/)

        $materialList = $value->pluck('material_no')->all();
        $supplierCode = DB::table('material_conversion_mst')
            ->whereIn('sws_code', $materialList)
            ->selectRaw("sws_code, STRING_AGG(supplier_code, ', ') AS supplier_code")
            ->groupBy('sws_code')
            ->get();

        // Merge kedua nya, ini untuk menambahkan supplier code karena di cnc yang dibaca di qr kodenya beda dengan namanya
        $value = $value->map(function ($value) use ($supplierCode) {
            $swsCode = trim($value->material_no);
            $supplier = $supplierCode->firstWhere('sws_code', $swsCode);

            if ($supplier) {
                $value->supplier_code = $supplier->supplier_code;
            }

            return $value;
        });

        foreach ($value as $k) {
            $k->total = $k->stock_in > 0 ? $k->picking_qty - $k->stock_in : $k->picking_qty;

            // if (trim($data['material_no']) == trim($k->material_no) && $data['line'] == $k->line_c && (int) $data['tipe'] == 1) {
            //     $k->counter = (int) $data['qty'];
            //     $k->location_cd = $data['location_cd'];
            //     $k->scanned = [[(int) $data['qty'], 1]];
            // } elseif (str_contains($k->supplier_code, $data['material_no']) && $data['tipe'] == '0') {
            //     $k->counter = (int) $data['qty'];
            //     $k->location_cd = $k->location_cd_ori;
            //     $k->scanned = [[(int) $data['qty'], 1]];
            // } else {
            //     $k->counter = 0;
            //     $k->location_cd = $k->location_cd_ori;
            //     $k->scanned = [];
            // }
        }

        return $value;
    }

    public function confirm($req)
    {
        $surat_jalan = $req['sj'];
        $scanned = $req['scanned'];
        $sorted = $req['sorted'];
        $location = $req['location'];
        $line_code = $req['line_code'];
        $po = $req['po'];

        $scanPerBox = [];

        // GENERATE PALET CODE
        $getConfig = DB::table('WH_config')->select('value')->whereIn('config', ['PalletCodeInStock', 'PeriodInStock'])->get();
        $ym = date('ym');

        $PalletCodeInStock = (int) $getConfig[0]->value + 1;
        if ($getConfig[1]->value != $ym) {
            $PalletCodeInStock = 1;
            DB::table('WH_config')->where('config', 'PeriodInStock')->update(['value' => $ym]);
        }

        $generatePaletCode = str_pad($PalletCodeInStock, 4, '0', STR_PAD_LEFT);
        $paletCode = 'L-' . $ym . '-' . $generatePaletCode;

        DB::table('WH_config')->where('config', 'PalletCodeInStock')->update(['value' => $PalletCodeInStock]);
        foreach ($scanned as $data) {
            if (!empty($data['scanned'])) {
                $scanData = $data['scanned'];
                $totalScanPerMaterial = count($scanData);
                $iteration = 1;
                $kelebihan = $data['counter'] - abs($data['total']);
                $sisaTerakhir = 0;
                foreach ($scanData as $value) {
                    $qtyPerScan = $value[0];
                    $box = $value[1];

                    $scanPerBox[] = (object) [
                        'po' => $po,
                        'material' => $data['material_no'],
                        'matl_nm' => $data['matl_nm'],
                        'box' => $box,
                        'total' => $data['total'],
                        'counter' => $qtyPerScan,
                        'line_c' => $data['line_c'],
                        'location' => $data['location_cd'],
                        'qty' => $data['qty'],
                        'iss_min_lot' => $data['iss_min_lot'],
                    ];

                    if (($iteration == $totalScanPerMaterial) && ($kelebihan > 0 || $data['total'] < 0)) {
                        $picking_qty = $data['total'] < 0 ? $qtyPerScan : $kelebihan;
                        abnormalMaterial::create([
                            'kit_no' => $this->po,
                            'surat_jalan' => $surat_jalan,
                            'pallet_no' => $paletCode,
                            'material_no' => $data['material_no'],
                            'picking_qty' => $picking_qty,
                            'trucking_id' => $data['trucking_id'],
                            'user_id' => $this->userId,
                            'status' => 1,
                            'box' => $box,
                            'line_c' => $data['line_c'],
                            'setup_by' => $data['setup_by'],
                            'locate' => $this->input_setup_by == 'PO MCS' ? $data['location_cd_ori'] : $data['location_cd'],
                        ]);
                        $sisaTerakhir = $qtyPerScan - $picking_qty;
                        if ($sisaTerakhir > 0) {
                            itemIn::create([
                                'pallet_no' => $paletCode,
                                'material_no' => $data['material_no'],
                                'picking_qty' => $sisaTerakhir,
                                'locate' => $this->input_setup_by == 'PO MCS' ? $data['location_cd_ori'] : $data['location_cd'],
                                'trucking_id' => $data['trucking_id'],
                                'kit_no' => $this->po,
                                'surat_jalan' => $surat_jalan,
                                'user_id' => $this->userId,
                                'line_c' => $data['line_c'],
                                'box' => $box,
                                'setup_by' => $data['setup_by'],
                            ]);
                        }
                    } else {
                        itemIn::create([
                            'pallet_no' => $paletCode,
                            'material_no' => $data['material_no'],
                            'picking_qty' => $qtyPerScan,
                            'locate' => $this->input_setup_by == 'PO MCS' ? $data['location_cd_ori'] : $data['location_cd'],
                            'trucking_id' => $data['trucking_id'],
                            'kit_no' => $this->po,
                            'surat_jalan' => $surat_jalan,
                            'user_id' => $this->userId,
                            'line_c' => $data['line_c'],
                            'box' => $box,
                            'setup_by' => $data['setup_by'],
                        ]);
                    }
                    $iteration++;
                }
            }
        }

        // update qr
        DB::table('WH_rcv_QRHistory')
            ->where('user_id', $this->userId)
            ->where('palet_iwpi', null)
            ->when($location == 'ASSY', function ($q) use ($line_code) {
                $q->where('line_code', $line_code);
            })
            ->where('PO', $po)
            ->update([
                'status' => 1,
                'surat_jalan' => $surat_jalan,
                'palet_iwpi' => $paletCode,
            ]);

        // JIKA ASSY
        if ($location == 'ASSY') {
            $dataPaletRegister = PaletRegister::selectRaw('palet_no,issue_date,line_c')->where('is_done', 1)->where('palet_no_iwpi', $paletCode)->first();

            // insert
            if ($dataPaletRegister) {
                $generator = new BarcodeGeneratorPNG();
                $barcode = $generator->getBarcode($dataPaletRegister->palet_no, $generator::TYPE_CODE_128);
                Storage::put('public/barcodes/' . $dataPaletRegister->palet_no . '.png', $barcode);

                $dataPrint = [
                    'data' => collect($sorted),
                    'palet_no' => $dataPaletRegister->palet_no,
                    'issue_date' => $dataPaletRegister->issue_date,
                    'line_c' => $dataPaletRegister->line_c
                ];
                $this->dispatch('confirmation');

                // $this->dispatch('alert', ['title' => 'Succes', 'time' => 5000, 'icon' => 'succes', 'text' => 'ASSY material saved succesfully']);
                return Excel::download(new ReceivingSupplierReport($dataPrint), 'Receiving ASSY_' . $dataPrint['palet_no'] . '_' . date('YmdHis') . '.pdf', \Maatwebsite\Excel\Excel::MPDF);
            } else {
                $this->dispatch('confirmation');
                $dataPrint = [
                    'data' => collect($sorted),
                    'issue_date' => '-',
                    'line_c' => '-',
                    'palet_no' => '-'
                ];
                return Excel::download(new ReceivingSupplierReport($dataPrint), 'Receiving ASSY_' . date('YmdHis') . '.pdf', \Maatwebsite\Excel\Excel::MPDF);

                // return $this->dispatch('alert', ['title' => 'Succes', 'time' => 5000, 'icon' => 'succes', 'text' => 'material saved succesfully without palet']);
                // $this->resetPage();
            }
        }
    }

    public function render()
    {
        return view('livewire.receiving-supplier-v2');
    }
}
