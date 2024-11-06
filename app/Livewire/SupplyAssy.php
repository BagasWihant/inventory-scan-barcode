<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SupplyAssy extends Component
{
    public $date, $line, $partial = false, $noPallet, $materialNo;
    public $topInputLock = false, $btnSetup = false, $inputMaterialNo = false;
    public $lines = [], $dataTable = [];
    public $collectPallet = [];

    public function updating($prop, $value)
    {
        // dd($prop,$value);
        switch ($prop) {
            case 'partial':
                $value ? $this->inputMaterialNo = true : $this->inputMaterialNo = false;
                break;
            case 'date':
                $this->lines = DB::table('palet_registers')->where('issue_date', $value)->select('line_c')->groupBy('line_c')->get();
                break;


            case 'noPallet':
                $arrNoPalet = collect($this->collectPallet)->toArray();

                if (in_array($value, $arrNoPalet)) {
                    $this->dataTable = DB::table('palet_register_details')->where('palet_no', $value)->get();
                } else {
                    dump('tidak');
                }
                break;
            case 'materialNo':
                if($this->noPallet){
                    $this->dataTable = DB::table('palet_register_details')->where('palet_no', $this->noPallet)->where('material_no',$value)->get();
                }else{
                    dump('material palet belum di isi');
                }

                break;

            default:
                # code...
                break;
        }
    }
    public function setup()
    {
        $this->topInputLock = true;
        $this->btnSetup = true;

        $this->collectPallet = DB::table('palet_registers')->where('issue_date', $this->date)->where('line_c', $this->line)->select('palet_no')->get()->pluck('palet_no');
        // dump($this->collectPallet);
    }

    public function setupDone()
    {
        dd($this->partial);
    }

    public function batal()
    {
        $this->topInputLock = false;
        $this->btnSetup = false;
    }

    public function render()
    {
        return view('livewire.supply-assy');
    }
}
