<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="create" aria-hidden="true" id="create">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Create slide') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Title') }}</label>
                                <input class="form-control" name="title" type="text" required />
                            </div>
                        </div>

                        @if(count(sys_langs())>1)
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Language') }}</label>
                                <select name="lang_id" class="form-control" required>
                                    <option value="">- {{ __('Select') }} -</option>
                                    @foreach (sys_langs() as $lang)
                                    <option value="{{ $lang->id }}">{{ $lang->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Position') }}</label>
                                <input class="form-control" name="position" type="text" />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Content') }}</label>
                                <textarea class="form-control editor" name="content"></textarea>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Destination URL') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="url" type="text" />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('URL target') }}</label>
                                <select name="target" class="form-control">
                                    <option value="self">{{ __('Same page') }}</option>
                                    <option value="blank">{{ __('New page') }}</option>
                                </select>
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

                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Slide image') }}</label> <br />
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="validatedCustomFile" name="image" aria-describedby="fileHelp">
                                    <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark"><i class="fas fa-share"></i> {{ __('Create slide') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>