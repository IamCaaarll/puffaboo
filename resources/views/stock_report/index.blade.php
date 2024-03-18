@extends('layouts.master')

@section('title')
Product Inventory Report

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
                <button onclick="updatePeriode()" class="btn btn-primary btn-flat"><i class="fa fa-plus-circle"></i> Change Branch</button>
                <a href="{{ route('stock_report.export_pdf', [$branch_id]) }}" target="_blank" class="btn btn-success btn-flat"><i class="fa fa-file-excel-o"></i> Export PDF</a> 
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-hover">
                    <thead>
                        <th width="5%">#</th>
                        <th>Product Name</th>
                        <th>Purchase Price</th>
                        <th>Selling Price</th>
                        <th>Discount</th>
                        <th>Stock</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('stock_report.form')
@endsection

@push('scripts')
<script src="{{ asset('/AdminLTE-2/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    let table;

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('stock_report.data', [$branch_id]) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'product_name'},
                {data: 'purchase_price'},
                {data: 'selling_price'},
                {data: 'discount'},
                {data: 'stock'},
            ],
            dom: 'Brt',
            bSort: false,
            bPaginate: false,
        });

    });

    function updatePeriode() {
        $('#modal-form').modal('show');
    }
</script>
@endpush