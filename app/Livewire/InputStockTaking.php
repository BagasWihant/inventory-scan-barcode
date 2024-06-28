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
    public $listMaterial, $materialCode, $location, $qty, $userID, $stoID, $hitung, $data,$update=false;
    public $idStockTaking=null;


    public function mount()
    {
        $this->userID = auth()->user()->id;


        $this->listMaterial = DB::table('material_in_stock')->where('is_taking', '1')
            ->selectRaw('Distinct(material_no)')->pluck('material_no')->all();


        $this->stoID = MenuOptions::where('status', '1')
            ->where('user_id', auth()->user()->id)->first()->id;
    }
    #[On('materialChange')]
    public function materialChange($id = null)
    {
        if ($this->materialCode) {
            if ($id) {
                $this->idStockTaking = $id;
                $this->update = true;
                $query = DB::table('stock_takings')
                ->selectRaw('loc,qty,hitung')
                ->where('id', $id)
                ->get();
                $dataSelected = $query->first();
                $this->location = $dataSelected->loc;
                $this->qty = $dataSelected->qty;
                $this->hitung = $dataSelected->hitung;
            } else {
                $this->update = false;
                $query = DB::table('material_in_stock')->selectRaw('sum(picking_qty) as picking_qty, locate')
                    ->where('material_no', $this->materialCode)->groupBy('locate');
                $dataSelected = $query->first();
                if($dataSelected){
                    $this->location = $dataSelected->locate;
                    $this->qty = $dataSelected->picking_qty;
                }else{
                    $this->location = null;
                    $this->qty = null;
                }
            }
        }
    }

    public function save()
    {
        if ($this->materialCode && $this->hitung) {
            if($this->update){

                StockTaking::where('id', $this->idStockTaking)->update([
                    'loc' => $this->location,
                    'qty' => $this->qty
                ]);
                
                $this->cancel();
                // dd($up);
                return;
                
            }else{

                StockTaking::create([
                    'sto_id' => $this->stoID,
                    'user_id' => $this->userID,
                    'material_no' => $this->materialCode,
                    'hitung' => $this->hitung,
                    'loc' => $this->location,
                    'is_taking' => '0',
                    'qty' => $this->qty
                ]);
                
                $this->cancel();
                // dd($up);
                return;
            }
        }
        $this->dispatch('popup', ['title' => 'Please fill all fields']);

    }

    #[On('deleteMat')]
    public function deleteMat($id=null) {
        StockTaking::where('id', $id)->delete();
    }
    public function delBtn($id=null) {
        $this->dispatch('popup', ['id' => $id]);
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
            ->selectRaw('sto_id,material_no,loc,qty,hitung,id')
            ->where('user_id', $this->userID)
            ->where('sto_id', $this->stoID)
            ->get();
        return view('livewire.input-stock-taking');
    }
}
