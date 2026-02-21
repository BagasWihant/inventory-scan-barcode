<?php

namespace App\Livewire\Rak;

use App\Models\Rak;
use App\Models\RakMaterial;
use App\Models\RakTransaksi;
use Livewire\Component;
use Livewire\WithPagination;
use DB;

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
            'kode' => $this->kode ?: '-',
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
        try {
            DB::beginTransaction();
            RakTransaksi::create([
                'rak_id' => $rakData['id'],
                'material_id' => $materialData['id'],
                'qty' => $form['qty'],
                'user_id' => 0,
                'dihapus' => 0,
                'stat' => $stat,
                'params' => ''
            ]);

            $materialDB = RakMaterial::findOrFail($materialData['id']);

            if ($stat === 'i') {
                $materialDB->stok += (int) $form['qty'];
            } elseif ($stat === 'o') {
                if ($materialDB->stok < $form['qty']) {
                    throw new \Exception('Stok tidak mencukupi!');
                }
                $materialDB->stok -= (int) $form['qty'];
            }

            $materialDB->save();

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function getStats()
    {
        $allMaterials = RakMaterial::where('rak_id', $this->rakId)->get();

        return [
            'total_material' => $allMaterials->count(),
            'total_stok' => $allMaterials->sum('stok'),
            'stok_rendah' => $allMaterials->where('stok', '<=', 5)->count(),
        ];
    }

    public function getMaterialsPage($page = 1)
    {
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $query = RakMaterial::where('rak_id', $this->rakId)->orderBy('nama', 'asc');

        $total = $query->count();
        $paginated = $query->offset($offset)->limit($perPage)->get();

        return [
            'items' => $paginated,
            'total_pages' => (int) ceil($total / $perPage),
            'current_page' => (int) $page,
            'history_count' => $this->historyCount(),
            'stats' => $this->getStats(),
        ];
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
            'stats' => $this->getStats(),
        ]);
    }
}
