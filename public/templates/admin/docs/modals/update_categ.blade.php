<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update_categ_{{ $categ->id }}" aria-hidden="true" id="update_categ_{{ $categ->id }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('admin.docs.categ.show', ['id' => $categ->id, 'search_lang_id' => $search_lang_id]) }}" method="post">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Update category') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Category title') }}</label>
                                <input class="form-control" name="title" type="text" required value="{{ $categ->title }}" />
                            </div>
                        </div>

                        @if(count(sys_langs())>1)
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Language') }}</label>
                                <select name="lang_id" class="form-control" required>
                                    <option value="">- {{ __('Select') }} -</option>
                                    @foreach (sys_langs() as $sys_lang)
                                    <option @if($categ->lang_id==$sys_lang->id) selected @endif value="{{ $sys_lang->id }}">{{ $sys_lang->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        @if($categ->slug!='uncategorized')
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Select parent category') }}</label>
                                <select class="form-control" name="parent_id">
                                    <option @if($categ->parent_id==null) selected @endif value="">{{ __('Root (no parent)') }}</option>
                                    @foreach ($categories as $cat)
                                    {{ $level = 1 }}
                                    @include('admin.docs.loops.categories-edit-select-loop', $cat)
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Description') }} ({{ __('optional') }})</label>
                                <textarea class="form-control" name="description" rows="2">{{ $categ->description }}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Custom URL structure') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="slug" type="text" value="{{ $categ->slug }}" />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Icon code') }} {{ __('optional') }}</label>
                                <input class="form-control" name="icon" type="text" value="{{ $categ->icon }}" />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Category badges') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="badges" type="text" value="{{ $categ->badges }}" />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Redirect URL') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="redirect_url" type="text" value="{{ $categ->redirect_url }}" />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Position') }}</label>
                                <input class="form-control" name="position" type="text" value="{{ $categ->position }}" />
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Active') }}</label>
                                <select name="active" class="form-control">
                                    <option @if ($categ->active==1) selected="selected" @endif value="1">{{ __('Yes') }}</option>
                                    <option @if ($categ->active==0) selected="selected" @endif value="0">{{ __('No') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Meta title') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="meta_title" type="text" value="{{ $categ->meta_title }}" />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Meta description') }} ({{ __('optional') }})</label>
                                <textarea class="form-control" name="meta_description" rows="2">{{ $categ->meta_description }}</textarea>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Update category') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>