<table>
    <thead>
        <tr>
            <th width='200px'>.</th>
            <th>.</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $k => $v)
            <tr>
                <td>{{ str_replace('_', ' ', $k) }}</td>
                <td>{{ $v }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
