<?php

namespace App\Livewire;

use App\Models\MenuOptions;
use Livewire\Component;

class PrepareTaking extends Component
{
    public $statusActive,$date,$id;

    public function mount()
    {
        $data = MenuOptions::where('code','1')->first();
        $this->statusActive = $data->status == 1 ? true : false;
        $this->id = $data->id;
    }

    public function changeStatusActive() {
    }

    public function savedata(){
        $data = MenuOptions::find($this->id);
        $data->update([
            'status' => $this->statusActive ? 1 : 0,
            'date_start' => date('d-m-Y H:i:s', strtotime($this->date))
        ]);
    }

    public function render()
    {

        return view('livewire.prepare-taking');
    }
}
