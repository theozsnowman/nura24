<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update_product_discount_{{ $product->id }}" aria-hidden="true" id="update_product_discount_{{ $product->id }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('admin.cart.product.discount', ['id' => $product->id]) }}" method="post">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Update discount') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>{{ __('New price') }}</label>

                                <div class="input-group">
                                    <input type="text" class="form-control" name="new_price" aria-describedby="discountHelp" value="{{ $product->price }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ nura_default_currency()->code}}</span>
                                        <span class="input-group-text">Example: 9.50</span>
                                    </div>
                                </div>
                                <small id="discountHelp" class="form-text text-muted">{{ __('Leave empty for no discount (previous price will be active)') }}</small>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Update discount') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>