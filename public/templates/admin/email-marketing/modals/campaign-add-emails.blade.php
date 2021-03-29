<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="campaign-add-emails" aria-hidden="true" id="campaign-add-emails">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add recipients') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Add recipients') }}</label>
                                <textarea class="form-control" name="recipients" rows="10"></textarea>

                                <div class="alert alert-info mt-3">
                                    <h5>{{ __('Add recipient in this format') }}:</h5>
                                    <b>email@website.com, Recipient name</b>
                                    <div class="mb-2"></div>                                    
                                    <h5>{{ __('Notes') }}:</h5>
                                    <div class="small">
                                    - {{ __('Recipient name is optional but recomended') }}<br>
                                    - {{ __('If you add multiple recipients, add each recipient in a new line') }}<br>
                                    - {{ __('If recipient email already exists in this list, it will be not added') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <input type="hidden" name="add_type" value="input">
                    <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Add recipients') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>