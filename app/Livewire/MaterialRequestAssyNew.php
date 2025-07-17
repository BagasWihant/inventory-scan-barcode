<?php

namespace App\Livewire;

use Livewire\Component;

class MaterialRequestAssyNew extends Component
{
    
    public $type;
    public $requestQty;
    public $materialNo;
    public $searchMaterialNo;
    public $resultSearchMaterial = [];
    public $selectedData = [];
    public $materialRequest = [];
    public $editedUser = false;
    public $userRequest;
    public $userRequestDisable = false;
    public $variablePage = [
        'materialRequestNW' => 0,
        'materialRequestWR' => 0,
        'timeNow' => null,
    ];
    public $totalRequest = [
        'qty' => 0,
        'data' => []
    ];
    public $transactionNo = [
        'nw' => null,
        'wr' => null,
    ];
    public $line_c;
    public $date;
    public $listLine = [];
    public $listMaterialNo = [];

    protected $listeners = ['editQty'];
    public function render()
    {
        return view('livewire.material-request-assy-new');
    }
}
