<div class="account-header">
    <h3><i class="fas fa-shopping-cart"></i> {{ __('Shopping cart') }}</h3>
</div>
<!-- end card-header -->

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if ($message = Session::get('success'))
<div class="alert alert-success">
    @if ($message=='created') {{ __('Item added to cart') }} @endif
    @if ($message=='updated') {{ __('Cart updated') }} @endif
    @if ($message=='deleted') {{ __('Item deleted from cart') }} @endif
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger">
    @if ($message=='invalid_product') {{ __('Error. Invalid product') }} @endif
    @if ($message=='error_user_role') {{ __('Error. You do not have access to shopping cart') }} @endif
    @if ($message=='error_payment_type') {{ __('Error. This payment type is not valid') }} @endif
</div>
@endif


@if(count($shopping_cart['products'])==0)<div class="mb-3">{{ __("You don't have any item in shopping cart") }}</div>
@else
<div class="table-responsive-md">

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>{{ __('Product details') }}</th>
                <th width="150">{{ __('Price') }}</th>
                <th width="120"></th>
            </tr>
        </thead>

        <tbody>

            @foreach ($shopping_cart['products'] as $cart_item)
            <tr>
                <td>
                    <a href="{{ cart_product_url($cart_item->id) }}">
                        <h4><b>{{ $cart_item->title }}</b></h4>
                    </a>
                    @if ($cart_item->status != 'active' or $cart_item->orders_disabled == 1) <div class="alert alert-danger">{{ __('Warning. Product no longer available') }}</div> @endif

                </td>

                <td>
                    {{ price($cart_item->price) }}
                </td>

                <td>
                    <form method="POST" action="{{ route('shopping_cart.delete', ['lang' => $lang, 'id' => $cart_item->item_id]) }}">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="float-right btn btn-light text-danger btn-block btn-sm delete-item-{{$cart_item->item_id}}"><i class="fas fa-trash-alt"></i> {{ __('Remove') }}</button>
                    </form>

                    <script>
                        $('.delete-item-{{ $cart_item->item_id }}').click(function(e){
                                e.preventDefault() // Don't post the form, unless confirmed
                                if (confirm("{{ __('Are you sure to delete this product?') }}")) {
                                    $(e.target).closest('form').submit() // Post the surrounding form
                                }
                            });
                    </script>
                </td>

            </tr>
            @endforeach

        </tbody>

        <tfoot>
            <tr>
                <th colspan="3">
                    <div class="float-right">
                        <h3>{{ __('Total') }}: {{ price($shopping_cart['total']) }} </h3>
                    </div>
                </th>
            </tr>

        </tfoot>
    </table>
</div>


<form action="{{ route('cart.store_order', ['lang' => $lang]) }}" method="post">
    {{ csrf_field() }}

    @if($shopping_cart['total']>0)
    <button type="submit" class="btn btn-lg btn-custom float-right"><i class="fas fa-money-check"></i> {{ __('Complete the order') }} (<span id="checkout">{{ price($shopping_cart['total']) }})</span>
    </button>
    @endif

</form>

@endif