<?php

namespace App\Livewire;

use App\Models\MenuOptions;
use App\Models\StockTaking;
use Livewire\Component;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

use function Laravel\Prompts\select;

class InputStockTaking extends Component
{
    public $listMaterial, $materialCode, $location, $qty, $userID, $stoID, $hitung, $data;


    public function mount()
    {
        $this->userID = auth()->user()->id;


        $this->listMaterial = DB::table('material_in_stock')
            ->selectRaw('Distinct(material_no)')->pluck('material_no')->all();


        $this->stoID = MenuOptions::where('status', '1')
            ->where('user_id', auth()->user()->id)->first()->id;
    }
    #[On('materialChange')]
    public function materialChange()
    {
        if ($this->materialCode) {
            $query = DB::table('material_in_stock')
                ->where('material_no', $this->materialCode);
            $dataSelected = $query->first();
            $this->location = $dataSelected->locate;
            $this->qty = $dataSelected->picking_qty;
        }
    }

    public function save()
    {
        if ($this->materialCode && $this->hitung) {
            // dd($this->hitung);
            StockTaking::create([
                'sto_id' => $this->stoID,
                'user_id' => $this->userID,
                'material_no' => $this->materialCode,
                'hitung' => $this->hitung,
                'loc' => $this->location,
                'qty' => $this->qty
            ]);
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
        $this->dispatch('reset');
    }

    public function render()
    {
        $this->data = DB::table('stock_takings')
            ->selectRaw('sto_id,material_no,loc,qty,hitung')
            ->where('user_id', $this->userID)
            ->where('sto_id', $this->stoID)
            ->get();
        return view('livewire.input-stock-taking');
    }
}
