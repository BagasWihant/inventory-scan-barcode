<?php

namespace App\Livewire\Rak;

use App\Models\Rak;
use App\Models\RakMaterial;
use App\Models\RakTransaksi;
use Livewire\Component;
use Livewire\WithPagination;

class DetailRak extends Component
{
    use WithPagination;

    public Rak $rak;
    public int $rakId;
    
    // Form tambah material
    public string $nama = '';
    public string $kode = '';
    public int $stok = 0;
    public string $satuan = 'PCS';

    public function mount(int $rak_id): void
    {
        $this->rak = Rak::findOrFail($rak_id);
        $this->rakId = $rak_id;
    }

    public function historyCount()
    {
        return RakTransaksi::where('rak_id', $this->rakId)
            ->whereDate('created_at', today())
            ->count();
    }

    public function storeMaterial(): ?array
    {

        $material = RakMaterial::create([
            'rak_id' => $this->rakId,
            'nama' => $this->nama,
            'kode' => $this->kode ?: null,
            'stok' => $this->stok,
            'satuan' => $this->satuan,
        ]);

        $this->reset(['nama', 'kode', 'stok', 'satuan']);
        $this->satuan = 'PCS';

        return [
            'id' => $material->id,
            'nama' => $material->nama,
            'kode' => $material->kode ?? '-',
            'stok' => $material->stok,
            'satuan' => $material->satuan,
        ];
    }

    public function hapusMaterial(int $id): bool
    {
        $material = RakMaterial::find($id);
        if (!$material)
            return false;

        $material->delete();
        return true;
    }

    public function addTransaksi($rakData, $materialData, $form)
    {
        $stat = substr($form['tipe'], 0, 1);
        RakTransaksi::create([
            'rak_id' => $rakData['id'],
            'material_id' => $materialData['id'],
            'qty' => $form['qty'],
            'user_id' => 0,
            'dihapus' => 0,
            'stat' => $stat,
            'params' => ''
        ]);
    }

    public function render()
    {
        $materials = RakMaterial::where('rak_id', $this->rakId)
            ->orderBy('nama')
            ->get()
            ->toArray();
        $historyCount = $this->historyCount();

        return view('livewire.rak.detail-rak', [
            'materials' => $materials,
            'historyCount' => $historyCount,
        ]);
    }
}
