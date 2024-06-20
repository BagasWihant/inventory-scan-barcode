<?php

namespace App\Livewire;

use Livewire\Component;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

use function Laravel\Prompts\select;

class InputStockTaking extends Component
{
    public $listMaterial, $materialCode, $location, $qty;

    #[On('materialChange')]
    public function materialChange()
    {

        $query = DB::table('material_in_stock')
            ->where('material_no', $this->materialCode);
        $dataSelected = $query->first();
        $this->location = $dataSelected->locate;
        $this->qty = $dataSelected->picking_qty;
    }

    public function save()
    {
        if ($this->materialCode) {

            // DB::table('material_in_stock')
            //     ->where('material_no', $this->materialCode)
            //     ->update([
            //         'picking_qty' => $this->qty
            //     ]);
            $up = DB::table('material_mst')
                ->where('matl_no', $this->materialCode);

            $up->update([
                'qty_IN' => $this->qty
            ]);

            $this->cancel();
            // dd($up);
            return;
        }
        dd('silahkan pilih material code setelah cancel');
    }

    public function cancel()
    {
        $this->materialCode = null;
        $this->location = null;
        $this->qty = null;
    }

    public function render()
    {
        $this->listMaterial = DB::table('material_in_stock')
            ->selectRaw('Distinct(material_no)')->pluck('material_no')->all();
        return view('livewire.input-stock-taking');
    }
}
