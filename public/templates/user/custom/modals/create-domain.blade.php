<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="create-domain" aria-hidden="true" id="create-domain">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="create-domain">{{ __('Add new domain') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('Close') }}</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Domain') }}</label>
                                <input class="form-control" name="domain" type="text" required>
                                <span class="small mt-2 text-muted">{{ __('Input only domain name, WITHOUT http or www. Example: nura24.com') }}</span>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Add new domain') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>