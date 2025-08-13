<?php

namespace App\Exports;

use Mpdf\Mpdf;

class PackingExport
{
    public static function download($fileName = null, $dataFraction = [], $dataActual = [])
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => [58, 100], // 58mm x 100mm (bisa diganti panjangnya)
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 2,
            'margin_bottom' => 2,
        ]);


        $html = '
        <style>
            body { width: 100%; font-size: 8pt; font-family: sans-serif; }
            table {  border-collapse: collapse; margin: auto; }
            td { padding: 2px; }
        </style>
        <div style="border:1px solid black;width:98%;margin:auto;margin-bottom:6px;">
            
            <table>
                <tr>
                    <td colspan="2"><h6 style="text-align:center;border:1px solid black;width:60px;margin-left:2px;">FRACTION</h6></td>
                    <td rowspan="4" style="text-align:center;width:60px;"><b>' . $dataFraction['qr'] . '</b></td>
                </tr>
                <tr>
                    <td style="text-align:left;width:60px;">Material No</td>
                    <td style="text-align:left;width:70px;"><b>' . $dataFraction['material_no'] . '</b></td>
                </tr>
                <tr>
                    <td>Qty</td>
                    <td><b>' . $dataFraction['qty'] . '</b></td>
                </tr>
                <tr>
                    <td>Location</td>
                    <td><b>' . $dataFraction['location'] . '</b></td>
                </tr>
            </table>
            <div style="background: black;height:1px;width:100%;text-align:center">
                <span style="color: white;text-align:center">Ditempel</span>
            </div>
        </div>

        <div style="border:1px solid black;width:98%;margin:auto;margin-bottom:6px;">
            
            <table>
                <tr>
                    <td colspan="2"><h6 style="text-align:center;border:1px solid black;width:60px;margin-left:2px;">ACTUAL SUPPLY</h6></td>
                    <td rowspan="5" style="text-align:center;width:60px;"><b>' . $dataFraction['qr'] . '</b></td>
                </tr>
                <tr>
                    <td style="text-align:left;width:60px;">Material No</td>
                    <td style="text-align:left;width:70px;"><b>' . $dataFraction['material_no'] . '</b></td>
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
            </table>
        </div>
        ';

        // $mpdf->WriteHTML($html);
        return response()->streamDownload(function () use ($mpdf, $html) {
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
