<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Product Inventory Report</title>

    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
</head>
<body>
    <h3 class="text-center">Product Inventory Report</h3>
    <h4 class="text-center">
        As of {{ date('F j, Y') }}
    </h4>
    <h4 class="text-center">
        {{ $branch_name }}
    </h4>
    
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th>Product Name</th>
                <th>Purchase Price</th>
                <th>Selling Price</th>
                <th>Discount</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    @foreach ($row as $col)
                        <td>{{ $col }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>