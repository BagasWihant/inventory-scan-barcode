<?php

namespace App\Livewire;

use App\Models\MaterialRequestAssy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Mpdf\Tag\Em;

class MaterialRequestAssyNew extends Component
{

    public $listLine = [];
    public $listProduct;
    public $listProductFilter;


    public $date;
    public $line_c;
    public $productModel;
    public $productModelSearch = false;
    public $filterMode = false;
    public $qty;
    public $disableConfirm = false;

    public $requestQty;
    public $materialNo;
    public $searchMaterialNo;
    public $resultSearchMaterial = [];
    public $selectedData = [];
    public $materialRequest = [];
    public $editedUser = false;
    public $userRequestDisable = false;
    public $variablePage = [
        'materialRequestNW' => 0,
        'materialRequestWR' => 0,
        'timeNow' => null,
    ];
    public $totalRequest = [
        'qty' => 0,
        'data' => []
    ];
    public $transactionNo = [
        'nw' => null,
        'wr' => null,
    ];
    public $listMaterialNo = [];

    protected $listeners = ['editQty'];

    private function generateNoTransaksi(): void
    {
        $getConfig = DB::table('WH_config')->select('config', 'value')
            ->whereIn('config', ['materialRequestNW', 'materialRequestWR', 'periodRequest'])
            ->get()->keyBy('config');

        $ymd = date('Ymd');
        $this->variablePage['materialRequestWR'] = (int)$getConfig['materialRequestWR']->value + 1;
        $this->variablePage['materialRequestNW'] = (int)$getConfig['materialRequestNW']->value + 1;

        if ($getConfig['periodRequest']->value != $ymd) {
            $this->variablePage['materialRequestNW'] = 1;
            $this->variablePage['materialRequestWR'] = 1;
            DB::table('WH_config')->where('config', 'periodRequest')->update(['value' => $ymd]);
        }
        DB::table('WH_config')->where('config', 'materialRequestNW')->update(['value' => $this->variablePage['materialRequestNW']]);
        DB::table('WH_config')->where('config', 'materialRequestWR')->update(['value' => $this->variablePage['materialRequestWR']]);

        $this->transactionNo['wr'] = "WR$ymd-" . str_pad($this->variablePage['materialRequestWR'], 4, '0', STR_PAD_LEFT);
        $this->transactionNo['nw'] = "NW$ymd-" . str_pad($this->variablePage['materialRequestNW'], 4, '0', STR_PAD_LEFT);
    }

    public function mount()
    {
        $key = 'listline';
        $key1 = 'listproduct';
        $ttl = now()->addHours(1);

        $listline = Cache::remember($key, $ttl, function () {
            return DB::table('mst_line_code')->get();
        });

        $productno = Cache::remember($key1, $ttl, function () {
            return DB::connection('it')->table('BOM')->groupBy('product_no')->select('product_no')->orderBy('product_no')->get();
        });
        // $productno = DB::connection('it')->table('BOM')->groupBy('product_no')->select('product_no')->orderBy('product_no')->get();
        $this->generateNoTransaksi();
        $this->listLine = $listline;
        $this->listProduct = $productno;
    }


    public function updated($prop, $val)
    {
        switch ($prop) {
            case 'productModel':
                if (strlen($val) > 2) {
                    $this->productModelSearch = true;
                    $this->disableConfirm = true;

                    $filtered = $this->listProduct->filter(function ($item) use ($val) {
                        return str_contains(strtolower($item->product_no), $val);
                    })->take(10);
                    $this->listProductFilter = $filtered;
                }
                break;

            case 'qty':
                foreach ($this->listMaterialNo as $item) {
                    if (empty($val) || !is_numeric($val)) $val = 0;
                    if (($item->bom_qty * $val) > $item->qty) {
                        $this->disableConfirm = true;
                        return $this->dispatch('alert', ['time' => 2500, 'icon' => 'error', 'title' => 'Qty melebihi stok']);
                    }
                    if ($item->qty <= 0) {
                        $this->disableConfirm = true;
                        return $this->dispatch('alert', ['time' => 2500, 'icon' => 'error', 'title' => 'Qty 0 atau kurang']);
                    }
                };

                if ($val != 0) {
                    $this->disableConfirm = false;
                    return true;
                } else {
                    $this->disableConfirm = true;
                    return false;
                }

                break;
        }
    }

    public function resetField()
    {
        $this->filterMode = false;
        $this->productModel = '';
        $this->qty = null;
        $this->line_c = null;
        $this->listMaterialNo = [];

    }

    public function selectProductModel($productModel)
    {
        $this->productModel = $productModel;
        $this->productModelSearch = false;
        $this->filterMode = true;
        $this->loadMaterial();
    }

    public function loadMaterial()
    {
        $material = DB::connection('it')->table('db_bantu.dbo.BOM as b')
            ->leftJoin('pt_kias.dbo.material_mst as m', 'b.material_no', '=', 'm.matl_no')
            ->where('product_no', $this->productModel)
            ->select('b.material_no', 'b.bom_qty', DB::raw('100 as qty'), 'm.matl_nm')->get();
        $this->listMaterialNo = $material;
    }

    public function submitRequest()
    {
        // validasi lagi
        if ($this->updated('qty', $this->qty)) {
            
            foreach ($this->listMaterialNo as $value) {
                $transaksiNoItem = (preg_match("/[a-z]/i", $value->material_no)) ? $this->transactionNo['wr'] : $this->transactionNo['nw'];
                // request = qty yang ditulis * bom qty
                $requestQty = $value->bom_qty * (int) $this->qty;
                MaterialRequestAssy::create([
                    'transaksi_no' => $transaksiNoItem,
                    'material_no' => $value->material_no,
                    'material_name' => $value->matl_nm,
                    'type' => '-',
                    'request_qty' => (int) $requestQty, // kadang nek hasil e koma error soale ng db int jaluk e, tak pekso int
                    'bag_qty' => (int) $value->bom_qty, // podo soale kadang desimal
                    'issue_date' => $this->date,
                    'line_c' => $this->line_c,
                    'user_id' => auth()->user()->id,
                    'status' => '1', // => ke menu packing
                    'user_request' => auth()->user()->username, // tak ganti user login
                    // sisa = qty dari query -> ambil dari mst - request
                    'sisa_request_qty' => $value->qty - (int) $requestQty // iki yo tak pekso int soale kadang eror nek koma
                ]);
            }

            $this->resetField();
            $this->dispatch('alert', ['time' => 2500, 'icon' => 'success', 'title' => 'Berhasil Request Material Assy']);

        } else {
            $this->dispatch('alert', ['time' => 2500, 'icon' => 'warning', 'title' => 'Kurang sesuai']);
        }
    }


    public function render()
    {
        return view('livewire.material-request-assy-new');
    }
}
