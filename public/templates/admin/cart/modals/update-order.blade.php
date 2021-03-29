<?php
debug_backtrace() || die ("Direct access not permitted"); 
?>
<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="update-order-{{ $order->id }}" aria-hidden="true" id="update-order-{{ $order->id }}">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post" action="{{ route('admin.cart.orders.update_items', ['id' => $order->id]) }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="update-order-{{ $order->id }}">{{ __('Update order') }}</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Due date') }}</label>
                                <input class="form-control" name="due_date" type="text" id="due_date" aria-describedby="duedateHelpBlock" autocomplete="off" value="{{ $order->due_date }}" />
                                <small id="duedateHelpBlock" class="form-text text-muted">
                                    {{ __('Leave empty if order have not any due date') }}
                                </small>
                            </div>
            
                            <script>
                                $('#due_date').datepicker({
                                            uiLibrary: 'bootstrap4',
                                            iconsLibrary: 'fontawesome',
                                            format: 'yyyy-mm-dd' 
                                        });
                            </script>
                        </div>


                        @foreach(cart_order_items($order->id) as $item)
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Item name') }}</label>
                                <input class="form-control" name="item_name_{{ $item->id }}" type="text" required value="{{ $item->item_name }}">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Item price') }}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="price_{{ $item->id }}" aria-describedby="priceHelp" required value="{{ $item->price }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ currency($order->currency_id)->code}}</span>
                                    </div>
                                </div>
                            </div>
                            <hr> 
                        </div>                                                     
                        @endforeach                        

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Update order') }}</button>
                </div>

            </form>

        </div>
    </div>
</div>