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
    </style>
</head>

<body>
    <h4 class="header">FORM MAN POWER REQUEST(MPR)</h4>
    {{-- header --}}
    <table>
        <tr>
            <td>
                <table>
                    <tr>
                        <th>
                            Di isi oleh user
                        </th>
                        <th></th>
                    </tr>
                    <tr>
                        <td>
                            Date
                        </td>
                        <td>: {{ Carbon\Carbon::parse($data->req_date)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td>
                            Section
                        </td>
                        <td>: {{ $data->section }} </td>
                    </tr>
                    <tr>
                        <td>
                            Reason
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td width="90px"> {!! $data->subtitution == 1 ? '&#x2611;' : '&#x2610;' !!} Subtitution</td>
                                    <td>: {{ $data->subtitution == 1 ? $data->req_reason : '-' }} </td>
                                </tr>
                                <tr>
                                    <td> {!! $data->increase == 1 ? '&#x2611;' : '&#x2610;' !!} Increase</td>
                                    <td>: {{ $data->increase == 1 ? $data->req_reason : '-' }}
                                    <td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
            <td>
                <table>
                    <tr>
                        <th colspan="2">
                            Di isi oleh HR
                        </th>
                    </tr>
                    <tr>
                        <td width="100px">
                            No. MPR
                        </td>
                        <td>: {{ $data->no_doc }} </td>
                    </tr>
                    <tr>
                        <td>
                            Receive Date
                        </td>
                        <td>: {{ $data->hr_recieved == null ? '' : $data->hr_recieved }} </td>
                    </tr>
                    <tr>
                        <td>
                            Join Date
                        </td>
                        <td>: {{ Carbon\Carbon::parse($data->due_date)->format('d-m-Y') }} </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    {{-- header --}}

    <table class="brr">
        <tr>
            <th colspan="2" align="center">JOB REQUEREMENT</th>
        </tr>
        {{-- isi tabel --}}
        <tr>
            <td colspan="2" align="left" class="no-border">
                <table class="no-border">
                    <tr>
                        <th colspan="2">Di isi oleh user</th>
                    </tr>
                    <tr>
                        <td width="190px">No of Candidates</td>
                        <td>: {{ $data->qty }} </td>
                    </tr>
                    <tr>
                        <td colspan="2"><i class="fs10">(Untuk Level Operator Tuliskan jumlah yang dibutuhkan untuk
                                staff up ditulis 1/Satu persatu)</i></td>
                    </tr>
                    <tr>
                        <td width="190px">Position</td>
                        <td>: {{ $data->position }} </td>
                    </tr>
                    <tr>
                        <td width="100px">Job Location</td>
                        <td>: {!! $data->plant == 'KIAS 2'
                            ? '&nbsp;&#x2610; KIAS 1 &nbsp;&nbsp;&nbsp;&#x2611; KIAS 2'
                            : '&nbsp;&#x2611; KIAS 1 &nbsp;&nbsp;&nbsp;&#x2610; KIAS 2' !!}

                        </td>
                    </tr>
                    <tr>
                        <td width="190px">Special Requirement</td>
                        <td>: @foreach ($data->spesial as $spesial)
                                {!! $loop->iteration > 1
                                    ? '&nbsp;&nbsp;' . $loop->iteration . '. ' . $spesial->key
                                    : $loop->iteration . '. ' . $spesial->key !!}<br>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td width="190px">Comment</td>
                        <td>: {{ $data->req_reason }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        {{-- isi tabel --}}
        {{-- TTD --}}
        <tr>
            <td colspan="2" align="left" class="no-border">
                <table class="no-border">
                    <tr>
                        <th colspan="2">APPROVAL</th>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table class="brr">
                                <tr>
                                    <th align="center">Request By</th>
                                    <th align="center">Acknowledge By</th>
                                    <th align="center" colspan="2">Approved By</th>
                                    <th align="center">Received By</th>
                                </tr>
                                <tr>
                                    <td height='80px' align="center"> {!! $data->req_by == null
                                        ? ''
                                        : str_replace(
                                            '<?xml version="1.0" encoding="UTF-8"?>',
                                            '',
                                            SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->generate("$data->req_by/$data->req_name/$data->no_doc/$data->req_date"),
                                        ) !!} </td>
                                    <td height='80px' align="center"> {!! $data->checked_by == null
                                        ? ''
                                        : str_replace(
                                            '<?xml version="1.0" encoding="UTF-8"?>',
                                            '',
                                            SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->generate("$data->checked_by/$data->checked_name/$data->no_doc/$data->req_date"),
                                        ) !!} <br>
                                        {{ $data->checked_judgement }}</td>
                                    <td height='80px' align="center"> {!! $data->approved1_by == null
                                        ? ''
                                        : str_replace(
                                            '<?xml version="1.0" encoding="UTF-8"?>',
                                            '',
                                            SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->generate("$data->approved1_by/$data->apr1_name/$data->no_doc/$data->approved1_date"),
                                        ) !!} <br>
                                        {{ $data->approved1_judgement }}</td>
                                    <td height='80px' align="center"> {!! $data->approved2_by == null
                                        ? ''
                                        : str_replace(
                                            '<?xml version="1.0" encoding="UTF-8"?>',
                                            '',
                                             SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->generate("$data->approved2_by/$data->apr2_name/$data->no_doc/$data->approved2_date"),
                                        ) !!} <br>
                                        {{ $data->approved2_judgement }}</td>
                                    <td height='80px' align="center"> {!! $data->hr_by == null
                                        ? ''
                                        : str_replace(
                                            '<?xml version="1.0" encoding="UTF-8"?>',
                                            '',
                                             SimpleSoftwareIO\QrCode\Facades\QrCode::size(50)->generate("$data->hr_by/$data->hr_name/$data->no_doc/$data->hr_recieved"),
                                        ) !!} <br>
                                        {{ $data->hr_status }}</td>
                                </tr>
                                <tr>
                                    <td align="center">{{ $data->req_by == null ? '' : substr($data->req_name,0, 15) }}</td>
                                    <td align="center">{{ $data->checked_by == null ? '' : substr($data->checked_name,0, 15) }}</td>
                                    <td align="center">Handoko R</td>
                                    <td align="center">Budi Eko</td>
                                    <td align="center">{{ $data->hr_by == null ? '' : substr($data->hr_name,0, 15) }}</td>
                                </tr>
                                <tr>
                                    <td align="center">Section Head</td>
                                    <td align="center">Dept. Head</td>
                                    <td align="center">Director</td>
                                    <td align="center">Director</td>
                                    <td align="center">HRD</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        <tr>
            {{-- TTD --}}
            {{-- List Karyawan --}}
        <tr>
            <td colspan="2">
                <table class='no-border'>
                    <tr>
                        <th colspan="3">*Karyawan Subtitution (Nama, NIK)</th>
                    </tr>
                    @for ($i = 0; $i < $data->countsubs; $i++)
                    <tr>
                        @foreach ($data->subs[$i] as $sub)
                                <td width="150px" align="center"> 
                                    {{ $sub->replace ?? '' }} - {{ $sub->emp_nm ?? '' }} 
                                    {{-- {{json_encode($sub)}} --}}
                                </td>
                            @endforeach
                        </tr>
                    @endfor
                </table>
            </td>
        </tr>
        {{-- List Karyawan --}}
        <tr>
            <td colspan="2">
                <table class="no-border">
                    <tr>
                        <th align="left">Note :</th>
                    </tr>
                    <tr>
                        <td><span class="fs11">

                                - Job Specification akan disesuaikan dengan standard masing-masing job<br>
                                - Jika terdapat requirement tambahan diluar dari standard job specification maka
                                tuliskan pada bagian special requirement<br>
                                - Jika man power request dilakukan untuk subtitution maka isi nama, NIK Karyawan yang
                                akan di replace.<br>
                                - Jika terdapat karyawan resign, admin/Spv wajib melampirkan MPR jika karyawan tersebut
                                perlu di replace<br>
                                &nbsp;&nbsp;(Jika tidak menyertakan MPR maka user tidak membutuhkan replacement
                                karyawan/Recruit internal/Mutasi)<br>
                                - Jika pada evaluasi karyawan perlu di replace, maka user wajib melampirkan MPR.<br>
                                &nbsp;&nbsp;(Jika tidak menyertakan MPR maka user tidak membutuhkan replacement
                                karyawan/Recruit internal/Mutasi)<br>
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
