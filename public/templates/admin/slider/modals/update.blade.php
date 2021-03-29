<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update-{{ $slide->id }}" aria-hidden="true" id="update-{{ $slide->id }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('admin.slider.show', ['id' => $slide->id]) }}" method="post" enctype="multipart/form-data">
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
                                <label>{{ __('Title') }}</label>
                                <input class="form-control" name="title" type="text" required value="{{ $slide->title }}" />
                            </div>
                        </div>                       

                        @if(count(sys_langs())>1)
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Language') }}</label>
                                <select name="lang_id" class="form-control" required>
                                    <option selected value="">- {{ __('Select') }} -</option>
                                    @foreach (sys_langs() as $lang)
                                    <option @if($slide->lang_id==$lang->id) selected @endif value="{{ $lang->id }}">{{ $lang->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Position') }}</label>
                                <input class="form-control" name="position" type="text" value="{{ $slide->position }}" />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Content') }}</label>
                                <textarea class="form-control editor" name="content">{{ $slide->content }}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Destination URL') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="url" type="text" value="{{ $slide->url }}" />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('URL target') }}</label>
                                <select name="target" class="form-control">
                                    <option @if ($slide->target=='self') selected="selected" @endif value="self">{{ __('Same page') }}</option>
                                    <option @if ($slide->target=='blank') selected="selected" @endif value="blank">{{ __('New page') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Active') }}</label>
                                <select name="active" class="form-control">
                                    <option @if ($slide->active==1) selected="selected" @endif value="1">{{ __('Yes') }}</option>
                                    <option @if ($slide->active==0) selected="selected" @endif value="0">{{ __('No') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Change image') }} ({{ __('optional') }})</label> <br />
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="validatedCustomFile" name="image" aria-describedby="fileHelp">
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