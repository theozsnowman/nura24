<div class="card-header">
	<h3><i class="fas fa-th"></i> {{ __('Products catalog') }} ({{ $products->total() ?? 0 }})</h3>
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
		@if ($message=='discount_updated') {{ __('Price updated') }} @endif
	</div>
	@endif

	<span class="pull-right"><a href="{{ route('admin.cart.products.create') }}" class="btn btn-primary"><i class="fas fa-plus-square" aria-hidden="true"></i> {{ __('Add new product') }}</a></span>

	<section>
		<form method="get" class="form-inline">
			<input type="text" name="search_terms" placeholder="{{ __('Search product') }}" class="form-control mr-2 @if($search_terms) is-valid @endif" value="{{ $search_terms ?? '' }}" />

			<select name="search_status" class="form-control mr-2 @if($search_status) is-valid @endif">
				<option value="">- {{ __('Active and inactive') }} -</option>
				<option @if ($search_status=='active' ) selected @endif value="active">{{ __('Only active') }}</option>
				<option @if ($search_status=='inactive' ) selected @endif value="inactive">{{ __('Only inactive') }}</option>
			</select>

			<select class="form-control mr-2 @if($search_categ_id) is-valid @endif" name="search_categ_id">
				<option selected="selected" value="">- {{ __('All categories') }} -</option>
				@foreach ($categories as $categ)
				@include('admin.cart.loops.posts-filter-categories-loop', $categ)
				@endforeach
			</select>
			
			<select name="search_featured" class="form-control mr-2 @if($search_featured) is-valid @endif">
				<option value="">- {{ __('All products') }} -</option>
				<option @if ($search_featured==1) selected @endif value="1">{{ __('Only featured') }}</option>
			</select>

			<select name="orderby" class="form-control mr-2 @if($orderby) is-valid @endif">
				<option value="">- {{ __('Default order') }} -</option>
				<option @if ($orderby=='latest' ) selected @endif value="latest">{{ __('Latest product added') }}</option>
				<option @if ($orderby=='price_low' ) selected @endif value="price_low">{{ __('Price low to high') }}</option>
				<option @if ($orderby=='price_high' ) selected @endif value="price_high">{{ __('Price high to low') }}</option>
				<option @if ($orderby=='amount_earned_low' ) selected @endif value="amount_earned_low">{{ __('Amount earned low to high') }}</option>
				<option @if ($orderby=='amount_earned_high' ) selected @endif value="amount_earned_high">{{ __('Amount earned high to low') }}</option>
			</select>

			<button class="btn btn-dark mr-2" type="submit" /><i class="fas fa-check"></i> {{ __('Apply') }}</button>
			<a class="btn btn-light" href="{{ route('admin.cart.products') }}"><i class="fas fa-undo"></i></a>
		</form>
	</section>
	<div class="mb-3"></div>


	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Details') }}</th>
					<th width="150">{{ __('Price') }}</th>
					<th width="220">{{ __('Financial') }}</th>
					<th width="200">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($products as $product)
				<tr @if ($product->status!='active') class="table-warning" @endif>
					<td>
						@if ($product->status=='inactive') <span class="pull-right ml-2"><button type="button" class="btn btn-danger btn-sm disabled">{{ __('Inactive') }}</button></span> @endif
						@if ($product->featured==1) <span class="pull-right ml-2"><button type="button" class="btn btn-success btn-sm disabled"><i class="fas fa-thumbtack"></i> {{ __('Featured') }}</button></span>@endif
						@if ($product->hidden==1) <span class="pull-right ml-2"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Hidden') }}</button></span>@endif

						@if ($product->image)
						<span style="float: left; margin-right:10px;"><img style="max-width:130px; height:auto;" src="{{ image($product->image) }}" /></span>
						@endif

						<h4>{{ $product->title }}</h4>

						<div class="text-muted mb-2">
							{{ __('SKU') }}: {{ strtoupper($product->sku) }}<br>
							{{ __('Created') }}: {{ date_locale($product->created_at) }} by {{ $product->author_name}}
						</div>

						<div class="clearfix"></div>
						<div class="mt-2">

						@if($product->categ_id)
						{{ __('Category') }}:
						@foreach(breadcrumb($product->categ_id, 'cart') as $item)
						<a target="_blank" href="{{ cart_url($item->id) }}"><b>{{ $item->title }}</b></a> @if(!$loop->last) / @endif
						@endforeach
						@endif

						<br>
						{{ __('Product type') }}: 
						@if($product->type == 'download'){{ __('Downloadable') }}@endif
						@if($product->type == 'task'){{ __('Task / Service') }}@endif
					
						<div class="text-muted mt-2">
							ID: {{ $product->id }}<br>
							{{ __('Slug') }}: {{ $product->slug }}
							@if ($product->custom_tpl)
							<br>{{ __('Custom template file') }}: {{ $product->custom_tpl }}
						@endif
						</div>
					</td>

					<td>
						<h4>
							@if($product->price==0){{ __('FREE') }} @else
							{{ price($product->price) }}
							@endif
						</h4>						
					</td>

					<td>
						<h5>{{ __('Amount earned') }}: {{ price($product->amount_total) ?? 0 }}</h5>
						<div class="mt-3"></div>
						<h5>{{ $product->count_paid_orders }} {{ __('orders paid') }}</h5>
						@if($product->count_unpaid_orders>0)<h5 class="text-danger">{{ $product->count_unpaid_orders }} {{ __('orders pending') }}</h5>@endif
					</td>

					<td>
						<a href="{{ route('admin.cart.products.show', ['id' => $product->id]) }}" class="btn btn-dark btn-sm btn-block mb-3"><i class="fas fa-edit"></i> {{ __('Manage product') }}</a>

						<form method="POST" action="{{ route('admin.cart.products.show', ['id' => $product->id]) }}">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}
							<button type="submit" class="float-right btn btn-danger btn-sm btn-block delete-item-{{$product->id}}"><i class="fas fa-times"></i> {{ __('Delete product') }}</button>
						</form>


						<script>
							$('.delete-item-{{$product->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm('Are you sure to delete this item?')) {
										$(e.target).closest('form').submit() // Post the surrounding form
									}
								});
						</script>
					</td>
				</tr>
				@endforeach

			</tbody>
		</table>
	</div>

	{{ $products->appends(['search_terms' => $search_terms, 'search_status' => $search_status, 'search_categ_id' => $search_categ_id, 'search_featured' => $search_featured, 'orderby' => $orderby])->links() }}

</div>
<!-- end card-body -->