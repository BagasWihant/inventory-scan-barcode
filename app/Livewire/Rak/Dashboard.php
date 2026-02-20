<?php

namespace App\Livewire\Rak;

use App\Models\Rak;
use App\Models\RakMaterial;
use App\Models\RakTransaksi;
use Livewire\Component;

class Dashboard extends Component
{
    public array $listRak = [];
    public array $total = [];

    public function mount(): void
    {
        $this->loadRak();
    }

    private function loadRak(): void
    {
        $raks = Rak::withCount('materials')
            ->withSum('materials', 'stok')
            ->get();

        $this->listRak = $raks->map(fn($r) => [
            'id' => $r->id,
            'nama_rak' => $r->name,
            'total_material' => $r->materials_count,
            'total_stok' => $r->materials_sum_stok ?? 0,
        ])->toArray();

        $totalIn = RakTransaksi::where('stat', 'i')->where('dihapus', 0)->sum('qty');
        $totalOut = RakTransaksi::where('stat', 'o')->where('dihapus', 0)->sum('qty');
        $history = RakTransaksi::where('dihapus', 0)
            ->whereDate('created_at', today())
            ->count();
        $totalAll = $totalIn + $totalOut;

        $this->total = [
            'rak' => $raks->count(),
            'in' => $totalIn,
            'out' => $totalOut,
            'all' => $totalAll,
            'history' => $history,
        ];
    }

    public function storeRak(string $nama): ?array
    {
        $nama = trim($nama);

        if (empty($nama))
            return null;

        $rak = Rak::create(['name' => $nama]);

        return [
            'id' => $rak->id,
            'nama_rak' => $rak->name,
        ];
    }

    public function destroyRak(int $id): bool
    {
        $rak = Rak::find($id);

        if (!$rak)
            return false;

        $rak->delete();
        return true;
    }

    public function storeMaterial(int $rakId, string $nama, string $kode = '', int $stok = 0, string $satuan = 'PCS'): ?array
    {
        if (empty(trim($nama)) || !$rakId)
            return null;

        $material = RakMaterial::create([
            'rak_id' => $rakId,
            'nama' => trim($nama),
            'kode' => trim($kode) ?: null,
            'stok' => $stok,
            'satuan' => $satuan,
        ]);

        return [
            'id' => $material->id,
            'nama' => $material->nama,
            'kode' => $material->kode ?? '-',
            'stok' => $material->stok,
            'satuan' => $material->satuan,
        ];
    }

    public function render()
    {
        return view('livewire.rak.dashboard');
    }
}
