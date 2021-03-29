<div class="account-header">
    <h3><i class="far fa-credit-card"></i> {{ __('Checkout') }}: {{ $gateway->title }}</h3>
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

@if ($message = Session::get('error'))
<div class="alert alert-danger">
    @if ($message == 'invalid_product') {{ __('Error. Invalid product') }} @endif
    @if ($message == 'error_gateway') {{ __('Error. This payment type is not valid') }} @endif
</div>
@endif


<div class="table-responsive-md">

    <table class="table table-bordered table-hover">
        <tbody>
            <tr>
                <td>
                    <a href="{{ route('user.orders.show', ['lang' => $lang, 'code' => $order->code]) }}">
                        <h5><b>#{{ $order->code }}</b></h5>
                    </a>                   
                    <div class="text-muted text-small mt-2">
                        {{ __('Created at') }}: {{ date_locale($order->created_at, 'datetime') }}
                    </div>
                </td>
            </tr>
        </tbody>

        <tfoot>
            <tr>
                <th colspan="3">
                    <div class="float-right">
                        <h3 class="mb-0">{{ __('Total') }}: {{ price($order->total, currency($order->currency_id)->id)  }}</h3>
                    </div>
                </th>
            </tr>

        </tfoot>
    </table>
</div>

<div class="mb-4"></div>

@if ($order->is_paid==1)
<div class="text-info">{{ __('This order is paid') }}</div>
@else

<h5>{{ __('Click on "Buy Now" button to redirect to PayPal where you can pay using your PayPal ballance or credit card') }}</h5>

<div class="mb-3"></div>

<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="notify_url" value="{{ route('gateway.paypal') }}">
    <input type="hidden" name="business" value="{{ $gateway->vendor_email }}">
    <input type="hidden" name="item_name" value="Order #{{ $order->code }}">
    <input type="hidden" name="item_number" value="{{ $order->code }}">    
    <input type="hidden" name="amount" value="{{ $order->total }}">
    <input type="hidden" name="custom" value="{{ $order->code }}">
    <input type="hidden" name="currency_code" value="{{ currency($order->currency_id)->code }}">
    <input type="hidden" name="no_shipping" value="1">
    <input type="hidden" name="return" value="{{ route('user.orders', ['lang' => $lang ?? null]) }}">
    <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
    <input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">    
</form>
@endif