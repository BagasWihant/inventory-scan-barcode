<?php

namespace App\Exports;

use Mpdf\Mpdf;

class PackingExport
{
    public static function download($fileName = null, $dataFraction = [], $dataActual = [])
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => [72, 50],
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 2,
            'margin_bottom' => 2,
        ]);


        $fraction = '
        <style>
            @page { size: 72mm 50mm; margin: 4px; }
            body { width: 100%; font-size: 8pt; font-family: sans-serif; }
            table {  border-collapse: collapse; margin: auto; }
            td { padding: 1px; }
        </style>
        <div style="border:1px solid black;width:98%;margin:auto;padding-top:6px">
            
            <table>
                <tr>
                    <td colspan="2"><h6 style="text-align:center;border:1px solid black;width:100px;margin-left:2px;">FRACTION</h6></td>
                </tr>
                <tr>
                    <td style="text-align:left;width:80px;">Material No</td>
                    <td style="text-align:left;width:100px;"><b>' . $dataFraction['material_no'] . '</b></td>
                </tr>
                <tr>
                    <td>Qty</td>
                    <td><b>' . $dataFraction['qty'] . '</b></td>
                </tr>
                <tr>
                    <td>Location</td>
                    <td><b>' . $dataFraction['location'] . '</b></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;width:100px;height:100px"><b>' . $dataFraction['qr'] . '</b></td>
                </tr>
            </table>

            <div style="background: black;height:1px;width:100%;text-align:center">
                <span style="color: white;text-align:center">Ditempel</span>
            </div>
        </div>';

        $actual = '<div style="border:1px solid black;width:98%;margin:auto;padding-top:6px">
            
            <table>
                <tr>
                    <td colspan="2"><h6 style="text-align:center;border:1px solid black;width:120px;margin-left:2px;">ACTUAL SUPPLY</h6></td>
                </tr>
                <tr>
                    <td style="text-align:left;width:80px;">Material No</td>
                    <td style="text-align:left;width:130px;"><b>' . $dataFraction['material_no'] . '</b></td>
                </tr>
                <tr>
                    <td>Issue Qty</td>
                    <td><b>' . $dataActual['qty'] . '</b></td>
                </tr>
                <tr>
                    <td>Kit Issue</td>
                    <td><b>' . $dataActual['kit'] . '</b></td>
                </tr>
                <tr>
                    <td>Location</td>
                    <td><b>' . $dataFraction['location'] . '</b></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;width:100px;height:100px"><b>' . $dataActual['qr'] . '</b></td>
                </tr>
            </table>
        </div>
        ';

        $filePath = storage_path('app/public/packing/' . trim($fileName).'.pdf');
        $mpdf->WriteHTML($fraction);
        $mpdf->AddPage();
        $mpdf->WriteHTML($actual);
        $mpdf->Output($filePath, 'F');

        // return filepath
        return asset('storage/packing/' . trim($fileName).'.pdf');

        // jika download langsung
        // return response()->streamDownload(function () use ($mpdf, $html) {
        //     $mpdf->WriteHTML($html);
        //     $mpdf->Output();
        // }, $fileName, [
        //     'Content-Type' => 'application/pdf',
        // ]);
    }
}
