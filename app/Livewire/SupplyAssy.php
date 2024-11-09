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

        $this->line = DB::table('palet_registers')
            ->where('issue_date', $this->date)
            ->where('palet_no', $value)
            ->select('line_c')->first()?->line_c;
        $this->dataTable = DB::table('palet_register_details')->where('palet_no', $value)->get();
    }

    private function setMaterialNo($value)
    {
        $this->dataTable = DB::table('palet_register_details')->where('palet_no', $this->noPallet)->where('material_no', $value)->get();
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
