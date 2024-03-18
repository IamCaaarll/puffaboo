<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('stock_report.index') }}" method="get" data-toggle="validator" class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Reporting Period</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="branch_id" class="col-lg-2 col-lg-offset-1 control-label">Branch</label>
                        <div class="col-lg-6">
                            <select name="branch_id" id="branch_id" class="form-control" required>
                                <option value="">Select Branch</option>
                                @foreach ($branch as $key => $item)
                                <option value="{{ $key }}" @if($key == request('branch_id')) selected @endif>{{ $item }}</option>
                                @endforeach
                            </select>
                            <span class="help-block with-errors"></span>
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
