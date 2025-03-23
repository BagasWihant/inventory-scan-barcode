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
use setasign\Fpdi\PdfReader\PdfReader;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SinglePage extends Controller
{
    public $typeDoc;
    private function typeDokumen($type)
    {
        switch ($type) {
            case '2':
                return 'Man Request';
                break;
            case '1':
                return 'PR System';
                break;
        }
    }
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
    private function generatePdf($url, $html, $lampiran = null)
    {
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
        if (!empty($lampiran)) {
            foreach ($lampiran as $lamp) {
                $mpdf->AddPage();
                // $pdf = asset('assets/syarat-dan-ketentuan-layanan-utama.pdf');
                $pdf = $lamp;

                $pageCount = $mpdf->setSourceFile($pdf);

                for ($i = 1; $i <= $pageCount; $i++) {
                    $tplId = $mpdf->importPage($i);
                    $mpdf->useTemplate($tplId);
                    if ($i < $pageCount) {
                        $mpdf->AddPage();
                    }
                }
            }
        }

        $mpdf->Output($url, Destination::FILE);
    }
    public function approval($type, $no)
    {
        $validator = Validator::make(
            ['type' => $type],
            ['type' => 'required|integer']
        );

        if ($validator->fails()) {
            abort(400, 'ID HARUS ANGKA'); // Atau bisa menggunakan dd('Invalid ID') untuk debug
        }

        switch ($type) {
            case '2':
                $data = $this->approvalMan($type, $no);
                return view('pages.single.approval-man-request', compact('data'));
                break;
            case '1':
                $req = $this->approvalPRSys($type, $no);
                return view('pages.single.approval', compact('req'));
                break;

            default:
                # code...
                break;
        }
    }

    public function approve(Request $req)
    {
        $decode = json_decode($req->data);

        $status = $decode->status;
        if (!in_array($status, ['O', 'AP', 'AS'])) {
            return false;
        }

        $id = $decode->id;
        $no = $decode->no_plan;

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

        // Tak buat simpel 
        $positions = [
            'O'  => ['next' => 'AP', 'posisi' => 'Purchasing', 'pos' => 'Purchasing'],
            'AP' => ['next' => 'AS', 'posisi' => 'Supervisor', 'pos' => $decode->spv1],
            'AS' => ['next' => 'AM', 'posisi' => 'Manager', 'pos' => $decode->mgr]
        ];

        $nextStatus = $positions[$status]['next'];
        $data['posisi'] = $positions[$status]['posisi'];
        $data['pos'] = $positions[$status]['pos'];


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

            // get new data after update
            $req = DB::table('IT.dbo.PR_MASTER_PLAN as m')
                ->where("m.id", $id)
                ->where("m.no_plan", $no)
                ->leftJoin('IT.dbo.PR_approval_hirarki as h', 'm.sec', '=', 'h.section')
                ->leftJoin('IT.dbo.PR_pr as pr', 'm.id', '=', 'pr.id_no_plan')
                ->selectRaw('pr.*,h.*,m.*')
                ->first();

            $sign = [];

            if ($status !== 'O') {
                $QR = "$req->nik_spv1/$req->spv1/$req->tgl_diperiksa/$req->no_pr";
                $sign['spv'] = [
                    'qrcode' => str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($QR)),
                    'name' => $req->spv1
                ];
            }

            if ($status === 'AS') {
                $QR = "$req->nik_mgr/$req->mgr/$req->tgl_disetujui/$req->no_pr";
                $sign['mgr'] = [
                    'qrcode' => str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($QR)),
                    'name' => $req->mgr
                ];

                $QR = "$req->nik_diterima/$req->diterima/$req->tgl_diterima/$req->no_pr";
                $sign['terima'] = [
                    'qrcode' => str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($QR)),
                    'name' => $req->diterima
                ];
            }

            $QR = "NIK/$req->nama/$req->tanggal_plan/$req->no_pr";
            $sign['creator'] = [
                'qrcode' => str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($QR)),
                'name' => $req->nama
            ];

            $req->signCode = $sign;

            // file pdf
            $fileName = $decode->docNo . '.pdf';
            $directory = storage_path('app/public/approval/pdf');
            $path = $directory . '/' . $fileName;
            $req->pdf = Storage::url('approval/pdf/' . $fileName);
            $req->detail = $decode->detail;

            // generatePdf
            $html = view('templates.pdf.approval-generate', compact('req'))->render();
            $this->generatePdf($path, $html);

            $data['pdf'] = $req->pdf;
            $data['text'] = 'Berhasil disetujui oleh';
            $data['status'] = '1';
        } else {
            $data['pdf'] = '';
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

    public function approvalPRSys($type, $no)
    {
        $req = DB::table('IT.dbo.PR_MASTER_PLAN as m')
            ->where("m.no_plan", $no)
            ->leftJoin('IT.dbo.PR_approval_hirarki as h', 'm.sec', '=', 'h.section')
            ->leftJoin('IT.dbo.PR_pr as pr', 'm.id', '=', 'pr.id_no_plan')
            ->selectRaw('pr.*,h.*,m.*')
            ->first();

        $detail = DB::table('IT.dbo.PR_detail_plan as d')
            ->where("id_master_plan", $req->id)->get();
        $req->detail = $detail;

        $docNo      = "ID-$req->sec-$req->no_plan";
        $req->docNo = $docNo;

        $status      = $this->getstatus($req->status, 'Short');
        $req->status = $status;

        $req->type = $this->typeDokumen($type);


        if ($status == 'O') {
            // halaman approve purchasing
            $allowedPurchase = [
                '127.0.0.1',
                '192.168.1.249'
            ];

            if (!in_array(request()->ip(), $allowedPurchase)) {
                return "<b>Anda tidak bisa mengakses ini. Bukan Purchasing<b>";
            }
        } elseif ($status == 'AP') {
            // halaman spv
            $allowedSPV = [
                '127.0.0.1',
                '192.168.1.249'
            ];

            if (!in_array(request()->ip(), $allowedSPV)) {
                return "<b>Anda tidak bisa mengakses ini. Bukan Supervisor<b>";
            }
        } elseif ($status == 'AS') {
            // halaman manager
            $allowedIPMan = [
                '127.0.0.1',
                '192.168.1.249'
            ];

            if (!in_array(request()->ip(), $allowedIPMan)) {
                return "<b>Anda tidak bisa mengakses ini. Bukan Manager<b>";
            }
        }

        // save qrcode
        $allow = ['spv1', 'mgr'];
        $sign = [];

        // Setting nilai QRCODE
        // Setting nama pembuat
        if ($status != 'O') {

            $valQR = "NIK/$req->nama/$req->tanggal_plan/$req->no_pr";
            $sign['creator']['qrcode'] = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($valQR));
            $sign['creator']['name'] = $req->nama;
        }

        // SPV
        if ($req->tgl_diperiksa != null) {
            $valQR = "$req->nik_spv1/$req->spv1/$req->tgl_diperiksa/$req->no_pr";
            $sign['spv']['qrcode'] = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($valQR));
            $sign['spv']['name'] = $req->spv1;
        }

        // MGR
        if ($req->tgl_disetujui != null) {
            $valQR = "$req->nik_mgr/$req->mgr/$req->tgl_disetujui/$req->no_pr";
            $sign['mgr']['qrcode'] = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($valQR));
            $sign['mgr']['name'] = $req->mgr;

            $QR = "$req->nik_diterima/$req->diterima/$req->tgl_diterima/$req->no_pr";
            $sign['terima'] = [
                'qrcode' => str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', QrCode::size(50)->generate($QR)),
                'name' => $req->diterima
            ];
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

            $this->generatePdf($path, $html);
        }

        $req->pdf = Storage::url('approval/pdf/' . $fileName);
        return $req;
    }

    public function approvalMan($type, $id)
    {
        $hrmpr = DB::table('IT.dbo.HR_MPR as main')
            ->select(
                'main.*',
                'aprv.*',
                'cek.emp_nm as checked_name',
                'req.emp_nm as req_name',
                'apr1.emp_nm as apr1_name',
                'apr2.emp_nm as apr2_name',
                'hr.emp_nm as hr_name'
            )
            ->join('IT.dbo.HR_MPR_Approval as aprv', 'main.id', '=', 'aprv.id')
            ->leftJoin('IT.dbo.HR_GM_Emp_mst as req', 'main.req_by', '=', 'req.emp_id')
            ->leftJoin('IT.dbo.HR_GM_Emp_mst as cek', 'aprv.checked_by', '=', 'cek.emp_id')
            ->leftJoin('IT.dbo.HR_GM_Emp_mst as apr1', 'aprv.approved1_by', '=', 'apr1.emp_id')
            ->leftJoin('IT.dbo.HR_GM_Emp_mst as apr2', 'aprv.approved2_by', '=', 'apr2.emp_id')
            ->leftJoin('IT.dbo.HR_GM_Emp_mst as hr', 'aprv.hr_by', '=', 'hr.emp_id')
            ->where('main.id', $id)->first();
        $data = $hrmpr;

        // spesial
        $spesial = DB::table('IT.dbo.mpr_special_requirement')
            ->select('special_requirement as key')
            ->whereNotNull('special_requirement')
            ->whereMprRecId($id)->get('special_requirement');
        $data->spesial = $spesial;

        // substitute
        $subs = DB::table('IT.dbo.mpr_substitution as sb')
            ->select('sb.replace', 'gm.emp_nm')
            ->join('IT.dbo.HR_GM_Emp_mst as gm', 'sb.replace', '=', 'gm.emp_id')
            ->whereMprRecId($id)->get();
        $subs = $subs->chunk(3);
        $data->subs = $subs;
        $data->countsubs = count($subs);

        // $->type = $this->typeDokumen($type);
        $fileName = str_replace('/', '-', $hrmpr->no_doc) . '.pdf';
        $directory = storage_path('app/public/approval/pdf');
        $path = $directory . '/' . $fileName;

        if (!file_exists($directory)) {
            mkdir($directory, 0775, true);
        }
        $html = view('templates.pdf.approval-man-request-generate', compact('data'))->render();

        // Lampiran
        $lampiran = DB::table('IT.dbo.HR_MPR_filepath')->where('idMpr', $id)->get()->pluck('FilePath');

        $this->generatePdf($path, $html);

        $data->pdf = Storage::url('approval/pdf/' . $fileName);
        $data->type = $this->typeDokumen($type);
        $data->doc_no = $hrmpr->no_doc;
        $data->doc_date = $hrmpr->req_date;
        return $data;
    }
}
