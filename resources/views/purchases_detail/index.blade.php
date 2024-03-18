@extends('layouts.master')

@section('title')
Purchase
@endsection

@push('css')
<style>
    .display-payment {
        font-size: 5em;
        text-align: center;
        height: 100px;
    }

    .display-in-words {
        padding: 10px;
        background: #f0f0f0;
    }

    .table-purchases tbody tr:last-child {
        display: none;
    }

    @media(max-width: 768px) {
        .display-payment {
            font-size: 3em;
            height: 70px;
            padding-top: 5px;
        }
    }
</style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Purchase Transaction</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <table>
                    <tr>
                        <td>Supplier</td>
                        <td>: {{ $supplier->name }}</td>
                    </tr>
                    <tr>
                        <td>Telephone</td>
                        <td>: {{ $supplier->phone }}</td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td>: {{ $supplier->address }}</td>
                    </tr>
                </table>
            </div>
            <div class="box-body">
                    
                <form class="form-product">
                    @csrf
                    <div class="form-group row">
                        <label for="product_code" class="col-lg-2">Product Code</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="hidden" name="purchase_id" id="purchase_id" value="{{ $purchase_id }}">
                                <input type="hidden" name="product_id" id="product_id">
                                <input type="text" class="form-control" name="product_code" id="product_code">
                                <span class="input-group-btn">
                                    <button onclick="displayProduct()" class="btn btn-info btn-flat" type="button"><i class="fa fa-arrow-right"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-stiped table-bordered table-purchases table-hover">
                    <thead>
                        <th width="5%">#</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th width="15%">Quantity</th>
                        <th>Subtotal</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="display-payment bg-primary"></div>
                        <div class="display-in-words"></div>
                    </div>
                    <div class="col-lg-4">
                        <form action="{{ route('purchases.store') }}" class="form-purchases" method="post">
                            @csrf
                            <input type="hidden" name="purchase_id" value="{{ $purchase_id }}">
                            <input type="hidden" name="total" id="total">
                            <input type="hidden" name="total_item" id="total_item">
                            <input type="hidden" name="payment" id="payment">

                            <div class="form-group row">
                                <label for="totalrp" class="col-lg-2 control-label">Total</label>
                                <div class="col-lg-8">
                                    <input type="text" id="totalrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="discount" class="col-lg-2 control-label">Discount</label>
                                <div class="col-lg-8">
                                    <input type="number" name="discount" id="discount" class="form-control" value="{{ $discount }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="paymentrp" class="col-lg-2 control-label">Pay</label>
                                <div class="col-lg-8">
                                    <input type="text" id="paymentrp" class="form-control">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm btn-flat pull-right btn-save"><i class="fa fa-floppy-o"></i> Save Transaction</button>
            </div>
        </div>
    </div>
</div>

@includeIf('purchases_detail.product')
@endsection

@push('scripts')
<script>
    let table, table2;

    $(function () {
        $('body').addClass('sidebar-collapse');

        table = $('.table-purchases').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('purchases_detail.data', $purchase_id) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'product_code'},
                {data: 'product_name'},
                {data: 'purchase_price'},
                {data: 'quantity'},
                {data: 'subtotal'},
                {data: 'action', searchable: false, sortable: false},
            ],
            dom: 'Brt',
            bSort: false,
            paginate: false
        })
        .on('draw.dt', function () {
            loadForm($('#discount').val());
        });

        table2 = $('.table-product').DataTable();

        $(document).on('input', '.quantity', function () {
            let id = $(this).data('id');
            let quantity = parseInt($(this).val());

            if (quantity < 1) {
                $(this).val(1);
                Swal.fire({
                            icon: "error",
                            title: "Oops... Something went wrong!",
                            text: "The number cannot be less than 1. Please try again.",
                        });
                return;
            }
            if (quantity > 10000) {
                $(this).val(10000);
                Swal.fire({
                            icon: "error",
                            title: "Oops... Something went wrong!",
                            text: "The number cannot exceed 10,000. Please try again.",
                        });
                return;
            }

            $.post(`{{ url('/purchases_detail') }}/${id}`, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'put',
                    'quantity': quantity
                })
                .done(response => {
                    $(this).on('mouseout', function () {
                        table.ajax.reload(() => loadForm($('#discount').val()));
                    });
                })
                .fail(errors => {
                    Swal.fire({
                    icon: "error",
                    title: "Oops... Something went wrong!",
                    text: "Unable to save the data. Please try again.",
                });
                    return;
                });
        });

        $(document).on('input', '#discount', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm($(this).val());
        });

        $('.btn-save').on('click', function () {
            $('.form-purchases').submit();
        });
    });

    function displayProduct() {
        $('#modal-product').modal('show');
    }

    function hideProduct() {
        $('#modal-product').modal('hide');
    }

    function selectProduct(id, code) {
        $('#product_id').val(id);
        $('#product_code').val(code);
        hideProduct();
        addProduct();
    }

    function addProduct() {
        $.post('{{ route('purchases_detail.store') }}', $('.form-product').serialize())
            .done(response => {
                $('#product_code').focus();
                table.ajax.reload(() => loadForm($('#discount').val()));
            })
            .fail(errors => {
                Swal.fire({
                    icon: "error",
                    title: "Oops... Something went wrong!",
                    text: "Unable to save the data. Please try again.",
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
                            table.ajax.reload(() => loadForm($('#discount').val()));
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

    function loadForm(discount = 0) {
        $('#total').val($('.total').text());
        $('#total_item').val($('.total_item').text());

        $.get(`{{ url('/purchases_detail/loadform') }}/${discount}/${$('.total').text()}`)
            .done(response => {
                $('#totalrp').val('₱ '+ response.totalrp);
                $('#paymentrp').val('₱ '+ response.paymentrp);
                $('#payment').val(response.payment);
                $('.display-payment').text('₱ '+ response.paymentrp);
                $('.display-in-words').text(response.in_words);

            })
            .fail(errors => {
                Swal.fire({
                    icon: "error",
                    title: "Oops... Something went wrong!",
                    text: "Unable to display the data. Please try again.",
                });
                return;
            })
    }
</script>
@endpush