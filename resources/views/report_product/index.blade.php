@extends('layouts.master')

@section('title')
Product Income Report ({{ us_date($startDate, false) }} - {{ us_date($endDate, false) }})
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Report</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="updatePeriode()" class="btn btn-primary btn-flat"><i class="fa fa-plus-circle"></i> Change Date</button>
                <a href="{{ route('report_product.export_pdf', [$branch_id,$startDate, $endDate]) }}" target="_blank" class="btn btn-success btn-flat"><i class="fa fa-file-excel-o"></i> Export PDF</a> 
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-sales table-hover">
                    <thead>
                        <th width="5%">#</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Selling Price</th>
                        <th>Purchase Price</th>
                        <th>Gross Profit</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('report_product.form')
@endsection

@push('scripts')
<script src="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    let table;

    $(function () {
        table = $('.table-sales').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('report_product.data', [$branch_id,$startDate, $endDate]) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'date'},
                {data: 'branch'},
                {data: 'product_name'},
                {data: 'quantity'},
                {data: 'subtotal'},
                {data: 'selling_price'},
                {data: 'purchase_price'},
                {data: 'income'},
            ]
        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });

    function updatePeriode() {
        $('#modal-form').modal('show');
    }
</script>
@endpush