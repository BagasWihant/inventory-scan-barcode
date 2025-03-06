<!DOCTYPE html>
<html>

<head>
    <title>Purchase Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.br th,
        table.br td {
            border: 1px solid rgb(175, 175, 175);
            padding: 5px;
            text-align: left;
        }

        .fs11 {
            font-size: 11px;
        }

        .fs10 {
            font-size: 10px;
        }

        .tc {
            text-align: center;
        }

        .header {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
            padding-top: 30px;
        }

        .signature {
            left: 50px;
            right: 50px;
            bottom: 20px;
            position: absolute;
        }

        .tab {
            padding-left: 10px;
            margin-left: 10px;
        }

        .fl {
            display: flex;
        }
    </style>
</head>

<body>
    <div class="header">
        PURCHASE REQUEST <br>
        NO: <span style="color: red">{{ $req->no_pr }}</span>
    </div>
    <table style="margin-bottom: 20px" width="100%">
        <tr>
            <td align="left">
                <table>
                    <tr>
                        <td width='100px'>Departemen</td>
                        <td>: {{ $req->sec }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>: {{ Carbon\Carbon::parse($req->tanggal_plan)->format('d-m-Y') }}</td>
                    </tr>
                </table>
            </td>
            <td width='10px'></td>
            <td align="left" width='150px'>
                <table>
                    <tr>
                        <td width='150px'>Diterima oleh Purchasing</td>
                        <td width="20">: </td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>: {{ Carbon\Carbon::parse($req->tanggal_pr)->format('d-m-Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="br">
        <thead>
            <tr>
                <th align="center" width='20px'>No</th>
                <th align="center" width='180px'>Uraian</th>
                <th align="center" width='150px'>No Item Master</th>
                <th align="center" width='20px'>Qty</th>
                <th align="center" width='50px'>Satuan</th>
                <th width='150px'>Tanggal Permintaan Kedatangan Barang</th>
                <th align="center">Keterangan</th>
            </tr>
        </thead>
        <tbody id="main">
            @foreach ($req->detail as $index => $item)
                <tr>
                    <td class="fs11" align="center">{{ $index + 1 }}</td>
                    <td class="fs11">{{ $item->item_brg }}</td>
                    <td class="fs11" align="center">{{ $item->item_id }}</td>
                    <td class="fs11" align="center">{{ $item->qty }}</td>
                    <td class="fs11" align="center">{{ $item->satuan }}</td>
                    <td class="fs11"> </td>
                    <td class="fs11" align="center">{{ $item->alasan_beli }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <table class="">
            <tr>
                <td align="left" width="200px">Dibuat oleh</td>
                <td width="80px"></td>
                <td align="left" width="200px">Diperiksa oleh</td>
                <td width="80px"></td>
                <td align="left" width="200px">Disetujui oleh</td>
            </tr>
            <tr>
                <td align="left">
                    @if (isset($req->signCode['creator']))
                        {!! $req->signCode['creator']['qrcode'] !!}
                    @else
                        <span><br><br><br><br><br></span>
                    @endif
                </td>
                <td></td>
                <td align="left">
                    @if (isset($req->signCode['spv']))
                        {!! $req->signCode['spv']['qrcode'] !!}
                    @endif
                </td>
                <td></td>
                <td align="left">
                    @if (isset($req->signCode['mgr']))
                        {!! $req->signCode['mgr']['qrcode'] !!}
                    @endif
                </td>

            </tr>
            <tr>
                <td align="left">
                    <table>
                        <tr>
                            <td style="width: 50px;">Nama</td>
                            <td>: {{ $req->signCode['creator']['name'] ?? '' }} </td>
                        </tr>
                        <tr>
                            <td style="width: 50px;">Tanggal</td>
                            <td>:
                                @if (isset($req->signCode['creator']['name']))
                                    {{ Carbon\Carbon::parse($req->tanggal_plan)->format('d-m-Y') }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td></td>

                <td align="left">
                    <table>
                        <tr>
                            <td style="width: 50px;">Nama</td>
                            <td>: {{ $req->signCode['spv']['name'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 50px;">Tanggal</td>
                            <td>:
                                @if ($req->tgl_diperiksa)
                                    {{ Carbon\Carbon::parse($req->tgl_diperiksa)->format('d-m-Y') }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td></td>

                <td align="left">
                    <table>
                        <tr>
                            <td style="width: 50px;">Nama</td>
                            <td>: {{ $req->signCode['mgr']['name'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 50px;">Tanggal</td>
                            <td>:
                                @if ($req->tgl_disetujui)
                                    {{ Carbon\Carbon::parse($req->tgl_disetujui)->format('d-m-Y') }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
