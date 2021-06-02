<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal hide fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="added-to-cart" aria-hidden="true" id="added-to-cart">
    <div class="modal-dialog">
        <div class="modal-content">
            
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Added to cart') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('Close') }}</span></button>
                </div>

                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h4>{{ $product->title}} {{ __('added to cart') }}</h4>
                        </div>
                    </div>
                </div>
                

                <div class="modal-footer">                   
                    <a href="{{ route('cart.basket') }}" class="btn btn-template-outlined"><i class="fas fa-shopping-cart"></i> {{ __('Go to shopping cart') }}</a>
                    <button type="button" data-dismiss="modal" class="btn btn-dark">{{ __('Continue shopping') }}</button>
                </div>

        </div>
    </div>
</div>