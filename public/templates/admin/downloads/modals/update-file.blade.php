<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update-file-{{ $file->id }}" aria-hidden="true" id="update-file-{{ $file->id }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('admin.download.files.update', ['id' => $download->id, 'file_id' => $file->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Update') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('File title') }}</label>
                                <input class="form-control" name="title" type="text" required value="{{ $file->title }}" />
                            </div>
                        </div>
                       
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('File version') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="version" type="text" value="{{ $file->version }}" />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Release date') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="release_date" type="text" id="datepicker_date_{{ $file->id }}" autocomplete="off" value="{{ $file->release_date }}" />

                                <script>
                                    $('#datepicker_date_{{ $file->id }}').datepicker({
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
                                    <option @if ($file->active==1) selected @endif value="1">{{ __('Yes') }}</option>
                                    <option @if ($file->active==0) selected @endif value="0">{{ __('No') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Featured') }}</label>
                                <select name="featured" class="form-control" aria-describedby="fHelp">
                                    <option @if ($file->featured==0) selected @endif value="0">{{ __('No') }}</option>
                                    <option @if ($file->featured==1) selected @endif value="1">{{ __('Yes') }}</option>
                                </select>
                                <small id="fHelp" class="form-text text-muted">{{ __('Featured files are displayed first') }}</small>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Change file') }} ({{ __('optional') }}</label> <br />
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="validatedCustomFile" name="file" aria-describedby="fileHelp">
                                    <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Update') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>