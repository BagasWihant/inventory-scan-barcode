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
    public $productModelSelected;
    public $filterMode = false;
    public $qty;
    public $disableConfirm = false;

    public $requestQty;
    public $materialNo;
    public $searchMaterialNo;
    public $materialRequest = [];
    public $variablePage = [
        'materialRequestREG' => 0,
        'timeNow' => null,
    ];
    public $totalRequest = [
        'qty' => 0,
        'data' => []
    ];
    public $transactionNo = null;
    public $listMaterialNo = [];

    protected $listeners = ['editQty'];

    private function generateNoTransaksi(): void
    {
        $row = DB::table('WH_config')->where('config', 'materialRequestREG')->first();

        if (!$row) {
            DB::table('WH_config')->updateOrInsert(
                ['config' => 'materialRequestREG'],
                ['value' => 0]
            );
        }

        $getConfig = DB::table('WH_config')->select('config', 'value')
            ->whereIn('config', ['materialRequestREG', 'periodRequest'])
            ->get()->keyBy('config');

        $ymd = date('Ymd');
        $this->variablePage['materialRequestREG'] = (int)$getConfig['materialRequestREG']->value + 1;

        if ($getConfig['periodRequest']->value != $ymd) {
            $this->variablePage['materialRequestREG'] = 1;
            DB::table('WH_config')->where('config', 'periodRequest')->update(['value' => $ymd]);
        }
        DB::table('WH_config')->where('config', 'materialRequestREG')->update(['value' => $this->variablePage['materialRequestREG']]);

        $this->transactionNo = "REG-$ymd-" . str_pad($this->variablePage['materialRequestREG'], 4, '0', STR_PAD_LEFT);
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
            return DB::connection('it')->table('db_bantu.dbo.BOM as bom')
                ->leftJoin('prs_kias.dbo.prs_master_product as m', function ($join) {
                    $join->on('bom.product_no', '=', 'm.product_no')
                        ->on('bom.dc', '=', 'm.dc');
                })
                // ->leftJoin('prs_kias.dbo.prs_assy_plan as a', 'm.id', '=', 'a.product_id')
                ->select(DB::raw("CONCAT(bom.product_no,' - ',bom.dc) as product_no"), 'bom.product_no as product_no_ori', 'bom.dc', 'm.id')
                ->groupBy('bom.product_no', 'bom.dc', 'm.id')
                ->orderBy('bom.product_no')->get();
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
                        return str_contains(strtolower($item->product_no), strtolower($val));
                    })->take(10);

                    $this->listProductFilter = $filtered;
                }
                break;

            case 'qty':
                if ($this->qty > $this->productModelSelected->plan) {

                    return $this->dispatch('alertB', ['time' => 2500, 'icon' => 'error', 'title' => 'Input Qty melebihi Qty Plan']);
                }
                foreach ($this->listMaterialNo as $item) {
                    if (empty($val) || !is_numeric($val)) $val = 0;
                    if (($item->bom_qty * $val) > $item->qty) {
                        $this->disableConfirm = true;
                        return $this->dispatch('alertB', ['time' => 2500, 'icon' => 'error', 'title' => 'Qty melebihi stok']);
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
        $this->productModelSelected = null;
    }

    public function selectProductModel($productModel)
    {
        $product = json_decode($productModel);
        $this->productModelSelected = $product;
        $this->productModel = $product->product_no;
        $this->loadMaterial();

        $assy_plan =DB::table('prs_kias.dbo.prs_assy_plan')->where('product_id', $product->id)->where('tanggal',$this->date)->whereNull('deleted_at')->orderByDesc('updated_at')->first();
        $this->qty = $assy_plan->plan;
        $this->productModelSelected->plan = $assy_plan->plan;
        $this->productModelSearch = false;
        $this->filterMode = true;
        $this->updated('qty', $this->qty);
    }

    public function loadMaterial()
    {
        $material = DB::connection('it')->table('db_bantu.dbo.BOM as b')
            ->leftJoin('pt_kias.dbo.material_mst as m', 'b.material_no', '=', 'm.matl_no')
            ->whereRaw("CONCAT(product_no,' - ',dc) like ?", $this->productModel)
            // ->select('b.material_no', 'b.bom_qty', DB::raw('1000 as qty'), 'm.matl_nm')->get(); 
            ->select('b.material_no', 'b.bom_qty', 'm.qty', 'm.matl_nm')->get(); // sing bener iki m.qty yaa DB::raw('100 as qty') tak go bypass qty

        $this->listMaterialNo = $material;
    }

    public function submitRequest()
    {
        // validasi lagi
        if ($this->updated('qty', $this->qty)) {

            foreach ($this->listMaterialNo as $value) {
                // $transaksiNoItem = (preg_match("/[a-z]/i", $value->material_no)) ? $this->transactionNo['wr'] : $this->transactionNo['nw'];
                // request = qty yang ditulis * bom qty
                $requestQty = $value->bom_qty * (int) $this->qty;
                MaterialRequestAssy::create([
                    'transaksi_no' => $this->transactionNo,
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
                    'product_model' => $this->productModel,
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
