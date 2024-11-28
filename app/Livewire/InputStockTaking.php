<?php

namespace App\Livewire;

use App\Models\MenuOptions;
use App\Models\StockTaking;
use Livewire\Component;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;


class InputStockTaking extends Component
{
    public $listMaterial = [];
    public $showSearch = false;
    public $materialCode, $location, $qty, $userID, $stoID, $hitung, $data, $update = false;
    public $idStockTaking = null;


    public function mount()
    {
        $this->userID = auth()->user()->id;


        // $this->listMaterial = DB::table('material_mst')->selectRaw('Distinct(matl_no) as material_no')->pluck('material_no')->all()

        $this->stoID = MenuOptions::where('status', '1')
            // ->where('user_id', auth()->user()->id)
            ->first()->id;
    }

    public function updated($prop, $value)
    {
        switch ($prop) {
            case 'materialCode':
                if (strlen($value) >= 3) $this->showSearch = true;
                else $this->showSearch = false;

                if (strtolower(substr($value, 0, 1)) == "c" && strlen($value) > 15) {
                    $tempSplit = explode(' ', $value);

                    if (strtolower(substr($value, 0, 1)) == "p") {
                        $value = substr($value, 1, 15);
                    } else {
                        $value = substr($tempSplit[0], 23, 15);
                    }

                    $this->showSearch = false;
                    $this->materialCode = $value;
                }

                $matMst = DB::table('material_mst')
                    ->selectRaw('matl_no,loc_cd')->limit(10)
                    ->where('matl_no', 'like', "%$value%")->get();
                $this->listMaterial = $matMst->pluck('matl_no')->all();
                if (count($this->listMaterial) == 1) {
                    $this->showSearch = false;
                    $this->location = $matMst[0]->loc_cd;
                    $this->dispatch('qtyFocus');
                }
                break;

            default:
                # code...
                break;
        }
    }

    // public function editBtn() {}
    #[On('materialChange')]
    public function materialChange($id = null)
    {
        if ($this->materialCode) {
            // if ($id) {
            //     $this->idStockTaking = $id;
            //     $this->update = true;
            //     $query = DB::table('stock_takings')
            //         ->selectRaw('loc,qty,hitung')
            //         ->where('id', $id)
            //         ->get();
            //     $dataSelected = $query->first();
            //     $this->location = $dataSelected->loc;
            //     $this->qty = $dataSelected->qty;
            //     $this->hitung = $dataSelected->hitung;
            // } else {
            //     $this->update = false;
            //     $query = DB::table('material_mst')->selectRaw('loc_cd as locate')
            //         ->where('matl_no', $this->materialCode);
            //     $dataSelected = $query->first();
            //     if ($dataSelected) {
            //         $this->location = $dataSelected->locate;
            //         $this->qty = 0;
            //     } else {
            //         $this->location = null;
            //         $this->qty = null;
            //     }
            // }

            if (strtolower(substr($this->materialCode, 0, 1)) == "c") {
                $tempSplit = explode(' ', $this->materialCode);

                if (strtolower(substr($this->materialCode, 0, 1)) == "p") {
                    $this->materialCode = substr($this->materialCode, 1, 15);
                } else {
                    $this->materialCode = substr($tempSplit[0], 23, 15);
                }
            }
        }
    }

    public function save()
    {
        if ($this->materialCode && $this->hitung) {
            if ($this->update) {

                StockTaking::where('id', $this->idStockTaking)->update([
                    'loc' => $this->location,
                    'qty' => $this->qty
                ]);

                $this->cancel();
                $this->dispatch('notification', ['title' => 'Update success', 'icon' => 'success']);
                return;
            } else {
                $checkDouble = DB::table('stock_takings')
                    ->selectRaw('user_id,hitung')
                    ->where('material_no', $this->materialCode)
                    ->where('sto_id', $this->stoID)
                    ->first();
                dump($this->stoID, $this->materialCode, $checkDouble);

                if ($checkDouble && ($checkDouble->user_id != $this->userID)) {
                    $this->dispatch('popup', ['title' => 'Material already taken by other user']);
                    return;
                }
                if ($checkDouble && ($checkDouble->hitung == $this->hitung) ) {
                    $this->dispatch('popup', ['title' => 'Material already taken ']);
                    return;
                }
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
                return;
            }
        }
        $this->dispatch('popup', ['title' => 'Please fill all fields']);
    }

    #[On('deleteMat')]
    public function deleteMat($id = null)
    {
        StockTaking::where('id', $id)->delete();
    }
    public function delBtn($id = null)
    {
        $this->dispatch('popup', ['id' => $id]);
    }

    public function cancel()
    {
        $this->materialCode = null;
        $this->location = null;
        $this->qty = null;
        $this->hitung = null;
        $this->update = false;
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
