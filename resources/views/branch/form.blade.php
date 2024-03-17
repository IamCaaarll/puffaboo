<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="" method="post" class="form-horizontal">
            @csrf
            @method('post')

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="branch_name" class="col-lg-2 col-lg-offset-1 control-label">Branch Name</label>
                        <div class="col-lg-6">
                            <input type="text" name="branch_name" id="branch_name" class="form-control" required autofocus>
                            <span class="help-block with-errors"></span>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="phone" class="col-lg-2 col-lg-offset-1 control-label">Telephone</label>
                    <div class="col-lg-6">
                        <input type="text" name="phone" id="phone" class="form-control" required>
                        <span class="help-block with-errors"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address" class="col-lg-2 col-lg-offset-1 control-label">Address</label>
                    <div class="col-lg-6">
                        <textarea name="address" id="address" rows="3" class="form-control"></textarea>
                        <span class="help-block with-errors"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="active" class="col-lg-2 col-lg-offset-1 control-label">Active</label>
                    <div class="col-lg-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1" checked>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-flat btn-success"><i class="fa fa-save"></i> Save</button>
                    <button type="button" class="btn btn-sm btn-flat btn-danger" data-dismiss="modal"><i class="fa fa-arrow-circle-left"></i> Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
