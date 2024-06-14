<table style="border: 2px;width: 100%">
    <thead>
    <tr>
        <th width="160px">Palet No</th>
        <th width="200px">Material No</th>
        <th width="120px">Pax</th>
        <th width="100px">Qty</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $invoice)
        <tr>
            <td>{{ $invoice->pallet_no }}</td>
            <td>{{ $invoice->material_no }}</td>
            <td align="left" >{{ $invoice->pax }}</td>
            <td align="left">{{ $invoice->qty }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
