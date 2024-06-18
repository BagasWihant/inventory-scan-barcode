<?php

namespace App\Livewire;

use App\Models\MenuOptions;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PrepareTaking extends Component
{
    public $statusActive, $date, $id,$listUser,$userSelected;

    public function mount()
    {
        $this->listUser = DB::table('users')->select('id','username')
        ->where('Role_ID','!=','3')
        ->where('Admin','!=','1')
        ->get();
        
        $data = MenuOptions::first();
        if ($data) {

            $this->statusActive = $data->status == 1 ? true : false;
            $this->id = $data->id;
        }
    }

    public function changeStatusActive()
    {
    }

    public function open()
    {
        // $data = MenuOptions::find($this->id);
        // $data->update([
        //     'status' => $this->statusActive ? 1 : 0,
        //     'date_start' => date('d-m-Y H:i:s', strtotime($this->date))
        // ]);
    }
    public function lock()
    {
        $collect = collect($this->userSelected);
        $userID = $collect->implode(',');

        MenuOptions::create([
            'status' => 0,
            'user_id' => "$userID",            
            'date_start' => date('Y-m-d', strtotime($this->date))
        ]);
        $this->userSelected = null;
        // $data->update([
        //     'status' => $this->statusActive ? 1 : 0,
        //     'date_start' => date('d-m-Y H:i:s', strtotime($this->date))
        // ]);
    }

    public function render()
    {

        return view('livewire.prepare-taking');
    }
}
