<div class="card-header">
    <span class="pull-right">
        <a class="btn btn-primary" href="{{ route('admin.cart.products') }}"><i class="fas fa-th"></i> {{ __('All products') }}</a>
    </span>
    <h3><i class="far fa-plus-square"></i> {{ __('Create product') }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="post" action="{{ route('admin.cart.products') }}" enctype="multipart/form-data">
        @csrf

        <div class="row">

            <div class="form-group col-xl-9 col-md-8 col-sm-12">

                <div class="row">
                    <div class="col-md-9 col-sm-8 col-12">
                        <div class="form-group">
                            <label>{{ __('Product title') }}</label>
                            <input class="form-control" name="title" type="text" required>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-4 col-12">
                        <div class="form-group">
                            <label>{{ __('Category') }}</label>
                            <select class="form-control" name="categ_id" required>
                                <option selected="selected" value="">- {{ __('select') }} -</option>
                                @foreach ($categories as $categ)
                                @include('admin.cart.loops.categories-add-select-loop', $categ)
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label>{{ __('Price') }}</label>
                            @if(! default_currency())
                            <input type="text" class="form-control is-invalid" id="validationServer03" name="price" value="" required readonly>
                            <div class="invalid-feedback">{{ __('Please set default currency in Business config') }}</div>
                            @else
                            <div class="input-group">
                                <input type="text" class="form-control" aria-label="{{ __('Amount (with dot and two decimal places)') }}" name="price" aria-describedby="priceHelp" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ default_currency()->code}}</span>
                                    <span class="input-group-text">{{ __('Example') }}: 9.50</span>
                                </div>
                            </div>
                            <small id="priceHelp" class="form-text text-muted">{{ __('Input 0 for Free product') }}</small>
                            @endif
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label>{{ __('Summary') }} ({{ __('optional') }})</label>
                    <textarea rows="3" class="form-control" name="summary"></textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('Product description') }}</label>
                    <textarea class="form-control editor" name="content"></textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('Help info') }} ({{ __('optional') }})</label>
                    <textarea rows="3" class="form-control" name="help_info"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>{{ __('Custom Meta title') }} ({{ __('optional') }})</label>
                            <input type="text" class="form-control" name="meta_title">
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>{{ __('Custom Meta description') }} ({{ __('optional') }})</label>
                            <input type="text" class="form-control" name="meta_description">
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>{{ __('Custom URL structure') }} ({{ __('optional') }})</label>
                            <input type="text" class="form-control" name="slug">
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>{{ __('Custom template file') }} ({{ __('optional') }})</label>
                            <input type="text" class="form-control" name="custom_tpl">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>{{ __('Search terms') }} ({{ __('optional') }})</label>
                            <input type="text" class="form-control" name="search_terms" aria-describedby="searchHelp">
                            <small id="searchHelp" class="form-text text-muted">{{ __('The list of words by which the product should be easily found by search') }}</small>
                        </div>
                    </div>
                </div>

            </div>

            <div class="form-group col-xl-3 col-md-4 col-sm-12 border-left">

                <div class="form-group">
                    <label>{{ __('Product code (SKU)') }}</label>
                    <input type="text" class="form-control" name="sku" aria-describedby="skuHelp">
                    <small id="skuHelp" class="form-text text-muted">{{ __('If not set, a random code will be generated') }}</small>
                </div>

                <div class="form-group">
                    <label>{{ __('Upload main image') }} ({{ __('optional') }})</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="validatedCustomFile" name="image">
                        <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_featured" type="checkbox" name="featured" class="custom-control-input" aria-describedby="featuredHelp">
                        <label for="checkbox_featured" class="custom-control-label"> {{ __('Is featured') }}</label>
                        <small id="featuredHelp" class="form-text text-muted">{{ __('Featured items appear first') }}</small>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_hidden" type="checkbox" name="hidden" class="custom-control-input" aria-describedby="hiddenHelp">
                        <label for="checkbox_hidden" class="custom-control-label"> {{ __('Is hidden') }}</label>
                        <small id="hiddenHelp" class="form-text text-muted">{{ __('Hidden products are accessible only with a direct link') }}</small>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_disable_orders" type="checkbox" name="disable_orders" class="custom-control-input" aria-describedby="disableOrderHelp">
                        <label for="checkbox_disable_orders" class="custom-control-label text-danger"> {{ __('Disable orders') }}</label>
                        <small id="disableOrderHelp" class="form-text text-muted">{{ __('If selected, the product appear on website but ordering is disabled') }}</small>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ __('Notes for disable orders') }}</label>
                    <textarea class="form-control" name="disable_orders_notes" rows="2"></textarea>
                </div>


            </div>

            <div class="col-12">
                <hr>
                <div class="form-group">
                    <button type="submit" name="status" value="active" class="btn btn-success"><i class="fas fa-check"></i> {{ __('Active') }}</button>
                    <button type="submit" name="status" value="inactive" class="btn btn-danger"><i class="fas fa-times"></i> {{ __('Inactive') }}</button>
                </div>

                <small class="form-text text-muted">
                    <b>{{ __('Active') }}:</b> {{ __('Product is displayed on website') }}<br>
                    <b>{{ __('Inactive') }}:</b> {{ __("Product is not available and it is not displayed on website") }}
                </small>
            </div>

        </div><!-- end row -->

    </form>

</div>
<!-- end card-body -->