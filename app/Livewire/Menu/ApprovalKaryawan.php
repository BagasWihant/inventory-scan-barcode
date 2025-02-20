<?php

namespace App\Livewire\Menu;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ApprovalKaryawan extends Component
{
    public $id;
    public $no;
    public $data;

    private function getstatus($str, $kondisi)
    {
        $map = [
            'O'  => 'Open',
            'OT'  => 'Tolak SPV',
            'AP' => 'Approved Purchasing',
            'APT' => 'Tolak Direksi',
            'AM' => 'Approved Management',
        ];

        return $kondisi == 'Long'
            ? ($map[$str] ?? 'Unknown')
            : (array_search($str, $map) ?: 'Unknown');
    }

    public function mount($recvData)
    {
        $this->id = $recvData['id'];
        $this->no = $recvData['no'];
        $req = DB::table(DB::raw('IP.dbo.PR_MASTER_PLAN'))->where("id", $recvData['id'])->where("no_plan", $recvData['no'])->first();
        $this->data = [
            'status' => $this->getstatus($req->status, 'Short'),
            'section' => $req->sec,
            'position' => 'Posisi',
            'qty' => 0,
            'reason' => 0,
            'docType' => 'Tipenya',
            'docNo' => $req->id . '-' . $req->no_plan,
            'docDate' => Carbon::parse($req->tanggal_plan)->format('d-m-Y'),
        ];
    }

    public function approve($status)
    {
        if(!in_array($status, ['O','AP'])){
            return false;
        }
        if ($status == 'O') $status = 'AP';
        elseif ($status == 'AP') $status = 'AM';


        DB::table(DB::raw('IP.dbo.PR_MASTER_PLAN'))
            ->where("id", $this->id)
            ->where("no_plan", $this->no)
            ->update([
                'status' => $this->getstatus($status, 'Long')
            ]);

            $this->dispatch('refresh');
    }
    public function reject($status, $message)
    {
        DB::table(DB::raw('IP.dbo.PR_MASTER_PLAN'))
            ->where("id", $this->id)
            ->where("no_plan", $this->no)
            ->update([
                'status' => $this->getstatus($status . 'T', 'Long'),
                'alasan_revisi' => $message
            ]);
            $this->dispatch('refresh');

    }
    public function render()
    {


        return view('livewire.menu.approval-karyawan');
    }
}
