<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Excel</title>
</head>
<body>
<h1>Show Excel</h1>

<form action="/process-excel" method="post" enctype="multipart/form-data">
    @csrf
    <input type="file" name="excel_file" accept=".xlsx, .xls, .csv">
    <button type="submit">Process Excel</button>
</form>

@if(isset($data))
    <table border="1">
        <thead>
        <tr>
            <th>Column 1</th>
            <th>Column 2</th>
            <th>Column 3</th>
            <th>Column 4</th>
            <th>Column 5</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data[0] as $row)
            <tr>
                @foreach($row as $cell)
                    <td>{{ $cell }}</td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
</body>
</html>
