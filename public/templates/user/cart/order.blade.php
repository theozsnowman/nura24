<div class="account-header">
    <h3><i class="far fa-credit-card"></i> {{ __('Order details') }}</h3>
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
                    @if($order->is_paid == 0)
                    <span class="float-right ml-3"><button class="btn btn-danger btn-sm">{{ __('Order is unpaid') }}</button></span>
                    @else
                    <span class="float-right ml-3"><button class="btn btn-success btn-sm">{{ __('Order is paid') }}</button></span>
                    @endif

                    <h5><b>#{{ strtoupper($order->code) }}</b></h5>

                    @foreach(cart_order_items($order->id) as $item)
						<b>{{ $item->item_name }}</b> ({{ price($item->price, currency($order->currency_id)->id) }})<br>
                    @endforeach
                    
                    <div class="text-muted text-small mb-2">
                        {{ __('Created at') }}: {{ date_locale($order->created_at, 'datetime') }}
                        @if($order->is_paid == 0 && $order->due_date)
                        <div class="alert alert-info p-1 mt-2">
                            {{ __('You can pay this order until') }} <b>{{ date_locale($order->due_date, 'datetime') }}</b>
                        </div>            
                        @endif
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

@if($order->is_paid == 0)
<form action="{{ route('cart.checkout', ['lang' => $lang, 'code' => $order->code]) }}" method="post">
    {{ csrf_field() }}

    <div class="row">

        <div class="col-lg-7 col-12">

            <div class="row">

                <div class="col-12">
                    <h3>{{ __('Billing details') }}</h3>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        <label>{{ __('Billing name') }}</label>
                        <input class="form-control" name="billing_name" type="text" value="{{ Auth::user()->name }}" />
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        <label>{{ __('Country') }}</label>
                        <input class="form-control" name="billing_country" type="text" value="{{ $country ?? null }}" />
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        <label>{{ __('Company details (if available)') }}</label>
                        <input class="form-control" name="billing_company" type="text" value="{{ $company ?? null }}" />
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        <label>{{ __('Billing address') }}</label>
                        <input class="form-control" name="billing_address" type="text" value="{{ $billing_address ?? null }}" />
                    </div>
                </div>

            </div>

        </div>


        <div class="col-lg-5 col-12">

            <div class="bg-light p-3">
                <h3>{{ __('Select payment type') }}</h3>
                @foreach($gateways as $gateway)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gateway_id" id="{{ $gateway->id }}" value="{{ $gateway->id }}" required @if(count($gateways)==1) checked @endif>
                    <label class="form-check-label" for="{{ $gateway->id }}">
                        <h4>{{ $gateway->title }}</h4>
                    </label>
                    @if($gateway->client_info)<div class="text-muted small">{{ $gateway->client_info }}</div>@endif
                </div>
                <div class="mb-4"></div>
                @endforeach

                <hr>

                <h3>
                    {{ __('TOTAL PAYMENT') }}: <span id="total">{{ price($order->total, currency($order->currency_id)->id)  }}</span>
                </h3>

                <div class="form-group mt-4">
                    <div class="custom-control custom-checkbox">
                        <input id="checkbox_tc" type="checkbox" name="checkbox_tc" class="custom-control-input" aria-describedby="tcHelp" required>
                        <label for="checkbox_tc" class="custom-control-label"> {{ __('I agree with') }} <a @if($config->cart_terms_and_conditions_url ?? null) target="_blank"
                                href="{{ $config->cart_terms_and_conditions_url}}" @else
                                href="#" @endif>{{ __('Terms and Conditions') }}</a></label>
                        <small id="tcHelp" class="form-text small text-muted">{{ __('You must agree the Terms and Conditions before completing the order.') }}</small>
                    </div>
                </div>

                @if($order->total > 0)
                <button type="submit" class="btn btn-lg btn-block btn-custom"><i class="far fa-credit-card"></i> {{ __('Checkout') }} (<span id="checkout">{{ price($order->total, currency($order->currency_id)->id)  }}</span>)</button>
                @endif

            </div>

        </div>
    </div>
</form>

@else
<h5>{{ __('Payment details') }}</h5>
@if($order->paid_at) {{ __('Paid at') }}: {{ date_locale($order->paid_at, 'datetime') }} <br>@endif
{{ __('Payment type') }}: {{ $order->gateway_title }}<br>
{{ __('Payment code') }}: {{ strtoupper($order->gateway_code) }}
@endif