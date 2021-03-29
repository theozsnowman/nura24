<div class="card-header">   
    <h3>{{ $product->title }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cart.products') }}">{{ __('Products catalog') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Update product') }}</li>
        </ol>                                
    </nav>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="mb-4">
        @include('admin.cart.layouts.menu-product')
    </div>

    <form method="post" enctype="multipart/form-data" {{ route('admin.cart.products.show', ['id' => $product->id]) }}>
        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-xl-9 col-md-8 col-sm-12">

                <div class="row">
                    <div class="col-md-9 col-sm-8 col-12">
                        <div class="form-group">
                            <label>{{ __('Product title') }}</label>
                            <input class="form-control" name="title" type="text" required value="{{ $product->title }}">
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-4 col-12" <div class="form-group">
                        <label>{{ __('Category') }}</label>
                        <select class="form-control" name="categ_id" required>
                            <option selected="selected" value="">- {{ __('No category') }} -</option>
                            @foreach ($categories as $categ)
                            @include('admin.cart.loops.post-edit-select-loop', $categ)
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-12">
                        <div class="form-group">
                            <label>{{ __('Price') }}</label>

                            <div class="input-group">
                                <input type="text" class="form-control" aria-label="Amount (with dot and two decimal places)" name="price" aria-describedby="priceHelp" required value="{{ $product->price }}">
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ default_currency()->code}}</span>
                                    <span class="input-group-text">{{ __('Example') }}: 9.50</span>
                                </div>
                            </div>
                            <small id="priceHelp" class="form-text text-muted">{{ __('Input 0 for free product') }}</small>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label>{{ __('Summary') }} ({{ __('optional') }})</label>
                    <textarea rows="3" class="form-control" name="summary">{{ $product->summary }}</textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('Product description') }}</label>
                    <textarea class="form-control editor" name="content">{{ $product->content }}</textarea>
                </div>


                <div class="form-group">
                    <label>{{ __('Help info') }} ({{ __('optional') }})</label>
                    <textarea rows="3" class="form-control" name="help_info">{{ $product->help_info }}</textarea>
                </div>


                <h4>{{ __('SEO') }}</h4>

                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>{{ __('Custom Meta title') }} ({{ __('optional') }})</label>
                            <input type="text" class="form-control" name="meta_title" value="{{ $product->meta_title }}">
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>{{ __('Custom Meta description') }} ({{ __('optional') }})</label>
                            <input type="text" class="form-control" name="meta_description" value="{{ $product->meta_description }}">
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>{{ __('Custom URL structure') }} ({{ __('optional') }})</label>
                            <input type="text" class="form-control" name="slug" value="{{ $product->slug }}">
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>{{ __('Custom template file') }} ({{ __('optional') }})</label>
                            <input type="text" class="form-control" name="custom_tpl" value="{{ $product->custom_tpl }}">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>{{ __('Search terms') }} ({{ __('optional') }})</label>
                            <input type="text" class="form-control" name="search_terms" value="{{ $product->search_terms }}" aria-describedby="searchHelp">
                            <small id="searchHelp" class="form-text text-muted">{{ __('The list of words by which the product should be easily found by search') }}</small>
                        </div>
                    </div>
                </div>

            </div>

            <div class="form-group col-xl-3 col-md-4 col-sm-12 border-left">

                <div class="form-group">
                    <label>{{ __('Product code (SKU)') }}</label>
                    <input type="text" class="form-control" name="sku" aria-describedby="skuHelp" value="{{ $product->sku }}">
                    <small id="skuHelp" class="form-text text-muted">{{ __('If not set, a random code will be generated') }}</small>
                </div>

                <div class="form-group">
                    <label>{{ __('Change main image') }} ({{ __('optional') }})</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="validatedCustomFile" name="image">
                        <label class="custom-file-label" for="validatedCustomFile">{{ __('Choose file') }}...</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_featured" type="checkbox" name="featured" class="custom-control-input" aria-describedby="featuredHelp" @if ($product->featured==1) checked @endif>
                        <label for="checkbox_featured" class="custom-control-label"> {{ __('Is featured') }}</label>
                        <small id="featuredHelp" class="form-text text-muted">{{ __('Featured items appear first') }}</small>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_hidden" type="checkbox" name="hidden" class="custom-control-input" aria-describedby="hiddenHelp" @if ($product->hidden==1) checked @endif>
                        <label for="checkbox_hidden" class="custom-control-label"> {{ __('Is hidden') }}</label>
                        <small id="hiddenHelp" class="form-text text-muted">{{ __('Hidden items are accessible only with a direct link') }}</small>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_disable_orders" type="checkbox" name="disable_orders" class="custom-control-input" aria-describedby="disableOrderHelp" @if ($product->disable_orders==1) checked
                        @endif>
                        <label for="checkbox_disable_orders" class="custom-control-label text-danger"> {{ __('Disable orders') }}</label>
                        <small id="disableOrderHelp" class="form-text text-muted">{{ __('If selected, the product appear on website but ordering is disabled') }}</small>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ __('Notes for disable orders') }}</label>
                    <textarea class="form-control" name="disable_orders_notes" rows="2">{{ $product->disable_orders_notes }}</textarea>
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