<?php

namespace App\Livewire\Rak;

use App\Models\Rak;
use App\Models\RakTransaksi;
use Livewire\Component;
use Livewire\WithPagination;
use Cache;

class History extends Component
{
    use WithPagination;

    public $rakInfo;
    public ?int $rakId = null;  
    public string $search = '';
    public string $filterTipe = '';  
    public string $filterDate = '';  
    public function mount(?int $rak_id = null)
    {
        $this->rakId = $rak_id;
        $this->rakInfo = Rak::find($this->rakId);
        $this->filterDate = today()->toDateString();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterTipe()
    {
        $this->resetPage();
    }

    public function updatingFilterDate()
    {
        $this->resetPage();
    }

    public function getHistoryProperty()
    {
        // unique key
        $cacheKey = 'history_page_' . $this->getPage()
            . '_' . $this->rakId
            . '_' . $this->search
            . '_' . $this->filterTipe
            . '_' . $this->filterDate;

        return Cache::remember($cacheKey, 60, function () {
            $query = RakTransaksi::with(['rak', 'material', 'user'])
                ->where('dihapus', 0);

            if ($this->rakId) {
                $query->where('id_rak', $this->rakId);
            }

            if ($this->filterDate) {
                $query->whereDate('created_at', $this->filterDate);
            }

            if ($this->filterTipe) {
                $query->where('stat', $this->filterTipe);
            }

            if ($this->search) {
                $query->whereHas('material', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                });
            }

            return $query->latest()->paginate(15);
        });
    }

    public function getTotalMasukProperty()
    {
        $key = "total_in_{$this->rakId}_{$this->filterDate}";

        return Cache::remember($key, 60, function () {
            $query = RakTransaksi::where('dihapus', 0)->where('stat', 'i');

            if ($this->rakId) {
                $query->where('id_rak', $this->rakId);
            }

            if ($this->filterDate) {
                $query->whereDate('created_at', $this->filterDate);
            }

            return $query->count();
        });
    }

    public function getTotalKeluarProperty()
    {
        $key = "total_out_{$this->rakId}_{$this->filterDate}";

        return Cache::remember($key, 60, function () {
            $query = RakTransaksi::where('dihapus', 0)->where('stat', 'o');

            if ($this->rakId) {
                $query->where('id_rak', $this->rakId);
            }

            if ($this->filterDate) {
                $query->whereDate('created_at', $this->filterDate);
            }

            return $query->count();
        });
    }

    public function render()
    {
        return view('livewire.rak.history', [
            'history' => $this->history,
            'totalMasuk' => $this->totalMasuk,
            'totalKeluar' => $this->totalKeluar
        ]);
    }
}
