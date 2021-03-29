<div class="card-header">
    <h3><i class="far fa-file-alt"></i> {{ __('Orders') }} ({{ $orders->total() ?? 0 }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

    @if(! check_module('cart'))
	<div class="alert alert-danger">
		{{ __('Warning. eCommerce module is disabled. You can manege content, but module is disabled for visitors and registered users') }}. <a href="{{ route('admin.config.modules') }}">{{ __('Manage modules') }}</a>
	</div>
    @endif
    
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
        @if ($message=='created') {{ __('Created') }} @endif
        @if ($message=='updated') {{ __('Updated') }} @endif
        @if ($message=='deleted') {{ __('Deleted') }} @endif
    </div>
    @endif

    @if ($message = Session::get('error'))
    <div class="alert alert-danger">
        @if ($message == 'is_paid') {{ __('Paid orders can not be deleted') }} @endif
    </div>
    @endif

    <section>
        <form action="{{ route('admin.cart.orders') }}" method="get" class="form-inline">

            <input type="text" name="search_terms" placeholder="Search order" class="form-control mr-2 @if($search_terms) is-valid @endif" value="{{ $search_terms ?? '' }}" />

            <input type="text" name="search_user" placeholder="Search customer" class="form-control mr-2 @if($search_user) is-valid @endif" value="{{ $search_user ?? '' }}" />

            <select name="search_payment_status" class="form-control mr-2 @if($search_payment_status) is-valid @endif">
                <option selected="selected" value="">- {{ __('Any payment status') }} -</option>
                <option @if($search_payment_status=='unpaid' ) selected @endif value="unpaid"> {{ __('Unpaid orders') }}</option>
                <option @if($search_payment_status=='paid' ) selected @endif value="paid"> {{ __('Paid orders') }}</option>
            </select>

            <button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
            <a class="btn btn-light" href="{{ route('admin.cart.orders') }}"><i class="fas fa-undo"></i></a>
        </form>
    </section>
    <div class="mb-3"></div>

    <div class="table-responsive-md">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>{{ __('Order details') }}</th>
                    <th width="400">{{ __('Customer details') }}</th>
                    <th width="250">{{ __('Amount') }}</th>
                    <th width="100">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($orders as $order)
                <tr>
                    <td>
                        <span class="float-right ml-2">
                            @if($order->is_paid == 0)<button type="button" class="btn btn-danger btn-sm btn-block">{{ 'Unpaid'}}</button>@endif
                            @if($order->is_paid == 1)<button type="button" class="btn btn-success btn-sm btn-block">{{ 'Paid'}}</button>@endif
                        </span>


                        <a href="{{ route('admin.cart.orders.show', ['id' => $order->id]) }}">
                            <h5>#{{ $order->code }}</h5>
                        </a>

                        @foreach(cart_order_items($order->id) as $item)
                        <b>{{ $item->item_name }}</b> ({{ price($item->price, currency($order->currency_id)->id) }})
                        @if($item->ticket_id) <a class="btn btn-sm btn-light ml-2 text-success" href="{{ route('user.tickets') }}">{{ __('Manage service') }}</a> @endif
                        <div class="mb-1"></div>
                        @endforeach

                        <div class="text-muted text-small mt-2">
                            {{ __('Created at') }}: {{ date_locale($order->created_at, 'datetime') }}

                            @if($order->is_paid == 0 && $order->due_date)
                            <div class="clearfix"></div>
                            <div class="alert alert-info p-1 mt-2" style="display:inline-block;">
                                {{ __('Client can pay until') }} <b>{{ date_locale($order->due_date, 'datetime') }}</b>
                            </div>
                            @endif
                        </div>

                    </td>

                    <td>
                        @if ($order->customer_avatar)
                        <span class="float-left mr-2"><img style="max-width:80px; height:auto;" src="{{ image($order->customer_avatar) }}" /></span>
                        @endif
                        <h5>{{ $order->customer_name }}</h5>
                        {{ $order->customer_email }}
                        <div class="mb-1"></div>
                        {{ $order->count_orders }} {{ __('orders') }}
                        @if($order->count_unpaid_orders > 0) <span class="text-danger">({{ $order->count_unpaid_orders }} {{ __('unpaid orders') }})</span> @endif
                    </td>

                    <td>
                        <h4>{{ price($order->total) }}</h4>

                        @if($order->paid_at) 
                        <div class="small">
                        {{ __('Paid') }}: {{ date_locale($order->paid_at, 'datetime') }} <br>
                        {{ __('Payment type') }}: {{ $order->gateway_title }}<br>
                        {{ __('Payment code') }}: {{ strtoupper($order->gateway_code) }}
                        </div>
                        @endif
                    </td>

                    <td>
                        <div class="d-flex">

                            <a href="{{ route('admin.cart.orders.show', ['id' => $order->id]) }}" class="btn btn-dark btn-sm mr-3"><i class="fas fa-search"></i></a>

                            @if($order->is_paid!=1)
                            <form method="POST" action="{{ route('admin.cart.orders.show', ['id'=>$order->id]) }}">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$order->id}}"><i class="fas fa-trash-alt"></i></button>
                            </form>

                            <script>
                                $('.delete-item-{{$order->id}}').click(function(e){
                                    e.preventDefault() // Don't post the form, unless confirmed
                                    if (confirm('Are you sure to delete this item?')) {
                                        $(e.target).closest('form').submit() // Post the surrounding form
                                    }
                                });
                            </script>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    {{ $orders->appends(['search_terms' => $search_terms, 'search_user' => $search_user, 'search_status' => $search_status, 'search_payment_status' => $search_payment_status])->links() }}

</div>
<!-- end card-body -->