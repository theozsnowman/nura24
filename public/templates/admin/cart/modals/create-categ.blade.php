<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>

<script>
    function showDiv(divId, element)
{
    document.getElementById(divId).style.display = element.value == '' ? 'block' : 'none';
}
</script>

<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="create-categ" aria-hidden="true" id="create-categ">
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

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>{{ __('Select parent category') }}</label>
                                <select class="form-control" name="parent_id" onchange="showDiv('hidden_div', this)">
                                    <option value="">{{ __('Root (no parent)') }}</option>
                                    @foreach ($categories as $categ)
                                    @include('admin.cart.loops.categories-add-select-loop', $categ)
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div id="hidden_div">
                                <div class="form-group">
                                    <label>{{ __('Select products type') }}</label>
                                    <select class="form-control" name="product_type">
                                        <option value="">-- {{ __('Select product type') }} --</option>
                                        <option value="download">{{ __('Downloadable product') }}</option>
                                        <option value="task">{{ __('Service / Task') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('Description') }} ({{ __('optional') }})</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
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
                                <label>{{ __('Custom template file badges') }} ({{ __('optional') }})</label>
                                <input class="form-control" name="custom_tpl" type="text" />
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
                    <button type="submit" class="btn btn-primary">{{ __('Create category') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>