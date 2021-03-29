<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="campaign-import-csv" aria-hidden="true" id="campaign-import-csv">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Import CSV file') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Upload CSV file') }}</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="validatedCustomFile" name="file">
                                    <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
                                </div>

                                <div class="alert alert-info mt-3">
                                    <h5>{{ __('CSV content must have this format') }}:</h5>
                                    <b>email@website.com, Recipient name</b>
                                    <div class="mb-2"></div>
                                    <h5>{{ __('Notes') }}:</h5>
                                    <div class="small">
                                        - {{ __('Recipient name is optional') }}<br>
                                        - {{ __('If you have multiple recipients, each recipient must be in a new line') }}<br>
                                        - {{ __('If recipient email already exists in this list, it will be not added') }}
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <input type="hidden" name="add_type" value="csv">
                    <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Import CSV file') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>