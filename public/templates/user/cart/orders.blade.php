<div class="account-header">
	<h3><i class="far fa-credit-card"></i> {{ __('Orders') }} ({{ $orders->total() ?? 0 }})</h3>
</div>
<!-- end card-header -->


@if ($message = Session::get('error'))
<div class="alert alert-danger">
	@if ($message=='error_order') {{ __("Error. You can't delete this order") }} @endif
</div>
@endif

@if ($message = Session::get('success'))
<div class="alert alert-info">
	@if ($message=='created') {{ __('Order created') }} @endif
	@if ($message=='deleted') {{ __('Order deleted') }} @endif
</div>
@endif

<div class="table-responsive-md">

	<table class="table table-bordered table-hover">
		<thead>
			<tr>
				<th>{{ __('Order details') }}</th>
				<th width="140">{{ __('Amount') }}</th>
				<th width="160">{{ __('Payment details') }}</th>
			</tr>
		</thead>

		<tbody>

			@foreach ($orders as $order)
			<tr>
				<td>
					<div class="float-right">
						@if ($order->is_paid == 0)
						<a class="text-danger font-weight-bold" href="{{ route('user.orders.show', ['lang' => $lang, 'code' => $order->code]) }}">{{ __('Order unpaid') }}</a>
						@endif
					</div>

					<a href="{{ route('user.orders.show', ['lang' => $lang, 'code' => $order->code]) }}">
						<h4><b>#{{ strtoupper($order->code) }}</b></h4>						
					</a>				

					@foreach(cart_order_items($order->id) as $item)
						<b>{{ $item->item_name }}</b> ({{ price($item->price, currency($order->currency_id)->id) }})
						@if($item->ticket_id) <a class="btn btn-sm btn-light ml-2 text-success" href="{{ route('user.tickets', ['lang' => $lang]) }}">{{ __('Manage service') }}</a> @endif
						<div class="mb-1"></div>
					@endforeach

					<div class="text-muted text-small mt-2">
						{{ __('Created at') }}: {{ date('M d Y, H:i', strtotime($order->created_at)) }}						

						@if($order->is_paid == 0 && $order->due_date)
						<div class="alert alert-info p-1 mt-2">
						{{ __('You can pay this order until') }} <b>{{ date_locale($order->due_date, 'datetime') }}</b>
						</div>
                        @endif
					</div>
				</td>

				<td>
					<h4>{{ price($order->total, currency($order->currency_id)->id) }}</h4>					
				</td>

				<td>
					@if ($order->is_paid == 0)										
					<a class="btn btn-danger btn-sm btn-block" href="{{ route('user.orders.show', ['lang' => $lang, 'code' => $order->code]) }}">{{ __('Pay order') }}</a>
				
					<form method="POST" action="{{ route('user.orders.show', ['lang' => $lang, 'code' => $order->code]) }}">
						{{ csrf_field() }}
						{{ method_field('DELETE') }}
						<a type="submit" class="mt-3 text-danger delete-item-{{ $order->code }}"><i class="fas fa-trash-alt"></i> {{ __('Delete order') }}</a>
					</form>
					<script>
						$('.delete-item-{{$order->code}}').click(function(e){
								e.preventDefault() // Don't post the form, unless confirmed
								if (confirm("{{ __('Are you sure to delete this order?') }}")) {
									$(e.target).closest('form').submit() // Post the surrounding form
								}
							});
					</script>

					@else
					<a class="btn btn-success btn-sm btn-block" href="{{ route('user.orders.show', ['lang' => $lang, 'code' => $order->code]) }}">{{ __('Paid') }}</a>
					@endif
				</td>
			</tr>
			@endforeach

		</tbody>
	</table>
</div>

{{ $orders->links() }}

</div>