<?php

namespace App\Livewire\Menu;

use Livewire\Component;

class StandarKerja extends Component
{
    public $urlPdf = null, $active;

    public function showPdf($type) {
        switch ($type) {
            case '1':
                $this->active = 1;
                $this->urlPdf = asset('Sertifikat-AIPT-STMIK-Amikom-Surakarta.pdf');
                break;
                
            case '2':
                $this->active = 1;
                $this->urlPdf = asset('Bagas Wihantoro.pdf');
                break;
                    
            default:
                $this->active = 99;
                $this->urlPdf = null;
                break;
        }    
    }

    public function render()
    {
        return view('livewire.menu.standar-kerja');
    }
}
