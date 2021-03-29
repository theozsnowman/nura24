<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="create-gateway" aria-hidden="true" id="create-gateway">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Create gateway') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Payment gateway') }}</label>
                                <input class="form-control" name="title" type="text" required />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Vendor email') }}</label>
                                <input class="form-control" name="vendor_email" type="email" required />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Client info') }} ({{ __('optional') }})</label>
                                <textarea class="form-control" rows="3" name="client_info" aria-describedby="infoHelp"></textarea>
                                <small id="infoHelp" class="form-text text-muted">{{ __('This info is visible to client in checkout page') }}</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Instant') }}</label>
                                <select name="instant" class="form-control" aria-describedby="instantHelp">
                                    <option value="1">{{ __('Yes') }}</option>
                                    <option value="0">{{ __('No') }}</option>
                                </select>
                                <small id="instantHelp" class="form-text text-muted">{{ __('Set yes if payment is instant. Set no if payment must be manually processed') }}</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Hidden') }}</label>
                                <select name="hidden" class="form-control" aria-describedby="hiddenHelp">
                                    <option value="0">{{ __('No') }}</option>
                                    <option value="1">{{ __('Yes') }}</option>
                                </select>
                                <small id="hiddenHelp" class="form-text text-muted">{{ __('Hidden payment types don\'t appear in clients orders payment options') }}</small>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Active') }}</label>
                                <select name="active" class="form-control">
                                    <option value="1">{{ __('Yes') }}</option>
                                    <option value="0">{{ __('No') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Position') }}</label>
                                <input class="form-control" name="position" type="text" />
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Logo') }}</label> <br />
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="validatedCustomFile" name="logo">
                                    <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Checkout file') }}</label>
                                <input class="form-control" name="checkout_file" type="text" aria-describedby="fileHelp" />
                                <small id="fileHelp" class="form-text text-muted">{{ __('File must be located in "/templates/checkout/" folder') }}</small>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Create gateway') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>