<?php

namespace App\Livewire\Menu;

use App\Imports\ImportUploadAssy;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class AssyUpload extends Component
{
    use WithFileUploads;

    public $file;
    public $periode;

    protected $rules = [
        'file' => 'required|mimes:xls,xlsx|max:20480',  // max 20MB
    ];

    public function uploadFile($data)
    {
        $bulan = $data[0];
        $tahun = $data[1];
        $this->validate();

        $ex = Excel::toArray(new ImportUploadAssy(), $this->file);

        foreach ($ex[0] as $row) {
            $tanggal = [];

            for ($i = 1; $i <= 31; $i++) {
                if (array_key_exists($i, $row)) {
                    $tanggal[$i] = $row[$i];
                    unset($row[$i]);
                }
            }

            foreach ($tanggal as $k => $value) {
                DB::connection('it')->statement(
                    "EXEC sp_Assy_daily_Import_Plan ?, ?, ?, ?, ?, ?, ?, ?, ?, ?",
                    [
                        $tahun . $bulan,  // @periode varchar(20),		-- 202411
                        $row['line_cd'],  // @line_cd varchar(20),		-- xhn1
                        $row['partno'],   // @product_no varchar(100),	-- 32110-6HA-J700
                        $row['dc'],       // @dc varchar(20),			-- 00-01-02
                        $row['cust'],     // @customer varchar(50),		-- HPM
                        $row['smh'],      // @smh decimal(10,6),			-- smh
                        $k,               // @tanggal varchar(20),		-- 21
                        $value,           // @qty varchar(20),			-- 60
                        $row['remain'],   // @remain numeric,			-- 0
                        '',               // @user varchar(50)			-- demo
                    ]
                );
            }
        }

        $this->dispatch(
            'done-upload',
            rows: $ex[0]
        );
    }

    public function render()
    {
        return view('livewire.menu.assy-upload');
    }
}
