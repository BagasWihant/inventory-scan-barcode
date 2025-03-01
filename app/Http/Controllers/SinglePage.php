<?php

namespace App\Http\Controllers;

use App\Exports\Approval\Generate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorPNG;

class SinglePage extends Controller
{
    private function getstatus($str, $kondisi)
    {
        $map = [
            'O'  => 'Open',
            'OT'  => 'Tolak SPV',
            'AP' => 'Approved Purchasing',
            'APT' => 'Tolak Direksi',
            'AM' => 'Approved Management',
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
        ->leftJoin('IT.dbo.PR_approval_hirarki as h','m.sec','=','h.section')        
        ->selectRaw('m.*,spv1,spv2,spv3,ast_mgr,mgr')
        ->first();

        $detail = DB::table('IT.dbo.PR_detail_plan as d')
        ->where("id_master_plan", $req->id)->get();
        $req->detail = $detail;

        $status = $this->getstatus($req->status, 'Short');

        if ($status == 'O') {
            $allowedIPspv = [
                '127.0.0.1',
            ];

            if (!in_array(request()->ip(), $allowedIPspv)) {
                // return abort(400, 'BUKAN SPV');
                return "<b>Anda tidak bisa mengakses ini. Bukan Supervisor<b>";
            }
        } elseif ($status == 'AP') {

            $allowedIPMan = [
                '127.0.0.1',
            ];

            if (!in_array(request()->ip(), $allowedIPMan)) {
                return "<b>Anda tidak bisa mengakses ini. Bukan Manager<b>";
            }
        }

        $data = [
            'status' => $status,
            'section' => $req->sec,
            'position' => 'Posisi',
            'qty' => '0',
            'reason' => "0aku",
            'docType' => 'Tipenya',
            'docNo' => $req->id . '-' . $req->no_plan,
            'docDate' => Carbon::parse($req->tanggal_plan)->format('d-m-Y'),
        ];

        // cek barcode smentara
        $barcode = "/$no";
        $barcodeFile = public_path("storage/barcodes/approval/1$no.png");
        if (!file_exists($barcodeFile)) {
            $generator = new BarcodeGeneratorPNG ();
            $barcode = $generator->getBarcode($barcode, $generator::TYPE_CODE_128);
            Storage::put('public/barcodes/approval/1' . $no . '.png', $barcode);
        }
        $req->barcode = $barcodeFile;
        echo $barcodeFile;
        // load pdf
        $fileName = "approval/Approval_" . $data['docNo'] . "_" . $data['docDate'] . ".pdf";
        $data['pdf'] = '';
        $pdfFile = url('storage/' . $fileName);
        
        // cek pdf
        // if(!file_exists($pdfFile)){
            Excel::store(new Generate($req), $fileName, 'public', \Maatwebsite\Excel\Excel::MPDF);
            $data['pdf'] = $pdfFile;
        // }

        
            
              

        return view('pages.single.approval', compact('data'));
    }

    public function approve(Request $req)
    {
        $docode = json_decode($req->data, true);
        $status = $docode['status'];
        if (!in_array($status, ['O', 'AP'])) {
            return false;
        }
        if (!in_array($status, ['O', 'AP'])) {
            return false;
        }

        $docno = explode('-', $docode['docNo']);
        $id = $docno[0];
        $no = $docno[1];

        $cek = DB::table(DB::raw('IT.dbo.PR_MASTER_PLAN'))
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
            $status = 'AP';
            $data['posisi'] = 'Supervisor';
            $data['pos'] = 'SPV';
        } elseif ($status == 'AP') {
            $data['posisi'] = 'Manager';
            $data['pos'] = 'Manager';
            $status = 'AM';
        }

        if ($shortStatus != $status) {

            DB::table(DB::raw('IT.dbo.PR_MASTER_PLAN'))
                ->where("id", $id)
                ->where("no_plan", $no)
                ->update([
                    'status' => $this->getstatus($status, 'Long')
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

                DB::table(DB::raw('IT.dbo.PR_pr'))
                    ->insert($insertData);
            } else {
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

        $docode = json_decode($req->data, true);
        $status = $docode['status'];
        if (!in_array($status, ['O', 'AP'])) {
            return false;
        }

        $docno = explode('-', $docode['docNo']);
        $id = $docno[0];
        $no = $docno[1];

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
            $statusT = 'OT';
            $data['posisi'] = 'Supervisor';
            $data['pos'] = 'SPV';
        } elseif ($status == 'AP') {
            $data['posisi'] = 'Manager';
            $data['pos'] = 'Manager';
            $statusT = 'APT';
        }

        if ($shortStatus != $statusT) {

            DB::table(DB::raw('IT.dbo.PR_MASTER_PLAN'))
                ->where("id", $id)
                ->where("no_plan", $no)
                ->update([
                    'status' => $this->getstatus($status . 'T', 'Long'),
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

                DB::table(DB::raw('IT.dbo.PR_pr'))
                    ->insert($insertData);
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
