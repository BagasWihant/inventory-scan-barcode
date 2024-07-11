<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\MaterialRegistrasi as ModelsMaterialRegistrasi;

class MaterialRegistrasi extends Component
{
    use WithPagination;

    // public $listMaterialComboBox = [];
    public $searchMaterial, $material;
    public $materialDisable = false;

    public function render()
    {
        return view('livewire.material-registrasi', [
            'listMaterialAdded' => ModelsMaterialRegistrasi::paginate(20),
            'listMaterialComboBox' =>  DB::table('material_mst')
                ->select('matl_no')
                ->groupBy('matl_no')->get()
        ]);
    }

    // public function materialType()
    // {
    //     if (strlen($this->searchMaterial) >= 1) {
    //         $this->listMaterialComboBox = DB::table('material_mst')
    //             ->select('matl_no')
    //             ->where('matl_no', 'like', "%$this->searchMaterial%")
    //             ->groupBy('matl_no')->limit(20)->get();
    //     }
    // }

    public function chooseMaterial($mat)
    {
        $this->material = $mat;
        $this->searchMaterial = $mat;
        $this->materialDisable = true;
    }

    public function addMaterial()
    {
        $exists = ModelsMaterialRegistrasi::where('material_no', $this->material)->exists();
        $this->searchMaterial = null;
        $this->materialDisable = false;

        if ($exists) return $this->dispatch('notification', ['icon' => 'error', 'title' => 'Material already added']);

        ModelsMaterialRegistrasi::create([
            'material_no' => $this->material
        ]);
        $this->material = null;
        return $this->dispatch('notification', ['icon' => 'success', 'title' => 'Successfully added']);
    }
    
    public function deleteMaterial($id=null){
        ModelsMaterialRegistrasi::where('id', $id)->delete();
        return $this->dispatch('notification', ['icon' => 'success', 'title' => 'Successfully deleted']);
    }
}
