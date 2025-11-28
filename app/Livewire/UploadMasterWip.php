<?php

namespace App\Livewire;

use App\Imports\ImportUploadAssy;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class UploadMasterWip extends Component
{
    use WithFileUploads;

    public $file;

    public function uploadFile()
    {
        $ex    = Excel::toArray(new ImportUploadAssy(), $this->file);
        $clean = array_filter(
            $ex[0],
            function ($row) {
                foreach ($row as $v) {
                    if (trim((string) $v) !== '')
                        return true;
                }
                return false;
            }
        );
        foreach ($clean as $key => $value) {
            // ubah tgl excel
            if ($value['model'] === null || $value['dc'] === null || $value['line'] === null || $value['cust'] === null || $value['tanggal'] === null || $value['qty'] === null)
                continue;
            $unix = ($value['tanggal'] - 25569) * 86400;
            $date = date('Y-m-d', $unix);
            
            DB::table('master_wip')->insert([
                'model'      => $value['model'],
                'dc'         => $value['dc'],
                'line'       => $value['line'],
                'cust'       => $value['cust'],
                'tanggal'    => $date,
                'qty'        => $value['qty'],
                'created_by' => auth()->user()->nik,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return 'success';
    }

    public function render()
    {
        return view('livewire.upload-master-wip');
    }
}
