<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SupplyAssy extends Component
{
    public $date, $line, $partial = false, $noPallet, $materialNo;
    public $topInputLock = false, $btnSetup = false, $inputMaterialNo = false, $optionPalletShow = true, $optionMaterialShow = true;
    public $lines = [], $dataTable = [];
    public $collectPallet = [], $collectMaterial = [];

    public function updating($prop, $value)
    {
        // dd($prop,$value);
        switch ($prop) {
            case 'partial':
                $value ? $this->inputMaterialNo = true : $this->inputMaterialNo = false;
                $this->dataTable = [];
                break;
            case 'date':
                $this->lines = DB::table('palet_registers')->where('issue_date', $value)->select('line_c')->groupBy('line_c')->get();
                break;


            case 'noPallet':
                // $arrNoPalet = collect($this->collectPallet)->toArray();
                $this->collectPallet = DB::table('palet_registers')
                    ->where('issue_date', $this->date)
                    ->where('line_c', $this->line)
                    ->where('palet_no', 'like', "%$value%")
                    ->select('palet_no')->get()->pluck('palet_no');
                if (!$this->partial) {
                    $this->dataTable = DB::table('palet_register_details')->where('palet_no', $value)->get();
                }



                break;
            case 'materialNo':
                $this->optionMaterialShow = true;
                if ($this->noPallet && $this->partial) {
                    $this->collectMaterial = DB::table('palet_register_details')
                        ->where('palet_no', $this->noPallet)
                        ->where('material_no', 'like', "%$value%")
                        ->select('material_no')->get()->pluck('material_no');
                } else {
                    $this->dispatch('notification', ['title' => "Mohon isi No Palet", 'icon' => 'error']);
                }

                break;

            default:
                # code...
                break;
        }
    }
    public function setMaterialNo($value)
    {
        $this->optionMaterialShow = false;
        $this->materialNo = $value;
        $this->dataTable = DB::table('palet_register_details')->where('palet_no', $this->noPallet)->where('material_no', $value)->get();
    }

    public function setup()
    {
        $this->topInputLock = true;
        $this->btnSetup = true;

        $this->collectPallet = DB::table('palet_registers')->where('issue_date', $this->date)->where('line_c', $this->line)->select('palet_no')->get()->pluck('palet_no');
    }

    public function setupDone()
    {
        dd($this->partial);
    }

    public function batal()
    {
        $this->topInputLock = false;
        $this->btnSetup = false;
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
