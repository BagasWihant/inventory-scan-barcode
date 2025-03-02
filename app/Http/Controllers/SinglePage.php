<?php

namespace App\Http\Controllers;

use App\Exports\Approval\Generate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SinglePage extends Controller
{
    private function getstatus($str, $kondisi)
    {
        $map = [
            'O'  => 'Open', // approve purchahing
            'PT' => 'Tolak Purchasing',
            'AP' => 'Approved Purchasing', // next approve SPv
            'APT' => 'Tolak SPV',
            'AS' => 'Approved SPV', // -> Next approve app mgr
            'AM' => 'Approved Management',
            'AMT'  => 'Tolak Management',
        ];

        return $kondisi == 'Long'
            ? ($map[$str] ?? 'Unknown')
            : (array_search($str, $map) ?: 'Unknown');
    }

    public function approval($id, $no)
    {
        $validator = Validator::make(
            ['id' => $id],
            ['id' => 'required|integer']
        );

        if ($validator->fails()) {
            abort(400, 'ID HARUS ANGKA'); // Atau bisa menggunakan dd('Invalid ID') untuk debug
        }

        $req = DB::table('IT.dbo.PR_MASTER_PLAN as m')
            ->where("m.id", $id)
            ->where("m.no_plan", $no)
            ->leftJoin('IT.dbo.PR_approval_hirarki as h', 'm.sec', '=', 'h.section')
            ->leftJoin('IT.dbo.PR_pr as pr', 'm.id', '=', 'pr.id_no_plan')
            ->selectRaw('pr.*,spv1,spv2,spv3,ast_mgr,mgr,m.*')
            ->first();

        $detail = DB::table('IT.dbo.PR_detail_plan as d')
            ->where("id_master_plan", $req->id)->get();
        $req->detail = $detail;

        $docNo      = "ID-$req->sec-$req->no_plan";
        $req->docNo = $docNo;

        $status      = $this->getstatus($req->status, 'Short');
        $req->status = $status;

        $req->type = 'Pengajuan';


        if ($status == 'O') {
            // halaman approve purchasing
            $allowedPurchase = [
                '127.0.0.1',
                '192.168.1.7'
            ];

            if (!in_array(request()->ip(), $allowedPurchase)) {
                return "<b>Anda tidak bisa mengakses ini. Bukan Purchasing<b>";
            }
        } elseif ($status == 'AP') {
            // halaman spv
            $allowedSPV = [
                '127.0.0.1',
                '192.168.1.7'
            ];

            if (!in_array(request()->ip(), $allowedSPV)) {
                return "<b>Anda tidak bisa mengakses ini. Bukan Supervisor<b>";
            }
        } elseif ($status == 'AS') {
            // halaman manager
            $allowedIPMan = [
                '127.0.0.1',
                '192.168.1.7'
            ];

            if (!in_array(request()->ip(), $allowedIPMan)) {
                return "<b>Anda tidak bisa mengakses ini. Bukan Manager<b>";
            }
        }

        // save qrcode
        $allow = ['spv1', 'mgr'];
        $sign = [];

        // Setting nilai QRCODE
        $req->qrCreator = '';
        $req->qrSpv     = '';
        $req->qrMgr     = '';
        foreach ($req as $key => $value) {
            if (in_array($key, $allow)) {

                $qr='';
                
                if ($key == 'spv1' && $req->tgl_diperiksa != null) {
                    $key = 'spv';
                    $valQR = "NIK/$value/$req->tgl_diperiksa";
                    $qr = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($valQR));
                
                } else if ($key == 'mgr' && $req->tgl_disetujui != null) {
                    $valQR = "NIK/$value/$req->tgl_disetujui";
                    $qr = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($valQR));
                }
                
                $sign[$key] = [
                    "qrcode" => $qr,
                    "name" => $value
                ];
            }
        }

        // Setting nama pembuat
        if ($status != 'O') {

            $valQR = "NIK/$req->nama/$req->tanggal_plan";
            $sign['creator']['qrcode'] = $qr = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($valQR));
            $sign['creator']['nama'] = $req->nama;
        }

        $req->signCode = $sign;

        $fileName = $docNo . '.pdf';
        $directory = storage_path('app/public/approval/pdf');
        $path = $directory . '/' . $fileName;

        if (!file_exists($directory)) {
            mkdir($directory, 0775, true);
        }

        // if (!file_exists($path)) {
        if (true) {

            $html = view('templates.pdf.approval-generate', compact('req'))->render();
            $mpdf = new Mpdf();
            $mpdf->SetHTMLHeader("
            <span style='left: 1px;
                top: 1px;
                position: absolute;
                font-weight: bold;
                font-size: 12px;'>
                PT. KARANGANYAR INDO AUTO SYSTEMS 
            </span>
            ");

            $mpdf->WriteHTML($html);
            $mpdf->Output($path, Destination::FILE);
        }

        $req->pdf = Storage::url('approval/pdf/' . $fileName);

        return view('pages.single.approval', compact('req'));
    }

    public function approve(Request $req)
    {
        $decode = json_decode($req->data, true);

        $status = $decode['status'];
        if (!in_array($status, ['O', 'AP', 'AS'])) {
            return false;
        }

        $id = $decode['id'];
        $no = $decode['no_plan'];

        $cek = DB::table('IT.dbo.PR_MASTER_PLAN')
            ->where("id", $id)
            ->where("no_plan", $no)
            ->first();

        $shortStatus = $this->getstatus($cek->status, 'Short');
        if ($shortStatus != $status) {
            $data['status'] = '0';
            $data['posisi'] = '';
            $data['text'] = 'Pengajuan ini sudah di Eksekusi';
            return view('pages.single.approval-response', compact('data'));
        }

        if ($status == 'O') {
            $nextStatus = 'AP';
            $data['posisi'] = 'Purchasing';
            $data['pos'] = 'Purchasing';
        } elseif ($status == 'AP') {
            $nextStatus = 'AS';
            $data['posisi'] = 'Supervisor';
            $data['pos'] = $decode['spv1'];
        } else {
            $nextStatus = 'AM';
            $data['posisi'] = 'Manager';
            $data['pos'] = $decode['mgr'];
        }

        if ($shortStatus != $nextStatus) {

            DB::table('IT.dbo.PR_MASTER_PLAN')
                ->where("id", $id)
                ->where("no_plan", $no)
                ->update([
                    'status' => $this->getstatus($nextStatus, 'Long')
                ]);

            $insertData = [
                'id_no_plan' => $id,
                'no_plan' => $no,
                'sec' => $cek->sec,
                'tanggal_pr' => $cek->tanggal_plan
            ];

            if ($shortStatus == 'O') {
                $insertData['diperiksa'] = $data['pos'];
                $insertData['tgl_diperiksa'] = date('Y-m-d H:i:s');

                // INSRT
                // DB::table(DB::raw('IT.dbo.PR_pr'))
                //     ->insert($insertData);


            } elseif ($shortStatus == 'AP') {

                $updateData['diperiksa'] = $data['pos'];
                $updateData['tgl_diperiksa'] = date('Y-m-d H:i:s');

                DB::table(DB::raw('IT.dbo.PR_pr'))
                    ->where('id_no_plan', $id)
                    ->where('no_plan', $no)
                    ->update($updateData);
            } elseif ($shortStatus == 'AS') {

                $updateData['disetujui'] = $data['pos'];
                $updateData['tgl_disetujui'] = date('Y-m-d H:i:s');

                DB::table(DB::raw('IT.dbo.PR_pr'))
                    ->where('id_no_plan', $id)
                    ->where('no_plan', $no)
                    ->update($updateData);
            }


            $data['text'] = 'Berhasil disetujui oleh';
            $data['status'] = '1';
        } else {
            $data['status'] = '0';
            $data['posisi'] = '';
            $data['pos'] = '';
            $data['text'] = 'Mohon buka kembali link anda. Pengajuan ini sudah di eksekusi';
        }

        return view('pages.single.approval-response', compact('data'));
    }


    public function reject(Request $req)
    {

        $decode = json_decode($req->data, true);
        $status = $decode['status'];
        if (!in_array($status, ['O', 'AP', 'AS'])) {
            return false;
        }

        $id = $decode['id'];
        $no = $decode['no_plan'];

        $cek = DB::table(DB::raw('IT.dbo.PR_MASTER_PLAN'))
            ->where("id", $id)
            ->where("no_plan", $no)
            ->first();

        // cek jika udah diapprove tapi direjek
        $shortStatus = $this->getstatus($cek->status, 'Short');
        if ($shortStatus != $status) {
            $data['status'] = '0';
            $data['posisi'] = '';
            $data['text'] = 'Pengajuan ini sudah di Eksekusi';
            return view('pages.single.approval-response', compact('data'));
        }

        if ($status == 'O') {

            $statusT = 'PT';
            $data['posisi'] = 'Purchasing';
            $data['pos'] = 'Purchasing';
        } elseif ($status == 'AP') {

            $statusT = 'APT';
            $data['posisi'] = 'Supervisor';
            $data['pos'] = $decode['spv1'];
        } elseif ($status == 'AS') {

            $data['posisi'] = 'Manager';
            $data['pos'] = $decode['mgr'];
            $statusT = 'AMT';
        }

        if ($shortStatus != $statusT) {

            DB::table(DB::raw('IT.dbo.PR_MASTER_PLAN'))
                ->where("id", $id)
                ->where("no_plan", $no)
                ->update([
                    'status' => $this->getstatus($statusT, 'Long'),
                    'alasan_revisi' => $req->message
                ]);

            if ($shortStatus == 'O') {

                $insertData = [
                    'id_no_plan' => $id,
                    'no_plan' => $no,
                    'sec' => $cek->sec,
                    'tanggal_pr' => $cek->tanggal_plan,
                    'ditolak' => $data['pos'],
                    'tgl_ditolak' => date('Y-m-d H:i:s'),
                ];

                // DB::table(DB::raw('IT.dbo.PR_pr'))
                //     ->insert($insertData);
                $updateData['ditolak'] = $data['pos'];
                $updateData['tgl_ditolak'] = date('Y-m-d H:i:s');

                DB::table(DB::raw('IT.dbo.PR_pr'))
                    ->where('id_no_plan', $id)
                    ->where('no_plan', $no)
                    ->update($updateData);
            } else {

                $updateData['ditolak'] = $data['pos'];
                $updateData['tgl_ditolak'] = date('Y-m-d H:i:s');

                DB::table(DB::raw('IT.dbo.PR_pr'))
                    ->where('id_no_plan', $id)
                    ->where('no_plan', $no)
                    ->update($updateData);
            }
            $data['text'] = 'Ditolak oleh';
            $data['status'] = '0';
        } else {
            $data['status'] = '0';
            $data['posisi'] = '';
            $data['text'] = 'Mohon buka kembali link anda. Pengajuan ini sudah di eksekusi';
        }

        return view('pages.single.approval-response', compact('data'));
    }
}
