@extends('layouts.master')

@section('title')
    Edit Profile
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Edit Profile</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <form action="{{ route('user.update_profile') }}" method="post" class="form-profile" data-toggle="validator" enctype="multipart/form-data">
                @csrf
                <div class="box-body">
                    <div class="alert alert-info alert-dismissible" style="display: none;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fa fa-check"></i> Changes saved successfully
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-lg-2 control-label">Name</label>
                        <div class="col-lg-6">
                            <input type="text" name="name" class="form-control" id="name" required autofocus value="{{ $profile->name }}">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="picture" class="col-lg-2 control-label">Profile</label>
                        <div class="col-lg-4">
                            <input type="file" name="picture" class="form-control" id="picture"
                                onchange="preview('.tampil-picture', this.files[0])" disabled>
                            <span class="help-block with-errors"></span>
                            <br>
                            <div class="display-photo">
                                <img src="{{ url($profile->picture ?? '/') }}" width="200">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="old_password" class="col-lg-2 control-label">Old Password</label>
                        <div class="col-lg-6">
                            <input type="password" name="old_password" id="old_password" class="form-control" 
                            minlength="6">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-lg-2 control-label">Password</label>
                        <div class="col-lg-6">
                            <input type="password" name="password" id="password" class="form-control" 
                            minlength="6">
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password_confirmation" class="col-lg-2 control-label">Confirm Password</label>
                        <div class="col-lg-6">
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                                data-match="#password">
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
        $('#old_password').on('keyup', function () {
            if ($(this).val() != "") $('#password, #password_confirmation').attr('required', true);
            else $('#password, #password_confirmation').attr('required', false);
        });

        $('.form-profile').validator().on('submit', function (e) {
            if (! e.preventDefault()) {
                $.ajax({
                    url: $('.form-profile').attr('action'),
                    type: $('.form-profile').attr('method'),
                    data: new FormData($('.form-profile')[0]),
                    async: false,
                    processData: false,
                    contentType: false
                })
                .done(response => {
                    $('[name=name]').val(response.name);
                    $('.display-photo').html(`<img src="{{ url('/') }}${response.picture}" width="200">`);
                    $('.img-profile').attr('src', `{{ url('/') }}/${response.picture}`);

                    $('.alert').fadeIn();
                    setTimeout(() => {
                        $('.alert').fadeOut();
                    }, 3000);
                })
                .fail(errors => {
                    if (errors.status == 422) {
                        alert(errors.responseJSON); 
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Oops... Something went wrong!",
                            text: "Unable to save the data. Please try again.",
                        });
                    }
                    return;
                });
            }
        });
    });
</script>
@endpush