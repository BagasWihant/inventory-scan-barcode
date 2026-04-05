<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; height: 20px; }
        th { background: #f2f2f2; font-weight: bold; text-align: center; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 20px; text-transform: uppercase; }
        
        .footer-sign { margin-top: 50px; width: 100%; }
        .sign-box { text-align: center; width: 33%; float: left; }
        .sign-name { margin-top: 60px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="title">Pengajuan approval TENKEN {{ $mesin }}</div>
    <div style="margin-bottom: 5px;">Tanggal: {{ $tanggal }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Nama line</th>
                <th style="width: 20%;">Nama member</th>
                <th style="width: 20%;">Leader Approve PE</th>
                <th style="width: 30%;">Tanggal Approve leader PE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row['nama_line'] ?? '-' }}</td>
                <td>{{ $row['nama_member'] ?? '-' }}</td>
                <td style="text-align: center;">{{ $row['leader_approve_pe'] ?? '-' }}</td>
                <td>{{ $row['tanggal_approve_leader_pe'] ?? '-' }}</td>
            </tr>
            @endforeach

        </tbody>
    </table>

    <div class="footer-sign">
        <div class="sign-box">
            <div>Foreman</div>
            <div class="sign-name">________________</div>
        </div>
        <div class="sign-box">
            <div>Spv</div>
            <div class="sign-name">________________</div>
        </div>
        <div class="sign-box">
            <div>Manager</div>
            <div class="sign-name">________________</div>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>