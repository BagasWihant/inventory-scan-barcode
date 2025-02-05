<?php

namespace App\Livewire;

use App\Exports\ExportMaterialAvailable;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class MaterialAvailable extends Component
{
    use WithPagination;
    public $dateStart, $dateEnd, $searchMat, $matDisable = false, $listMaterial = [];
    public $resetBtn = false;
    public $shift = 'all';


    public function matChange()
    {
        if (strlen($this->searchMat) >= 3) {
            $this->listMaterial = DB::table('material_in_stock as  mis')->distinct()
                ->select('material_no as material')
                ->leftJoin('material_mst as mst', 'mis.material_no', 'mst.matl_no')
                ->whereNot('mst.loc_cd', "ASSY")
                ->where('material_no', 'like', '%' . $this->searchMat . '%')->limit(15)
                ->get();
        }
    }
    public function chooseMat($val)
    {
        $this->searchMat = $val;
        $this->matChange();
        // $this->listMaterial = [];
    }

    public function resetFilter()
    {
        $this->matDisable   = false;
        $this->searchMat = null;
        $this->listMaterial = [];
        $this->resetBtn = false;
        $this->dateStart = null;
        $this->dateEnd = null;
        $this->shift = null;
    }
    public function showData()
    {
        $this->matDisable   = true;
        $this->resetBtn = true;
        if (!$this->dateStart && !$this->dateEnd) {
            $this->dateStart = '2024-07-01';
            $this->dateEnd = date('Y-m-d');
        }
    }

    public function export()
    {
        $data = [
            $this->dateStart,
            $this->dateEnd,
            $this->searchMat,
            $this->shift
        ];
        return Excel::download(new ExportMaterialAvailable($data), "Material Available " . date('YmdHis') . ".xlsx", \Maatwebsite\Excel\Excel::XLSX);
    }
    public function render()
    {

        $listData = [];
        $startDate = $this->dateStart;
        $endDate = $this->dateEnd;
        $materialNo = $this->searchMat;
        $shift = $this->shift;
        // dd($this);
        $result = self::queryHandle($startDate, $endDate, $materialNo, $shift);

        if ($this->dateStart && $this->dateEnd && $this->resetBtn) {
            $listData = $result->paginate(20);
        }


        return view('livewire.material-available', compact('listData'));
    }

    public static function queryHandle($startDate, $endDate, $materialNo, $shift = null)
    {
        // $complexQuery = DB::table(function ($subQuery1) use ($startDate, $endDate, $materialNo, $shift) {
        //     //  MaterialInStock
        //     $subQuery1->from(function ($unionQuery) use ($startDate, $endDate, $materialNo, $shift) {
        //         $unionQuery->from('material_mst AS mis')
        //             ->selectRaw("mis.matl_no AS material_no, 0 AS total_picking_qty, '' AS first_created_at1, '' AS first_created_at2")
        //             ->where('updated_at', '>=', '2024-07-01 00:00:00.001')
        //             ->unionAll(
        //                 DB::table('material_in_stock AS mis')
        //                     ->selectRaw("mis.material_no, SUM(mis.picking_qty) AS total_picking_qty, '' AS first_created_at1, MIN(CONVERT(DATE, mis.created_at)) AS first_created_at2")
        //                     // ->whereBetween(DB::raw('CONVERT(DATE, mis.created_at)'), [$startDate, $endDate])
        //                     ->when($materialNo, function ($sub) use ($materialNo) {
        //                         $sub->where('mis.material_no', $materialNo);
        //                     })
        //                     ->where(function ($query) {
        //                         $query->where('mis.locate', '!=', 'ASSY')
        //                             ->orWhereNull('mis.locate');
        //                     })
        //                     // ->when($shift, function ($sub) use ($shift) {
        //                     //     if ($shift == 'day') {
        //                     //         $sub->whereBetween(DB::raw('CONVERT(TIME,mis.created_at)'), ['07:00:00', '16:00:00']);
        //                     //     } else {
        //                     //         $sub->where(function ($sub2) {
        //                     //             $sub2->where(DB::raw('CONVERT(TIME,mis.created_at)'), '>=', '18:30:00')->orWhere(DB::raw('CONVERT(TIME,mis.created_at)'), '<', '06:30:00');
        //                     //         });
        //                     //     }
        //                     // })
        //                     ->when($shift, function ($sub) use ($shift, $startDate, $endDate) {

        //                         if ($shift == 'night') {
        //                             $sub->where(function ($query) use ($startDate) {
        //                                 $query->where(DB::raw("CONVERT(date, mis.created_at)"), $startDate)
        //                                     ->where(DB::raw("CONVERT(time, mis.created_at)"), '>=', '18:30:00');
        //                             })->orWhere(function ($query) use ($endDate) {
        //                                 $query->where(DB::raw("CONVERT(date, mis.created_at)"), $endDate)
        //                                     ->where(DB::raw("CONVERT(time, mis.created_at)"), '<', '06:30:00');
        //                             });
        //                         } elseif ($shift == 'day') {
        //                             $sub->where(function ($query) use ($startDate) {
        //                                 $query->where(DB::raw("CONVERT(date, mis.created_at)"), $startDate)
        //                                     ->where(DB::raw("CONVERT(time, mis.created_at)"), '>=', '07:00:00');
        //                             })->orWhere(function ($query) use ($endDate) {
        //                                 $query->where(DB::raw("CONVERT(date, mis.created_at)"), $endDate)
        //                                     ->where(DB::raw("CONVERT(time, mis.created_at)"), '<',  '16:00:00');
        //                             });
        //                         } else {
        //                             $sub->whereBetween(DB::raw('CONVERT(DATE, mis.created_at)'), [$startDate, $endDate]);
        //                         }
        //                     })
        //                     ->groupBy('mis.material_no')
        //             );
        //     }, 'X')
        //         ->selectRaw('material_no, SUM(total_picking_qty) AS total_picking_qty, MAX(CONVERT(DATE, first_created_at2)) AS first_created_at')
        //         ->groupBy('material_no');
        // }, 'MaterialInStock')
        //     ->leftJoinSub(
        //         // QuantityOut
        //         DB::table(function ($unionQuery) use ($startDate, $endDate, $materialNo, $shift) {
        //             $unionQuery->from('siws_materialrequest.dbo.dtl_transaction')
        //                 ->selectRaw('part_number, SUM(qty_mc) AS Qty')
        //                 ->when($materialNo, function ($sub) use ($materialNo) {
        //                     $sub->where('part_number', $materialNo);
        //                 })
        //                 ->when($shift, function ($sub) use ($shift, $startDate, $endDate) {

        //                     if ($shift == 'night') {
        //                         $sub->where(function ($query) use ($startDate) {
        //                             $query->where(DB::raw("CONVERT(date, transaction_date)"), $startDate)
        //                                 ->where(DB::raw("CONVERT(time, transaction_date)"), '>=', '18:30:00');
        //                         })->orWhere(function ($query) use ($endDate) {
        //                             $query->where(DB::raw("CONVERT(date, transaction_date)"), $endDate)
        //                                 ->where(DB::raw("CONVERT(time, transaction_date)"), '<', '06:30:00');
        //                         });
        //                     } elseif ($shift == 'day') {
        //                         $sub->where(function ($query) use ($startDate) {
        //                             $query->where(DB::raw("CONVERT(date, transaction_date)"), $startDate)
        //                                 ->where(DB::raw("CONVERT(time, transaction_date)"), '>=', '07:00:00');
        //                         })->orWhere(function ($query) use ($endDate) {
        //                             $query->where(DB::raw("CONVERT(date, transaction_date)"), $endDate)
        //                                 ->where(DB::raw("CONVERT(time, transaction_date)"), '<',  '16:00:00');
        //                         });
        //                     } else {
        //                         $sub->whereBetween(DB::raw('CONVERT(DATE, transaction_date)'), [$startDate, $endDate]);
        //                     }
        //                 })
        //                 ->groupBy('part_number')
        //                 ->unionAll(
        //                     DB::table('Setup_dtl AS c')
        //                         ->leftJoin('Setup_mst AS b', 'b.id', '=', 'c.setup_id')
        //                         ->selectRaw('c.material_no AS part_number, SUM(c.qty) AS Qty')
        //                         ->whereNotNull('b.finished_at')
        //                         // ->whereBetween(DB::raw('CONVERT(DATE, c.created_at)'), [$startDate, $endDate])
        //                         // ->when($shift, function ($sub) use ($shift) {
        //                         //     if ($shift == 'day') {
        //                         //         $sub->whereBetween(DB::raw('CONVERT(TIME,c.created_at)'), ['07:00:00', '16:00:00']);
        //                         //     } else {
        //                         //         $sub->where(function ($sub2) {
        //                         //             $sub2->where(DB::raw('CONVERT(TIME,c.created_at)'), '>=', '18:30:00')->orWhere(DB::raw('CONVERT(TIME,c.created_at)'), '<', '06:30:00');
        //                         //         });
        //                         //     }
        //                         // })
        //                         ->when($shift, function ($sub) use ($shift, $startDate, $endDate) {

        //                             if ($shift == 'night') {
        //                                 $sub->where(function ($query) use ($startDate) {
        //                                     $query->where(DB::raw("CONVERT(date, c.created_at)"), $startDate)
        //                                         ->where(DB::raw("CONVERT(time, c.created_at)"), '>=', '18:30:00');
        //                                 })->orWhere(function ($query) use ($endDate) {
        //                                     $query->where(DB::raw("CONVERT(date, c.created_at)"), $endDate)
        //                                         ->where(DB::raw("CONVERT(time, c.created_at)"), '<', '06:30:00');
        //                                 });
        //                             } elseif ($shift == 'day') {
        //                                 $sub->where(function ($query) use ($startDate) {
        //                                     $query->where(DB::raw("CONVERT(date, c.created_at)"), $startDate)
        //                                         ->where(DB::raw("CONVERT(time, c.created_at)"), '>=', '07:00:00');
        //                                 })->orWhere(function ($query) use ($endDate) {
        //                                     $query->where(DB::raw("CONVERT(date, c.created_at)"), $endDate)
        //                                         ->where(DB::raw("CONVERT(time, c.created_at)"), '<',  '16:00:00');
        //                                 });
        //                             } else {
        //                                 $sub->whereBetween(DB::raw('CONVERT(DATE, transaction_date)'), [$startDate, $endDate]);
        //                             }
        //                         })
        //                         ->groupBy('c.material_no')
        //                 );
        //         }, 'combined_qty_out')
        //             ->selectRaw('part_number, SUM(Qty) AS qty')
        //             ->groupBy('part_number'),
        //         'QuantityOut',
        //         'MaterialInStock.material_no',
        //         '=',
        //         'QuantityOut.part_number'
        //     )
        //     ->leftJoin('material_mst AS mst', 'MaterialInStock.material_no', '=', 'mst.matl_no')
        //     ->where('mst.loc_cd', '!=', 'ASSY')
        //     ->where(function ($query) {
        //         $query->where('MaterialInStock.total_picking_qty', '<>', 0)
        //             ->orWhere(DB::raw('COALESCE(QuantityOut.qty, 0)'), '<>', 0);
        //     })
        //     ->selectRaw('MaterialInStock.material_no, (MaterialInStock.total_picking_qty - COALESCE(QuantityOut.qty, 0)) as qty_balance, MaterialInStock.total_picking_qty AS qty_in, COALESCE(QuantityOut.qty, 0) AS qty_out, SUM(mst.qty) AS qty_now, mst.loc_cd')
        //     ->groupBy('MaterialInStock.material_no', 'MaterialInStock.total_picking_qty', 'QuantityOut.qty', 'mst.loc_cd')
        //     ->orderBy('MaterialInStock.material_no');

        // return $complexQuery;


        // --------------------------------------- METODE BARU ---------------------------------------
        $qtyInQuery = DB::table(function ($subQuery1) use ($startDate, $endDate, $materialNo, $shift) {
            $subQuery1->from(function ($unionQuery) use ($startDate, $endDate, $materialNo, $shift) {
                $unionQuery->from('material_mst AS mis')
                    ->selectRaw("mis.matl_no AS material_no, 0 AS total_picking_qty, '' AS first_created_at1, '' AS first_created_at2")
                    ->where('updated_at', '>=', '2024-07-01 00:00:00.001')
                    ->when($materialNo, function ($sub) use ($materialNo) {
                        $sub->where('mis.matl_no', $materialNo);
                    })
                    ->unionAll(
                        DB::table('material_in_stock AS mis')
                            ->selectRaw("mis.material_no, SUM(mis.picking_qty) AS total_picking_qty, '' AS first_created_at1, MIN(CONVERT(DATE, mis.created_at)) AS first_created_at2")
                            ->when($materialNo, function ($sub) use ($materialNo) {
                                $sub->where('mis.material_no', $materialNo);
                            })
                            ->where(function ($query) {
                                $query->where('mis.locate', '!=', 'ASSY')
                                    ->orWhereNull('mis.locate');
                            })
                            ->when($shift, function ($sub) use ($shift, $startDate, $endDate) {
                                // Filter shift
                                if ($shift == 'night') {
                                    $sub->where(function ($query) use ($startDate, $endDate) {
                                        $query->whereBetween(DB::raw("CONVERT(DATE, mis.created_at)"), [$startDate, $endDate])
                                            ->where(function ($q) {
                                                $q->where(DB::raw("CONVERT(TIME, mis.created_at)"), '>=', '18:30:00')
                                                    ->orWhere(DB::raw("CONVERT(TIME, mis.created_at)"), '<', '06:30:00');
                                            });
                                    });
                                } elseif ($shift == 'day') {
                                    $sub->where(function ($query) use ($startDate, $endDate) {
                                        $query->whereBetween(DB::raw("CONVERT(DATE, mis.created_at)"), [$startDate, $endDate])
                                            ->where(function ($q) {
                                                $q->where(DB::raw("CONVERT(TIME, mis.created_at)"), '>=', '07:00:00')
                                                    ->where(DB::raw("CONVERT(TIME, mis.created_at)"), '<', '16:00:00');
                                            });
                                    });
                                } else {
                                    $sub->whereBetween(DB::raw('CONVERT(DATE, mis.created_at)'), [$startDate, $endDate]);
                                }
                            })
                            ->groupBy('mis.material_no')
                    );
            }, 'X')
                ->selectRaw('material_no, SUM(total_picking_qty) AS total_picking_qty, MAX(CONVERT(DATE, first_created_at2)) AS first_created_at')
                ->groupBy('material_no');
        }, 'MaterialInStock');

        $qtyOutQuery = DB::table(function ($unionQuery) use ($startDate, $endDate, $materialNo, $shift) {
            $unionQuery->from('siws_materialrequest.dbo.dtl_transaction')
                ->selectRaw('part_number, SUM(qty_mc) AS Qty')
                ->when($materialNo, function ($sub) use ($materialNo) {
                    $sub->where('part_number', $materialNo);
                })
                ->when($shift, function ($sub) use ($shift, $startDate, $endDate) {
                    // Filter shift
                    if ($shift == 'night') {
                        $sub->where(function ($query) use ($startDate, $endDate) {
                            $query->whereBetween(DB::raw("CONVERT(DATE, transaction_date)"), [$startDate, $endDate])
                                ->where(function ($q) {
                                    $q->where(DB::raw("CONVERT(TIME, transaction_date)"), '>=', '18:30:00')
                                        ->orWhere(DB::raw("CONVERT(TIME, transaction_date)"), '<', '06:30:00');
                                });
                        });
                    } elseif ($shift == 'day') {
                        $sub->where(function ($query) use ($startDate, $endDate) {

                            $query->whereBetween(DB::raw("CONVERT(DATE, transaction_date)"), [$startDate, $endDate])
                                ->where(function ($q) {
                                    $q->where(DB::raw("CONVERT(TIME, transaction_date)"), '>=', '07:00:00')
                                        ->where(DB::raw("CONVERT(TIME, transaction_date)"), '<', '16:00:00');
                                });
                        });
                    } else {
                        $sub->whereBetween(DB::raw('CONVERT(DATE, transaction_date)'), [$startDate, $endDate]);
                    }
                })
                ->groupBy('part_number')
                ->unionAll(
                    DB::table('Setup_dtl AS c')
                        ->leftJoin('Setup_mst AS b', 'b.id', '=', 'c.setup_id')
                        ->selectRaw('c.material_no AS part_number, SUM(c.qty) AS Qty')
                        ->whereNotNull('b.finished_at')
                        ->when($materialNo, function ($sub) use ($materialNo) {
                            $sub->where('c.material_no', $materialNo);
                        })
                        ->when($shift, function ($sub) use ($shift, $startDate, $endDate) {
                            // Filter shift
                            if ($shift == 'night') {
                                $sub->where(function ($query) use ($startDate, $endDate) {
                                    $query->whereBetween(DB::raw("CONVERT(DATE, c.created_at)"), [$startDate, $endDate])
                                        ->where(function ($q) {
                                            $q->where(DB::raw("CONVERT(TIME, c.created_at)"), '>=', '18:30:00')
                                                ->orWhere(DB::raw("CONVERT(TIME, c.created_at)"), '<', '06:30:00');
                                        });
                                });
                            } elseif ($shift == 'day') {
                                $sub->where(function ($query) use ($startDate, $endDate) {
                                    $query->whereBetween(DB::raw("CONVERT(DATE, c.created_at)"), [$startDate, $endDate])
                                        ->where(function ($q) {
                                            $q->where(DB::raw("CONVERT(TIME, c.created_at)"), '>=', '07:00:00')
                                                ->where(DB::raw("CONVERT(TIME, c.created_at)"), '<', '16:00:00');
                                        });
                                });
                            } else {
                                $sub->whereBetween(DB::raw('CONVERT(DATE, c.created_at)'), [$startDate, $endDate]);
                            }
                        })
                        ->groupBy('c.material_no')
                );
        }, 'combined_qty_out')
            ->selectRaw('part_number, SUM(Qty) AS qty')
            ->groupBy('part_number');


        $finalQuery = DB::table(DB::raw("({$qtyInQuery->toSql()}) AS MaterialInStock"))
            ->mergeBindings($qtyInQuery)
            ->leftJoinSub($qtyOutQuery, 'QuantityOut', 'MaterialInStock.material_no', '=', 'QuantityOut.part_number')
            ->leftJoin('material_mst AS mst', 'MaterialInStock.material_no', '=', 'mst.matl_no')
            ->where('mst.loc_cd', '!=', 'ASSY')
            ->where(function ($query) {
                $query->where('MaterialInStock.total_picking_qty', '<>', 0)
                    ->orWhere(DB::raw('COALESCE(QuantityOut.qty, 0)'), '<>', 0);
            })
            ->selectRaw('MaterialInStock.material_no, 
                (MaterialInStock.total_picking_qty - COALESCE(QuantityOut.qty, 0)) as qty_balance, 
                MaterialInStock.total_picking_qty AS qty_in, 
                COALESCE(QuantityOut.qty, 0) AS qty_out, 
                SUM(mst.qty) AS qty_now, 
                mst.loc_cd')
            ->groupBy('MaterialInStock.material_no', 'MaterialInStock.total_picking_qty', 'QuantityOut.qty', 'mst.loc_cd')
            ->orderBy('MaterialInStock.material_no');
        // dump($qtyInQuery->toRawSql(), $qtyInQuery->getBindings());
        // dump($qtyOutQuery->toRawSql());
        // dump($finalQuery->toRawSql(), $finalQuery->getBindings());
        return $finalQuery;
    }
}
