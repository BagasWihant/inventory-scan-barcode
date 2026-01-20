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
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfReader\PdfReader;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SinglePage extends Controller
{
    public $typeDoc;
    private function typeDokumen($type)
    {
        switch ($type) {
            case '1':
                return 'Man Request';
                break;
            case '2':
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
    private function generatePdf($url, $html, array $images = [], array $lampiran = [])
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
                // ini untuk cek jika pdf dari db pakai varbinary
                if(strncmp($lamp,'%PDF',4) === 0){
                    $reader    = StreamReader::createByString($lamp);
                    $pageCount = $mpdf->setSourceFile($reader);

                    for ($page = 1; $page <= $pageCount; $page++) {
                        $tplId = $mpdf->importPage($page);
                         $pd = $mpdf->getTemplateSize($tplId); 
                         $orien = $pd['width'] > $pd['height'] ? 'L' : 'P'; 
                         $mpdf->AddPage($orien);
                         $mpdf->useTemplate($tplId);
                    }
                }else{
                    $mpdf->AddPage();
                    $httml = $lamp;
                    $mpdf->WriteHTML($httml);
                }
            }
        }
        if (!empty($images)) {
            foreach ($images as $img) {
                if(empty($img)) continue;
                $mpdf->AddPage();
                $base64 = $this->convertBase64toImg($img);
                $mpdf->WriteHTML('<img src="' . $base64 . '" style="width:100%; max-width:750px;" />');
            }
        }

        $mpdf->Output($url, Destination::FILE);
    }

    /**
     * 
     * ubah dari base64 ke gambar
     * 
     * di database simpan base64 aja, kedepan kalo mau  insert dari web mudah
     */
    private function convertBase64toImg(string $rawBase64, string $mime = 'image/png'): string
    {
        //return 'data:' . $mime . ';base64,' . $rawBase64;
        return $rawBase64;
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
            case '1':
                $data = $this->approvalMan($type, $no);
                if(!empty($data->msg)){
                    $data = (array)$data;
                    return view('errors.custom-error',compact('data'));
                }
                return view('pages.single.approval-man-request', compact('data'));
            case '2':
                $req = $this->approvalPRSys($type, $no);
                if(!empty($req->msg)){
                    $req = (array)$req;
                    return view('errors.custom-error',compact('req'));
                }
                return view('pages.single.approval', compact('req'));

            default:
                # code...
                break;
        }
    }

    public function approve(Request $req, $type)
    {
        if ($type == 1) {
            $decode = json_decode($req->data);
            $no = $decode->id;
            $approvalSteps = [
                'checked_date' => 'Checker Approve',
                'approved1_date' => 'Approved1',
                'approved2_date' => 'Approved2',
                'hr_recieved' => 'Recieved'
            ];

            foreach ($approvalSteps as $field => $param) {

                if (empty($decode->$field)) {
                    DB::connection('it')->statement("EXEC sp_hr_mpr_email_web ?, ?, ?", [$param, $decode->no_doc, '']);

                    $getData = $this->approvalMan($type, $no);

                    $data['pdf'] = $getData->pdf;
                    $data['text'] = $getData->status;
                    $data['status'] = '';
                    $data['posisi'] = '';
                    
                    if(!empty($getData->msg)){
                        $data['msg'] = $getData->msg;
                        return view('errors.custom-error',compact('data'));
                    }

                    return view('pages.single.approval-response', compact('data'));
                }
            }
        }
        if ($type == 2) {


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
                        'status' => $this->getstatus($nextStatus, 'Long'),
                        'alasan_approve' => $req->message
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

                // ambil file yang ada di pr masterplan
                if($cek->pdf_data){
                    $page2 = $cek->pdf_data;
                }
                // generatePdf
                $html = view('templates.pdf.approval-generate', compact('req'))->render();
                $page2 = view('templates.pdf.approval-monthly')->render();

                $this->generatePdf($path, $html, [], [$page2]);

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
    }


    public function reject(Request $req, $type)
    {
        if ($type == 1) {
            $decode = json_decode($req->data);
            $id = $decode->id;

            $approvalSteps = [
                'checked_date' => 'Checker Reject',
                'approved1_date' => 'Rejected1',
                'approved2_date' => 'Rejected2',
            ];
            foreach ($approvalSteps as $field => $param) {

                if (empty($decode->$field)) {
                    DB::connection('it')->statement("EXEC sp_hr_mpr_email_web ?, ?, ?", [$param, $decode->no_doc, '']);

                    $getData = $this->approvalMan($type, $id);

                    $data['pdf'] = $getData->pdf;
                    $data['text'] = $getData->status;
                    $data['status'] = '0';
                    $data['posisi'] = '';
                     
                    if(!empty($getData->msg)){
                        $data['msg'] = $getData->msg;
                        return view('errors.custom-error',compact('data'));
                    }


                    return view('pages.single.approval-response', compact('data'));
                }
            }
        }
        if ($type == 2) {
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

        //kolom di tabel nya
        $images = $detail->pluck('image')->toArray();

        $req->detail = $detail;

        $docNo      = "ID-$req->sec-$req->no_plan";
        $req->docNo = $docNo;

        $status      = $this->getstatus($req->status, 'Short');
        $req->status = $status;

        $req->type = $this->typeDokumen($type);
        $err = '';

        if ($status == 'O') {
            // halaman approve purchasing
            $allowedPurchase = [
                '172.99.0.254',
                '192.168.1.249'
            ];

            if (!in_array(request()->ip(), $allowedPurchase)) {
                $err = "Anda tidak bisa mengakses ini. Bukan Purchasing";
            }
        } elseif ($status == 'AP') {
            // halaman spv
            $allowedSPV = [
                '172.99.0.254',
                '192.168.1.249'
            ];

            if (!in_array(request()->ip(), $allowedSPV)) {
                $err = 'Anda tidak bisa mengakses ini. Bukan Supervisor';
            }
        } elseif ($status == 'AS') {
            // halaman manager
            $allowedIPMan = [
                '172.99.0.254',
                '192.168.1.249'
            ];

            if (!in_array(request()->ip(), $allowedIPMan)) {
                $err = "Anda tidak bisa mengakses ini. Bukan Manager";
            }
        }
        
        $fileName = $docNo.'_'.$status . '.pdf';
        $directory = storage_path('app/public/approval/pdf');
        $path = $directory . '/' . $fileName;

        /**
         * pindah sini, jika status masih sama, gausah buat pdf lagi
         * kalo reload2 biar langsung ambil pdf,
         * beda setatus baru buat pdf
         */

        // if (file_exists($path)) {
        //     $req->pdf = Storage::url('approval/pdf/' . $fileName);
        //     return $req;
        // }

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

        if (!file_exists($directory)) {
            mkdir($directory, 0775, true);
        }

        // if (!file_exists($path)) {
        $html = view('templates.pdf.approval-generate', compact('req'))->render();

        $page2 = view('templates.pdf.approval-monthly')->render();

        // pdf data
        if(isset($req->pdf_data) && $req->pdf_data !== null) {
            $lampiran = [$req->pdf_data];
        }else{
            $lampiran = [$page2];
        }
        
        $this->generatePdf($path, $html, $images, $lampiran);

        $req->pdf = Storage::url('approval/pdf/' . $fileName);
        $req->msg = $err;
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
                'apr1.emp_nm as approved1_name',
                'apr2.emp_nm as approved2_name',
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
        $err = '';

        if(empty($data->checked_date)){
            // ganti nama jabatan yang sesuai
            $allowedIP = DB::table('ip_conf')->where('jabatan', 'checker')->get()->pluck('ip')->toArray();
            
            if (!in_array(request()->ip(), $allowedIP)) {
                $err = "Anda tidak bisa mengakses ini. Hubungi IT";

            }
        }else if(empty($data->approved1_date)){
            // ganti nama jabatan yang sesuai
            $allowedIP = DB::table('ip_conf')->where('jabatan', 'Approv1')->get()->pluck('ip')->toArray();

            if (!in_array(request()->ip(), $allowedIP)) {
                $err = "Anda tidak bisa mengakses ini. Hubungi IT";
            }
        }else if(empty($data->approved2_date)){
            // ganti nama jabatan yang sesuai
            $allowedIP = DB::table('ip_conf')->where('jabatan', 'Approv2')->get()->pluck('ip')->toArray();

            if (!in_array(request()->ip(), $allowedIP)) {
                $err = "Anda tidak bisa mengakses ini. Hubungi IT";
            }
        }else if(empty($data->hr_recieved)){
            // ganti nama jabatan yang sesuai
            $allowedIP = DB::table('ip_conf')->where('jabatan', 'HR')->get()->pluck('ip')->toArray();

            if (!in_array(request()->ip(), $allowedIP)) {
                $err = "Anda tidak bisa mengakses ini. Hubungi IT";
            }
        }

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
        

        // get blob sementara  ambil random
        $blob = DB::table('IT.dbo.HR_MPR_filepath')->first();
        $tmpPath = null;
        if(isset($blob->fileName) && $blob->fileName) {
            // simpan ke tmp
            $tmpPath = storage_path('app/' . $blob->fileName);
            file_put_contents($tmpPath, $blob->fileByte);
        }

        $this->generatePdf($path, $html, [$tmpPath]);

        $data->pdf = Storage::url('approval/pdf/' . $fileName);
        $data->type = $this->typeDokumen($type);
        $data->doc_no = $hrmpr->no_doc;
        $data->doc_date = $hrmpr->req_date;
        $data->msg = $err;
        return $data;
    }
}
