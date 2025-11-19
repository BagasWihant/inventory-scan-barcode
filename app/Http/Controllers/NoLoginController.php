<?php
namespace App\Http\Controllers;

use App\Models\User;
use Cache;

class NoLoginController extends Controller
{
    protected $nik;

    public function __construct()
    {
        $nik = request()->route('nik');
        $uss = Cache::rememberForever(
            $nik . "09",
            function () use ($nik) {
                return User::where('nik', $nik)->select('id')->first();
            }
        );
        if (!$uss) {
            abort(499, 'NIK tidak terdaftar');
        }
    }

    public function recvSiws()
    {
        return view('pages.receiving-siws-news', ['bypass' => true]);
    }

    public function poNew()
    {
        return view('pages.po_new', ['bypass' => true]);
    }

    public function inStock()
    {
        return view('pages.material-instock', ['bypass' => true]);
    }

    public function checkingStock()
    {
        return view('pages.checking-stock', ['bypass' => true]);
    }

    public function assyUpload()
    {
        return view('pages.single.assy-upload', ['bypass' => true]);
    }
}
