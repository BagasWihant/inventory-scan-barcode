<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class InventoryInController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.list-product');
        
    }
    
    public function lack() {
        return view('pages.lack-item');
    }
    public function excess() {
        return view('pages.excess-material');
    }
    public function instock() {
        return view('pages.material-instock');
    }
    public function checking() {
        return view('pages.checking-stock');
    }
    public function detail()
    {
        return view('pages.list-product');
    }

}
