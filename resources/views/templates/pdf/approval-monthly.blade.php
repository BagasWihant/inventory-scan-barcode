<!DOCTYPE html>
<html>

<head>
    <title>PR System</title>
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

        table.brr th,
        table.brr td {
            border: 1px solid rgb(100, 100, 100);
            padding: 5px;
            text-align: left;
        }

        table.no-border th,
        table.no-border td {
            border: 0px solid rgb(255, 255, 255) !important;
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

        .credit {
            color: red;
        }
        .debit {
            color: blue;
        }

        .no-cr{
            color: #727272ff;
        }
    </style>
</head>

<body>
    <table class="br">
        <tr>

            <th>Bulan</th>
            <th>no</th>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Debt/Cred</th>
            <th>Fix Asset</th>
            <th>Non Fix Asset</th>
            <th>Expense</th>
        </tr>
        
            <tr>
                <td>apr-25</td>
                <td>isi no</td>
                <td>isi Tanggal</td>
                <td>isi Keterangan</td>
                <td class="credit">credit</td>
                <td class="credit">isi Fix Asset</td>
                <td class="credit">isi Non Fix Asset</td>
                <td class="credit">isi Expense</td>

            </tr>
            <tr>
                <td>apr-25</td>
                <td>isi no</td>
                <td>isi Tanggal</td>
                <td>isi Keterangan</td>
                <td class="debit">debit</td>
                <td class="debit">isi Fix Asset</td>
                <td class="debit">isi Non Fix Asset</td>
                <td class="debit">isi Expense</td>

            </tr>
            <tr>
                <td>apr-25</td>
                <td>isi no</td>
                <td>isi Tanggal</td>
                <td>isi Keterangan</td>
                <td class="no-cr"></td>
                <td class="no-cr">isi Fix Asset</td>
                <td class="no-cr">isi Non Fix Asset</td>
                <td class="no-cr">isi Expense</td>

            </tr>
    </table>
</body>

</html>
