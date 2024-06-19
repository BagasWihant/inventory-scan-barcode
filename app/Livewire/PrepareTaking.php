<?php

namespace App\Livewire;

use App\Models\MenuOptions;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PrepareTaking extends Component
{
    public $statusActive, $date, $id,$canOpen,$user_id, $data;

    
   
    public function changeStatusActive()
    {
    }

    public function open()
    {
        $data = MenuOptions::find($this->data->id);
        $this->date = $this->date ?? now();
        $data->update([
            'status' => 0,
            'date_end' => date('Y-m-d H:i:s', strtotime($this->date))
        ]);

    }
    public function lock()
    {
        $this->date = $this->date ?? now();
        MenuOptions::create([
            'status' => 1,
            'user_id' => $this->user_id,            
            'date_start' => date('Y-m-d H:i:s', strtotime($this->date))
        ]);
    }

    public function render()
    {
        $this->user_id = auth()->user()->id;
        $data = MenuOptions::where('status', 1);
        $this->canOpen =$data->exists();
        
        if ($this->canOpen) {
            $this->data = $data->first();
        }
        
        return view('livewire.prepare-taking');
    }
}
