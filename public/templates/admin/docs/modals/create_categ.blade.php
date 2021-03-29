<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="create_categ" aria-hidden="true" id="create_categ">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="create_categ">{{ __('Create category') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Category title') }}</label>
                                <input class="form-control" name="title" type="text" required />
                            </div>
                        </div>

                        @if(count(sys_langs())>1)
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Language') }}</label>
                                <select name="lang_id" class="form-control" required>
                                    <option selected value="">- {{ __('Select') }} -</option>
                                    @foreach (sys_langs() as $sys_lang)
                                    <option value="{{ $sys_lang->id }}">{{ $sys_lang->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Select parent category') }}</label>
                                <select class="form-control" name="parent_id">
                                    <option value="">{{ __('Root (no parent)') }}</option>
                                    @foreach ($categories as $categ)
                                    @include('admin.docs.loops.categories-add-select-loop', $categ)
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Description') }} ({{ __('optional') }})</label>
                                <textarea class="form-control" name="description" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Custom URL structure') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="slug" type="text" />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Icon code') }} {{ __('optional') }}</label>
                                <input class="form-control" name="icon" type="text" />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Category badges') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="badges" type="text" />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Redirect URL') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="redirect_url" type="text" />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Position') }}</label>
                                <input class="form-control" name="position" type="text" />
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

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Meta title') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="meta_title" type="text" />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Meta description') }} ({{ __('optional') }})</label>
                                <textarea class="form-control" name="meta_description" rows="2"></textarea>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <input type="hidden" name="search_lang_id" value="{{ $search_lang_id }}">
                    <button type="submit" class="btn btn-primary">{{ __('Create category') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>