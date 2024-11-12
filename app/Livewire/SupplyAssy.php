<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SupplyAssy extends Component
{
    public $date, $line, $partial = false, $noPallet, $materialNo;
    public $topInputLock = false, $btnSetup = false, $inputMaterialNo = false, $optionPalletShow = true, $optionMaterialShow = true, $btnSetupDone = true;
    public $lines = [], $dataTable = [];
    public $collectPallet = [], $collectMaterial = [];

    public function updating($prop, $value)
    {
        switch ($prop) {
            case 'partial':
                $this->dataTable = [];
                if ($value) {
                    $this->inputMaterialNo = true;
                    if ($this->materialNo) $this->setMaterialNo($this->materialNo);
                } else {
                    $this->inputMaterialNo = false;
                    if ($this->noPallet) $this->setPallet($this->noPallet);
                }
                break;

            case 'noPallet':
                $this->setPallet($value);
                break;
            case 'materialNo':
                $this->setMaterialNo($value);
                break;

            default:
                # code...
                break;
        }
    }

    private function setPallet($value)
    {

        $paletRegister = DB::table('palet_registers')
            ->where('issue_date', $this->date)
            ->where('palet_no', $value)
            ->select(['line_c', 'supply_date'])->first();

        if ($paletRegister) {
            if ($paletRegister->supply_date) {
                $this->btnSetupDone = false;
            }
        } else {
            $this->dataTable = [];
            $this->dispatch('notification', ['title' => "No Palet tidak ada", 'icon' => 'error']);
            return;
        }

        $this->line = $paletRegister?->line_c;
        $this->dataTable = DB::table('palet_register_details as p')->where('palet_no', $value)
        ->leftJoin('material_mst as m','p.material_no','=','m.matl_no')->select(['m.matl_nm','material_no','p.qty','qty_supply'])
        ->get();
    }

    private function setMaterialNo($value)
    {
        $this->dataTable = DB::table('palet_register_details as p')->where('palet_no', $this->noPallet)->where('material_no', $value)
        ->leftJoin('material_mst as m','p.material_no','=','m.matl_no')->select(['m.matl_nm','material_no','p.qty','qty_supply'])
        ->get();
    }

    public function setup()
    {
        $this->topInputLock = true;
        $this->btnSetup = true;

        $this->collectPallet = DB::table('palet_registers')
            ->where('issue_date', $this->date)
            ->select('palet_no')->get()->pluck('palet_no');
    }

    public function setupDone()
    {
        DB::beginTransaction();
        try {
            DB::table('palet_registers')
                ->where('palet_no', $this->noPallet)
                ->where('issue_date', $this->date)
                ->update([
                    'supply_date' => now(),
                    'status' => 1
                ]);

            DB::table('palet_register_details')
                ->where('palet_no', $this->noPallet)
                ->update(['qty_supply' => DB::raw('qty')]);

            $listData = DB::table('palet_register_details as p')->where('palet_no', $this->noPallet)
            ->leftJoin('material_mst as m','p.material_no','=','m.matl_no')->select(['m.matl_nm','material_no','p.qty','qty_supply'])
                ->get();
            $this->dataTable = $listData;

            $setupMstId = DB::table('Setup_mst')->insertGetId([
                'issue_dt' => now(),
                'line_cd' => $this->line,
                'created_at' => now(),
                'created_by' => auth()->user()->username
            ]);

            foreach ($listData as $data) {
                DB::table('Setup_dtl')->insert([
                    'setup_id' => $setupMstId,
                    'material_no' => $data->material_no,
                    'qty' => $data->qty,
                    'created_at' => now(),
                    'pallet_no' => $data->palet_no
                ]);
            }


            $this->btnSetupDone = false;

            DB::commit();
            $this->dispatch('notification', ['title' => "Supply Disimpan", 'icon' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('notification', ['title' => $e->getMessage(), 'icon' => 'error']);
        }
    }

    public function batal()
    {
        $this->topInputLock = false;
        $this->btnSetup = false;
        $this->btnSetupDone = true;
        $this->noPallet = null;
        $this->date = null;
        $this->line = null;
        $this->optionPalletShow = true;
        $this->optionMaterialShow = true;
        $this->dataTable = [];
    }

    public function render()
    {
        if (!$this->optionPalletShow && !$this->materialNo && $this->partial) {
            $this->collectMaterial = DB::table('palet_register_details')
                ->where('palet_no', $this->noPallet)
                ->select('material_no')->get()->pluck('material_no');
        }
        return view('livewire.supply-assy');
    }
}
