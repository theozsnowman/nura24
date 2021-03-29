<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>

<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="create_product_file" aria-hidden="true" id="create_product_file">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Create file') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('Close') }}</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Title') }}</label>
                                <input class="form-control" name="title" type="text" required />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Description') }}</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('File version') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="version" type="text" />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Release date') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="release_date" type="text" id="datepicker_date" autocomplete="off" />

                                <script>
                                    $('#datepicker_date').datepicker({
                                        uiLibrary: 'bootstrap4',
                                        iconsLibrary: 'fontawesome',
                                        format: 'yyyy-mm-dd' 
                                    });
                                </script>
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
                                <label>{{ __('Featured') }}</label>
                                <select name="featured" class="form-control" aria-describedby="fHelp">
                                    <option value="0">{{ __('No') }}</option>
                                    <option value="1">{{ __('Yes') }}</option>                                    
                                </select>
                                <small id="fHelp" class="form-text text-muted">{{ __('Featured files are displayed first') }}</small>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Upload file') }}</label> <br />
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="validatedCustomFile" name="file" aria-describedby="fileHelp">
                                    <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Create file') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>