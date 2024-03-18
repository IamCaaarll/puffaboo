@extends('layouts.master')

@section('title')
    Expense List
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Expense List</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm('{{ route('expenses.store') }}')" class="btn btn-success btn-flat"><i class="fa fa-plus-circle"></i> New Expenses Entry</button>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-hover">
                    <thead>
                        <th width="5%">#</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@includeIf('expenses.form')
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
                url: '{{ route('expenses.data') }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'created_at'},
                {data: 'branch_name'},
                {data: 'description'},
                {data: 'amount'},
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
                    title: "Oops... Something went wrong!",
                    text: "Unable to save the data. Please try again.",
                });
                        return;
                    });
            }
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Add Expenses');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=description]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Expenses');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=description]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=branch_id]').val(response.branch_id);
                $('#modal-form [name=description]').val(response.description);
                $('#modal-form [name=amount]').val(response.amount);
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