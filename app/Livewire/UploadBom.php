<?php

namespace App\Livewire;

use App\Imports\ImportBom;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class UploadBom extends Component
{
    use WithFileUploads;

    public $importFile;
    public $product_no;
    public $dc;

    public function render()
    {
        return view('livewire.upload-bom');
    }

    public function searchDropdown($qey)
    {
        return DB::table('db_bantu.dbo.bom')
            ->selectRaw("
            REPLACE(CONCAT(product_no,'_',dc), ' ', '') AS product_no,
            REPLACE(CONCAT(product_no,'_',dc), ' ', '') AS id
        ")
            ->whereRaw("REPLACE(CONCAT(product_no,'_',dc), ' ', '') LIKE ?", ["%{$qey}%"])
            ->groupBy('product_no', 'dc')
            ->limit(5)
            ->get();
    }


    public function selectDropdown($item)
    {
        $no = explode('_', $item['id']);
        $this->product_no = $no[0];
        $this->dc = $no[1];
        $this->dispatch(
            'product-model-selected',
            productNo: $this->product_no
        );
    }

    public function updatedImportFile()
    {
        if ($this->importFile) {
            $this->processFile();
        }
    }
    private function processFile()
    {
        // Validasi
        $this->validate([
            'importFile' => 'required|mimes:csv,xlsx,xls|max:10240'
        ]);

        $dataYangAda = DB::table('db_bantu.dbo.bom')
            ->selectRaw("REPLACE(material_no, ' ', '') as material_no")
            ->whereRaw("product_no = ?", [$this->product_no])
            ->whereRaw("dc = ?", [$this->dc])
            ->groupBy('material_no')
            ->pluck('material_no')
            ->all();
        // Process file
        $excel = new ImportBom($dataYangAda, $this->product_no, $this->dc);
        Excel::import($excel, $this->importFile);

        $this->dispatch(
            'excel-data-loaded',
            headers: ['product_no', 'dc', 'material_no', 'bom_qty', 'created_at', 'updated_at', 'status'],
            rows: $excel->getData()
        );
    }

    public function saveBom($data)
    {
        foreach ($data as $d) {
            sleep(1);
            DB::table('db_bantu.dbo.bom')
                ->insert([
                    'product_no' => $d['product_no'],
                    'dc' => $d['dc'],
                    'material_no' => $d['material_no'],
                    'bom_qty' => $d['bom_qty'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    'status' => 1
                ]);
        }
    }
}
