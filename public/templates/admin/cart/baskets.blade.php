<div class="card-header">
	<h3><i class="fas fa-shopping-cart"></i> {{ __('All products from clients baskets') }} ({{ $items->total() ?? 0 }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

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
		@if ($message=='deleted') {{ __('Deleted') }} @endif
	</div>
	@endif

	<div class="alert alert-success">
		{{ __('In this page are displayed products added by customers in their shopping carts') }}
	</div>

	<section>
		<form method="get" class="form-inline">
			<input type="text" name="search_terms" placeholder="{{ __('Search product') }}" class="form-control mr-2 @if($search_terms) is-valid @endif" value="{{ $search_terms ?? '' }}" />

			<select class="form-control mr-2 @if($search_user_id) is-valid @endif" name="search_user_id">
				<option selected="selected" value="">- {{ __('All customers') }} -</option>
				@foreach ($customers as $customer)
				<option @if ($search_user_id==$customer->id) selected="selected" @endif value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->email }}</option>
				@endforeach
			</select>

			<select class="form-control mr-2 @if($search_product_id) is-valid @endif" name="search_product_id">
				<option selected="selected" value="">- {{ __('All products') }} -</option>
				@foreach ($products as $product)
				<option @if ($search_product_id==$product->id) selected="selected" @endif value="{{ $product->id }}">{{ $product->title }}</option>
				@endforeach
			</select>

			<button class="btn btn-dark mr-2" type="submit" /><i class="fas fa-check"></i> {{ __('Apply') }}</button>
			<a class="btn btn-light" href="{{ route('admin.cart.baskets') }}"><i class="fas fa-undo"></i></a>
		</form>
	</section>
	<div class="mb-3"></div>


	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th width="450">{{ __('Product') }}</th>
					<th width="120">{{ __('Quantity') }}</th>
					<th>{{ __('Customer') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($items as $item)
				<tr>
					<td>
						@if ($item->product_image)
						<span style="float: left; margin-right:10px;"><img style="max-width:80px; height:auto;" src="{{ asset('uploads/'.nura_thumb($item->product_image)) }}" /></span>
						@endif

						<h4>{{ $item->product_title }}</h4>						
						<div class="text-muted">
							{{ __('Price') }}: {{ $item->product_price }} {{ nura_default_currency()->code }}
							<br>
							{{ __('SKU') }}: {{ strtoupper($item->product_sku) }}<br>
						</div>

					</td>

					<td>
						<h4>
							{{ $item->quantity }}
						</h4>
					</td>

					<td>
						<h4>
							{{ $item->client_name }}
						</h4>
					</td>

				</tr>
				@endforeach

			</tbody>
		</table>
	</div>

	{{ $items->links() }}

</div>
<!-- end card-body -->