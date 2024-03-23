@extends('layouts.master')

@section('title')
    Product List
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Product List</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="btn-group">
                    <button onclick="addForm('{{ route('product.store') }}')" class="btn btn-success  btn-flat"><i class="fa fa-plus-circle"></i> Add New Product</button>
                    <button onclick="deleteSelected('{{ route('product.delete_selected') }}')" class="btn btn-danger  btn-flat"><i class="fa fa-trash"></i> Delete</button>
                    <butt/on onclick="printBarcode('{{ route('product.print_barcode') }}')" class="btn btn-warning  btn-flat"><i class="fa fa-barcode"></i> Print Barcode</button>
                </div>
            </div>
            <div class="box-body table-responsive">
                <form action="" method="post" class="form-product">
                    @csrf
                    <table class="table table-stiped table-bordered table-hover">
                        <thead>
                            <th width="5%">
                                <input type="checkbox" name="select_all" id="select_all">
                            </th>
                            <th width="5%">#</th>
                            <th>Code</th>
                            <th>Branch</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Purchase Price</th>
                            <th>Selling Price</th>
                            <th>Discount</th>
                            <th>Stock</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

@includeIf('product.form')
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
                url: '{{ route('product.data') }}',
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'product_code'},
                {data: 'branch_name'},
                {data: 'product_name'},
                {data: 'brand'},
                {data: 'purchase_price'},
                {data: 'selling_price'},
                {data: 'discount'},
                {data: 'stock'},
                {data: 'action', searchable: false, sortable: false},
            ]
        });
         // Handle change event of individual checkboxes
         $('.table').on('change', 'input[type="checkbox"]', function() {
            // If any individual checkbox is unchecked, uncheck the select_all checkbox
            if (!$(this).prop('checked')) {
                $('input[name="select_all"]').prop('checked', false);
            }
        });

        // Handle change event of select_all checkbox
        $('input[name="select_all"]').on('change', function() {
            // If select_all checkbox is checked, check all individual checkboxes
            if ($(this).prop('checked')) {
                $('.table').find('input[type="checkbox"]').prop('checked', true);
            } else {
                // If select_all checkbox is unchecked, uncheck all individual checkboxes
                $('.table').find('input[type="checkbox"]').prop('checked', false);
            }
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

        $('[name=select_all]').on('click', function () {
            $(':checkbox').prop('checked', this.checked);
        });
    });

    function addForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Add Product');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=product_name]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Product');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('put');
        $('#modal-form [name=product_name]').focus();

        $.get(url)
            .done((response) => {
                $('#modal-form [name=product_name]').val(response.product_name);
                $('#modal-form [name=branch_id]').val(response.branch_id);
                $('#modal-form [name=brand]').val(response.brand);
                $('#modal-form [name=purchase_price]').val(response.purchase_price);
                $('#modal-form [name=selling_price]').val(response.selling_price);
                $('#modal-form [name=discount]').val(response.discount);
                $('#modal-form [name=stock]').val(response.stock);
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

    function deleteSelected(url) {
        if ($('input:checked').length > 1) {

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
                $.post(url, $('.form-product').serialize())
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
        } else {
            Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Please select the data that you want to delete.",
                });
            return;
        }
    }

    function printBarcode(url) {
        if ($('input:checked').length < 1) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Please select the data that you want to print.",
            });

            return;
        } else if ($('input:checked').length < 3) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Please select at least three data entries to print.",
            });

            return;
        } else {
            $('.form-product')
                .attr('target', '_blank')
                .attr('action', url)
                .submit();
        }
    }
</script>
@endpush