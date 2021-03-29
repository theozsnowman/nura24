<div class="card-header">
    <h3><i class="far fa-file-alt"></i> {{ __('Order details') }} #{{ $order->code }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        @if ($message=='updated') {{ __('Updated') }} @endif
    </div>
    @endif

    <div class="table-responsive-md">

        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td>
                        @if($order->is_paid == 0)
                        <button data-toggle="modal" data-target="#update-order-{{ $order->id }}" class="btn btn-primary btn-sm float-right mr-2"><i class="fas fa-pen" aria-hidden="true"></i>
                            {{ __('Edit order') }}</button>
                        @include('admin.cart.modals.update-order')
                        @endif

                        <h4><b>#{{ strtoupper($order->code) }}</b>
                            @if($order->is_paid == 0)
                            <span class="ml-2"><button class="btn btn-danger btn-sm py-0">{{ __('Order is unpaid') }}</button></span>
                            @else
                            <span class="ml-2"><button class="btn btn-success btn-sm py-0">{{ __('Order is paid') }}</button></span>
                            @endif
                        </h4>

                        @foreach(cart_order_items($order->id) as $item)
                        <h5><b>{{ $item->item_name }}</b> ({{ price($item->price, currency($order->currency_id)->id) }})<h5>
                                @endforeach

                                <div class="text-muted text-small mb-2">
                                    {{ __('Created at') }}: {{ date_locale($order->created_at, 'datetime') }}
                                    @if($order->is_paid == 0 && $order->due_date)
                                    <div class="alert alert-info p-1 mt-2">
                                        {{ __('Client can pay this order until') }} <b>{{ date_locale($order->due_date, 'datetime') }}</b>
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

    <div class="row">

        <div class="col-lg-7 col-12">


                <form action="{{ route('admin.cart.orders.update_notes', ['id' => $order->id]) }}" method="post">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Notes for client') }}</label>
                                <textarea class="form-control" name="client_notes" rows="4">{{ $order->client_notes }}</textarea>  
                                <small class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ __('This notes are visible for client') }}</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Internal notes') }}</label>                                
                                <textarea class="form-control" name="staff_notes" rows="4">{{ $order->staff_notes }}</textarea>                                   
                                <small class="text-info">{{ __('This notes are visible for administrators and staff who have access to orders. This notes are NOT visible for client') }}</small>        
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-dark">{{ __('Update notes') }}</button>
                        </div>

                    </div>

                </form>


        </div>


        <div class="col-lg-5 col-12">

            <div class="bg-light p-3">

                @if($order->is_paid == 0)

                <form action="{{ route('admin.cart.orders.update_payment', ['id' => $order->id]) }}" method="post">
                    {{ csrf_field() }}
                    <h3>{{ __('Manually pay this order') }}</h3>
                    <h5>{{ __('Select payment type') }}</h5>
                    @foreach($gateways as $gateway)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gateway_id" id="{{ $gateway->id }}" value="{{ $gateway->id }}" required @if(count($gateways)==1) checked @endif>
                        <label class="form-check-label" for="{{ $gateway->id }}">
                            <h4>{{ $gateway->title }}</h4>
                        </label>
                        @if($gateway->hidden == 1)<div class="text-info font-weight-bold">{{ __('Hidden payment type') }}</div>@endif
                        @if($gateway->active == 0)<div class="text-info font-weight-bold">{{ __('Inactive payment type') }}</div>@endif
                        @if($gateway->client_info)<div class="text-muted small">{{ $gateway->client_info }}</div>@endif
                    </div>
                    <div class="mb-4"></div>
                    @endforeach

                    <hr>

                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label>{{ __('Payment code') }}</label>
                            <input class="form-control" name="gateway_code">
                            <small>{{ __('If not provided, a random code will be generated') }}</small>
                        </div>
                    </div>

                    @if($order->total > 0)
                    <button type="submit" class="btn btn-danger"><i class="fas fa-check"></i> {{ __('Set this order as paid') }}</button>
                    @endif
                </form>

                @else
                <h5>{{ __('Payment details') }}</h5>
                {{ __('Paid at') }}: {{ date_locale($order->paid_at, 'datetime') }}<br>
                {{ __('Payment type') }}: {{ $order->gateway_title }}<br>
                {{ __('Payment code') }}: {{ strtoupper($order->gateway_code) }}

                @endif

            </div>

        </div>
    </div>

</div>
<!-- end card-body -->