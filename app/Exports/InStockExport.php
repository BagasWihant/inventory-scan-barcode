<?php

namespace App\Exports;

use App\Models\itemIn;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InStockExport implements FromView
{
    public $data;
    public function __construct($dt) {
        $this->data = $dt;
    }
    public function view() : View {
        return view('exports.in-stock',['data'=>$this->data]);
    }
}
