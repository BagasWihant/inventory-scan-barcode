<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Settings extends Component
{
    public $allowAddMaterialInReceiving = null;

    private function configAddMaterial()
    {
        $configKey = 'ADD_MAT_BTN_RECEIVE';

        $row = DB::table('WH_config')->where('config', $configKey)->first();

        if (!$row) {
            DB::table('WH_config')->updateOrInsert(
                ['config' => $configKey],
                ['value' => '0']
            );
            $newValue = '0';
        } else {
            $newValue = $row->value;
        }
        return $newValue;
    }

    public function mount()
    {
        $this->allowAddMaterialInReceiving = $this->configAddMaterial();
    }

    public function allowAddMaterialInReceivingChange($val)  {
        $configKey = 'ADD_MAT_BTN_RECEIVE';

        DB::table('WH_config')->where('config', $configKey)->update(['value' => $val]);
        Cache::forget($configKey);
        
    }
    public function render()
    {
        return view('livewire.settings');
    }
}
