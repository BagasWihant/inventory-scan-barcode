<?php

namespace App\Exports\Approval;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class Generate implements FromView
{

    public $data;
    public function __construct($data)
    {
        $newData = [
            'Section' => $data['section'],
            'Position' => $data['position'],
            'Qty' => $data['qty'],
            'Reason' => $data['reason'],
            'Detail_Subsitution' => $data['reason']
        ];

        $this->data = $newData;
    }

    public function view(): View
    {
        return view('templates.pdf.approval-generate', [
            'data' => $this->data,
            'dat1' => '1'
        ]);
    }
}
