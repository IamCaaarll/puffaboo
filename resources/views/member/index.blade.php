@extends('layouts.master')

@section('title')
List of Members
@endsection

@section('breadcrumb')
    @parent
    <li class="active">List of Members</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="addForm('{{ route('member.store') }}')" class="btn btn-success btn-flat"><i class="fa fa-plus-circle"></i> Add New Member</button>
                {{-- <button onclick="printMember('{{ route('member.print_member') }}')" class="btn btn-primary btn-flat"><i class="fa fa-id-card"></i> Download Membership Card</button> --}}
            </div>
            <div class="box-body table-responsive">
                <form action="" method="post" class="form-member">
                    @csrf
                    <table class="table table-stiped table-bordered table-hover">
                        <thead>
                            <th width="5%">
                                <input type="checkbox" name="select_all" id="select_all">
                            </th>
                            <th width="5%">#</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Telephone</th>
                            <th>Address</th>
                            <th width="15%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

@includeIf('member.form')
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
                url: '{{ route('member.data') }}',
            },
            columns: [
                {data: 'select_all', searchable: false, sortable: false},
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'member_code'},
                {data: 'name'},
                {data: 'phone'},
                {data: 'address'},
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
        $('#modal-form .modal-title').text('Add Member');

        $('#modal-form form')[0].reset();
        $('#modal-form form').attr('action', url);
        $('#modal-form [name=_method]').val('post');
        $('#modal-form [name=name]').focus();
    }

    function editForm(url) {
        $('#modal-form').modal('show');
        $('#modal-form .modal-title').text('Edit Member');

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

    function printMember(url) {
        if ($('input:checked').length < 1) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Please select the data that you want to print.",
            });

            return;
        } else {
            $('.form-member')
                .attr('target', '_blank')
                .attr('action', url)
                .submit();
        }
    }
</script>
@endpush