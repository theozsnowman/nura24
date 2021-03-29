<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="campaign-import-list" aria-hidden="true" id="campaign-import-list">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add list recipients') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Add recipientsSelect list') }}</label>
                                <select class="form-control" name="list_id" required>
                                    <option value="">- {{ __('Selet list') }} -</option>
                                    @foreach($lists as $list)
                                    <option value="{{ $list->id }}">{{ $list->title }} ({{ $list->count_recipients }} {{ __('recipients') }})</option>
                                    @endforeach
                                </select>

                                <div class="alert alert-info mt-3">                                                               
                                    <div class="small">
                                    {{ __('If recipient email already exists in this campaign, it will be not added again') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <input type="hidden" name="add_type" value="list">
                    <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Add list recipients') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>