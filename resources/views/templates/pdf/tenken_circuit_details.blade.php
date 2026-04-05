<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; height: 20px; }
        th { background: #f2f2f2; font-weight: bold; text-align: center; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 20px; text-transform: uppercase; }
        
        /* Layout tanda tangan */
        .footer-sign { margin-top: 50px; width: 100%; }
        .sign-box { text-align: center; width: 33%; float: left; }
        .sign-name { margin-top: 60px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="title">Detail TENKEN {{ $mesin }} {{ $nama_line }}</div>
    <div style="margin-bottom: 5px;">Tanggal: {{ $tanggal }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">No</th>
                <th style="width: 30%;">Item Pengecekan</th>
                <th style="width: 20%;">Metode Pengecekan</th>
                <th style="width: 20%;">Kriteria</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->item_pengecekan ?? '-' }}</td>
                <td>{{ $row->metode_pengecekan ?? '-' }}</td>
                <td>{{ $row->kriteria ?? '-' }}</td>
            </tr>
            @endforeach

        </tbody>
    </table>

</body>
</html>