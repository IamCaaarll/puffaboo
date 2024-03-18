@extends('layouts.master')

@section('title')
Sales Transactions
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

    .table-sales tbody tr:last-child {
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
    <li class="active">Sales Transactions</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body">
                    
                <form class="form-product">
                    @csrf
                    <div class="form-group row">
                        <label for="product_code" class="col-lg-2">Product Code</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <input type="hidden" name="sale_id" id="sale_id" value="{{ $sale_id }}">
                                <input type="hidden" name="product_id" id="product_id">
                                <input type="text" class="form-control" name="product_code" id="product_code">
                                <span class="input-group-btn">
                                    <button onclick="displayProduct()" class="btn btn-success btn-flat" type="button"><i class="fa fa-search-plus"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </form>

                <table class="table table-stiped table-bordered table-sales">
                    <thead>
                        <th width="5%">#</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th width="15%">Quantity</th>
                        <th>Discount</th>
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
                        <form action="{{ route('transaction.save') }}" class="form-sales" method="post">
                            @csrf
                            <input type="hidden" name="sale_id" value="{{ $sale_id }}">
                            <input type="hidden" name="total" id="total">
                            <input type="hidden" name="total_item" id="total_item">
                            <input type="hidden" name="payment" id="payment">
                            <input type="hidden" name="member_id" id="member_id" value="{{ $memberSelected->member_id }}">

                            <div class="form-group row">
                                <label for="totalrp" class="col-lg-2 control-label">Total</label>
                                <div class="col-lg-8">
                                    <input type="text" id="totalrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="member_code" class="col-lg-2 control-label">Member</label>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="member_code" value="{{ $memberSelected->member_code }}">
                                        <span class="input-group-btn">
                                            <button onclick="displayMember()" class="btn btn-success btn-flat" type="button"><i class="fa fa-search-plus"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="discount" class="col-lg-2 control-label">Discount</label>
                                <div class="col-lg-8">
                                    <input type="number" name="discount" id="discount" class="form-control" 
                                        value="{{ ! empty($memberSelected->member_id) ? $discount : 0 }}" 
                                        readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="paymentrp" class="col-lg-2 control-label">Pay</label>
                                <div class="col-lg-8">
                                    <input type="text" id="paymentrp" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="received" class="col-lg-2 control-label">Received</label>
                                <div class="col-lg-8">
                                    <input type="number" id="received" class="form-control" name="received" value="{{ $sales->received ?? 0 }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="return" class="col-lg-2 control-label">Return</label>
                                <div class="col-lg-8">
                                    <input type="text" id="return" name="return" class="form-control" value="0" readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-success btn-sm btn-flat pull-right btn-save"><i class="fa fa-floppy-o"></i> Save Transaction</button>
            </div>
        </div>
    </div>
</div>

@includeIf('sales_detail.product')
@includeIf('sales_detail.member')
@endsection

@push('scripts')
<script>
    let table, table2;

    $(function () {
        $('body').addClass('sidebar-collapse');

        table = $('.table-sales').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('transaction.data', $sale_id) }}',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'product_code'},
                {data: 'product_name'},
                {data: 'selling_price'},
                {data: 'stock'},
                {data: 'quantity'},
                {data: 'discount'},
                {data: 'subtotal'},
                {data: 'action', searchable: false, sortable: false},
            ],
            dom: 'Brt',
            bSort: false,
            paginate: false
        })
        .on('draw.dt', function () {
            loadForm($('#discount').val());
            setTimeout(() => {
                $('#received').trigger('input');
            }, 300);
        });
        table2 = $('.table-product').DataTable();

        $(document).on('input', '.quantity', function () {
            let id = $(this).data('id');
            let stock = $(this).data('stock');
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
            if (quantity > stock) {
                $(this).val(stock);
                Swal.fire({
                            icon: "error",
                            title: "Oops... Something went wrong!",
                            text: "The quantity cannot exceed "+stock+" quantity. Please adjust the quantity and try again",
                        });
                return;
            }

            $.post(`{{ url('/transaction') }}/${id}`, {
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

        $('#received').on('input', function () {
            if ($(this).val() == "") {
                $(this).val(0).select();
            }

            loadForm($('#discount').val(), $(this).val());
        }).focus(function () {
            $(this).select();
        });

        $('.btn-save').on('click', function () {
            $('.form-sales').submit();
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
        $.post('{{ route('transaction.store') }}', $('.form-product').serialize())
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

    function displayMember() {
        $('#modal-member').modal('show');
    }

    function selectMember(id, code) {
        $('#member_id').val(id);
        $('#member_code').val(code);
        $('#discount').val('{{ $discount }}');
        loadForm($('#discount').val());
        $('#received').val(0).focus().select();
        hideMember();
    }

    function hideMember() {
        $('#modal-member').modal('hide');
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

    function loadForm(discount = 0, received = 0) {
        $('#total').val($('.total').text());
        $('#total_item').val($('.total_item').text());

        $.get(`{{ url('/transaction/loadform') }}/${discount}/${$('.total').text()}/${received}`)
            .done(response => {
                $('#totalrp').val('₱ '+ response.totalrp);
                $('#paymentrp').val('₱ '+ response.paymentrp);
                $('#payment').val(response.payment);
                $('.display-payment').text('Pay: ₱ '+ response.paymentrp);
                $('.display-in-words').text(response.in_words);

                $('#return').val('₱'+ response.paymentrp);
                if ($('#received').val() != 0) {
                    $('.display-payment').text('Return: ₱ '+ response.change_rp);
                    $('.display-in-words').text(response.change_in_words);
                }
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