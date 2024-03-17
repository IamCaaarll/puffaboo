@extends('layouts.master')

@section('title')
    Supplier List
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Supplier List</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm('{{ route('supplier.store') }}')" class="btn btn-success btn-flat"><i class="fa fa-plus-circle"></i> Add New Supplier</button>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-hover">
                    <thead>
                        <th width="5%">#</th>
                        <th>Name</th>
                        <th>Telephone</th>
                        <th>Address</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('supplier.form')
@endsection

@push('scripts')
<script>
    let table;

    $(function () {
        table = $('.table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('supplier.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'name'},
                {data: 'phone'},
                {data: 'address'},
                {data: 'action', searchable: false, sortable: false},
            ]
        });

        $('#modal-form').validator().on('submit', function (e) {
            if (! e.preventDefault()) {
                $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                    .done((response) => {
                        Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Data Saved Successfully",
                        showConfirmButton: false,
                        timer: 1500
                        }).then(() => {
                        $('#modal-form').modal('hide');
                        table.ajax.reload();
                    });
                    })
                    .fail((errors) => {
                        Swal.fire({
                                icon: "error",
                                title: "Oops... Duplicate Entry Detected!",
                                text: "The data you are trying to save already exists and duplicates are not allowed. Please review and try again.",
                            });
                        return;
                    });
            }
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Add Supplier');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=name]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Supplier');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=name]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=name]').val(response.name);
                $('#modal-form [name=phone]').val(response.phone);
                $('#modal-form [name=address]').val(response.address);
            })
            .fail((errors) => {
                Swal.fire({
                    icon: "error",
                    title: "Oops... Something went wrong!",
                    text: "Unable to display the data. Please try again.",
                });
                return;
            });
    }

    function deleteData(url) {
        Swal.fire({
            title: "Are you sure you want to delete selected data?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
            }).then((result) => {
            if (result.isConfirmed) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Data Deleted Successfully",
                        showConfirmButton: false,
                        timer: 1500
                        }).then(() => {
                    table.ajax.reload();
                    });
                })
                .fail((errors) => {
                    Swal.fire({
                            icon: "error",
                            title: "Oops... Something went wrong!",
                            text: "Unable to delete the data. Please try again.",
                        });
                    return;
                });
            }
            });
    }
</script>
@endpush