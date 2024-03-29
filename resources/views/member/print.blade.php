<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Member Card</title>

    <style>
        .box {
            position: relative;
        }
        .card {
            width: 85.60mm;
        }
        .title {
            position: absolute;
            top: 3pt;
            right: 0pt;
            font-size: 16pt;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            color: #fff !important;
        }
        .title p {
            text-align: right;
            margin-right: 16pt;
        }
        .logo {
            position: absolute;
            top: 10pt;
            right: 0pt;
            font-size: 16pt;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            color: #fff !important;
        }
        .logo img {
            position: absolute;
            margin-top: -5pt;
            right: 150pt;
            width: 100px;
            height: 100px;
            right: 16pt;
        }
        .name {
            position: absolute;
            top: 85pt;
            right: 16pt;
            font-size: 12pt;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            color: #fff !important;
        }
        .phone {
            position: absolute;
            top: 100pt;
            right: 16pt;
            text-align: left;
            color: #fff;
        }
        .barcode {
            position: absolute;
            top: 105pt;
            left: .860rem;
            border: 1px solid #fff;
            padding: .5px;
            background: #fff;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <section style="border: 1px solid #fff">
        <table width="100%">
            @foreach ($datamember as $key => $data)
                <tr>
                    @foreach ($data as $item)
                        <td class="text-center">
                            <div class="box">
                                <div class="logo">
                                    <img src="{{ public_path($setting->path_logo) }}" alt="logo">
                                </div>
                                <img src="{{ public_path($setting->member_card_path) }}" alt="card" width="100%">
                                <div class="name">{{ $item->name  }}</div>
                                <div class="phone">{{ $item->phone }}</div>
                               
                            </div>
                        </td>
                        
                        @if (count($data) == 1)
                        <td class="text-center" style="width: 50%;"></td>
                       @endif
                    @endforeach
                </tr>
            @endforeach
        </table>
    </section>
</body>
</html>