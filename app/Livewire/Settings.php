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

    public function getData($name)
    {
        switch ($name) {
            case 'allow-add-material':
                $users = DB::table('users')->select(['id', 'username'])->where('Admin', 0)->get();
                $config = Cache::rememberForever('ADD_MAT_BTN_RECEIVE', function () {
                    return DB::table('WH_config')->where('config', 'ADD_MAT_BTN_RECEIVE')->first();
                });


                $configData = json_decode($config->value ?? '{}', true);
                $merged = $users->map(function ($user) use ($configData) {
                    return [
                        'i' => $user->id,
                        'u' => $user->username,
                        's' => $configData[$user->id] ?? 0
                    ];
                });

                return $merged;
                break;

            default:
                # code...
                break;
        }
    }

    public function saveData($data, $name) {
        switch ($name) {
            case 'allow-add-material':
                try {
                    Cache::forget('ADD_MAT_BTN_RECEIVE');
                    DB::table('WH_config')->where('config', 'ADD_MAT_BTN_RECEIVE')->update(['value' => json_encode($data)]);
                    return 'success';
                } catch (\Exception $th) {
                    return 'error';
                }
                break;
            
            default:
                # code...
                break;
        }
    }

    public function mount()
    {
        if(auth()->user()->Admin != 1) {
            abort(499, 'Anda bukan Admin');
        }
        $this->allowAddMaterialInReceiving = $this->configAddMaterial();
    }

    public function allowAddMaterialInReceivingChange($val)
    {
        $configKey = 'ADD_MAT_BTN_RECEIVE';

        DB::table('WH_config')->where('config', $configKey)->update(['value' => $val]);
       
    }
    public function render()
    {
        return view('livewire.settings');
    }
}
