@extends('layouts.master')

@section('title')
    Settings
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Settings</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <form action="{{ route('setting.update') }}" method="post" class="form-setting" data-toggle="validator" enctype="multipart/form-data">
                @csrf
                <div class="box-body">
                    <div class="alert alert-info alert-dismissible" style="display: none;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fa fa-check"></i> Changes saved successfully
                    </div>
                    <div class="form-group row">
                        <label for="company_name" class="col-lg-2 control-label">Company name</label>
                        <div class="col-lg-6">
                            <input type="text" name="company_name" class="form-control" id="company_name" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="phone" class="col-lg-2 control-label">Telephone</label>
                        <div class="col-lg-6">
                            <input type="text" name="phone" class="form-control" id="phone" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address" class="col-lg-2 control-label">Address</label>
                        <div class="col-lg-6">
                            <textarea name="address" class="form-control" id="address" rows="3" required></textarea>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="path_logo" class="col-lg-2 control-label">Logo</label>
                        <div class="col-lg-4">
                            <input type="file" name="path_logo" class="form-control" id="path_logo"
                                onchange="preview('.display-logo', this.files[0])">
                            <span class="help-block with-errors"></span>
                            <br>
                            <div class="display-logo"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="member_card_path" class="col-lg-2 control-label">Membership Card</label>
                        <div class="col-lg-4">
                            <input type="file" name="member_card_path" class="form-control" id="member_card_path"
                                onchange="preview('.display-member-card', this.files[0], 300)">
                            <span class="help-block with-errors"></span>
                            <br>
                            <div class="display-member-card"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="discount" class="col-lg-2 control-label">Discount</label>
                        <div class="col-lg-2">
                            <input type="number" name="discount" class="form-control" id="discount" required>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="note_type" class="col-lg-2 control-label">Note Type</label>
                        <div class="col-lg-2">
                            <select name="note_type" class="form-control" id="note_type" required>
                                <option value="1">Small Invoice</option>
                                <option value="2">PDF Invoice</option>
                            </select>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                </div>
                <div class="box-footer text-right">
                    <button class="btn btn-sm btn-flat btn-primary"><i class="fa fa-save"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        showData();
        $('.form-setting').validator().on('submit', function (e) {
            if (! e.preventDefault()) {
                $.ajax({
                    url: $('.form-setting').attr('action'),
                    type: $('.form-setting').attr('method'),
                    data: new FormData($('.form-setting')[0]),
                    async: false,
                    processData: false,
                    contentType: false
                })
                .done(response => {
                    
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Data Saved Successfully",
                        showConfirmButton: false,
                        timer: 1500
                        }).then(() => {
                            showData();
                    $('.alert').fadeIn();
                            setTimeout(() => {
                        $('.alert').fadeOut();
                    }, 3000);
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
            }
        });
    });

    function showData() {
        $.get('{{ route('setting.show') }}')
            .done(response => {
                $('[name=company_name]').val(response.company_name);
                $('[name=phone]').val(response.phone);
                $('[name=address]').val(response.address);
                $('[name=discount]').val(response.discount);
                $('[name=note_type]').val(response.note_type);
                $('title').text(response.company_name + ' | Settings');
                
                let words = response.company_name.split(' ');
                let word  = '';
                words.forEach(w => {
                    word += w.charAt(0);
                });
                $('.logo-mini').text(word);
                $('.logo-lg').text(response.company_name);

                $('.display-logo').html(`<img src="{{ url('/') }}${response.path_logo}" width="200">`);
                $('.display-member-card').html(`<img src="{{ url('/') }}${response.member_card_path}" width="300">`);
                $('[rel=icon]').attr('href', `{{ url('/') }}/${response.path_logo}`);
            })
            .fail(errors => {
                Swal.fire({
                    icon: "error",
                    title: "Oops... Something went wrong!",
                    text: "Unable to display the data. Please try again.",
                });
                return;
            });
    }
</script>
@endpush