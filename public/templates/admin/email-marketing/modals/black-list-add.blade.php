<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="black-list-add" aria-hidden="true" id="black-list-add">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post" action="{{ route('admin.email.black-list') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add recipient in black list') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Email') }}</label>
                                <input class="form-control" name="email" required>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Reason') }}</label>
                                <select class="form-control" name="reason" required>
                                    <option value="">- {{ __('Select') }} -</option>
                                    <option value="unsubscribed">{{ __('Unsubscribed') }}</option>
                                    <option value="invalid_email">{{ __('Invalid email') }}</option>
                                    <option value="other">{{ __('Other') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <input type="hidden" name="add_type" value="input">
                    <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Add') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>