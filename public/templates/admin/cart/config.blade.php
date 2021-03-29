<div class="card-header">
	@include('admin.cart.layouts.menu-config')
</div>
<!-- end card-header -->


<div class="card-body">

	@if ($message = Session::get('success'))
	<div class="alert alert-success">
		@if ($message=='updated') {{ __('Updated') }} @endif
	</div>
	@endif

	<div class="row">
		<div class="col-md-4 col-12">

			<form method="post">
				@csrf

				<h4 class="mt-2">{{ __('DUE DATE HOURS') }}</h4>
				<div class="form-group">
					<label>{{ __('The number of hours in which unpaid invoices are kept and waiting to be paid') }}</label>
					<div class="input-group">
						<input type="text" class="form-control" name="cart_invoices_due_date_hours" value="{{ $config->cart_invoices_due_date_hours ?? null }}" aria-describedby="Help">
						<div class="input-group-append">
							<span class="input-group-text">{{ __('hours') }}</span>
						</div>
					</div>
					<small id="Help" class="form-text text-muted">{{ __('The number of hours in which unpaid invoices are kept and waiting to be paid until automatic deletion. Due date can be manually extended for a specific unpaid invoice, if needed.') }}</small>
					<small id="Help" class="form-text text-muted">{{ __('Usual values: 24 (1 day), 72 (3 days), 168 (one week). Leave empty for no time limit.') }}</small>
				</div>

				<h4 class="mt-5">{{ __('PRODUCTS KEEPT IN CLIENTS BASKETS') }}</h4>
				<div class="form-group">
					<label>{{ __('The number of hours in which each product are kept in the basket (shopping cart)') }}</label>
					<div class="input-group">
						<input type="text" class="form-control" name="cart_basket_retention_hours" value="{{ $config->cart_basket_retention_hours ?? null }}" aria-describedby="Help">
						<div class="input-group-append">
							<span class="input-group-text">{{ __('hours') }}</span>
						</div>
					</div>
					<small id="Help" class="form-text text-muted">{{ __('The number of hours in which each product are kept in the clients basket until automatic deletion') }}</small>
					<small id="Help" class="form-text text-muted">{{ __('Usual values: 24 (1 day), 72 (3 days), 168 (one week). Leave empty for no time limit.') }}</small>
				</div>

				<h4 class="mt-5">{{ __('ORDER TERMS AND CONDITIONS') }}</h4>
				<div class="form-group">
					<label>{{ __('URL for Terms and Conditions page') }}</label>
					<div class="input-group">
						<input type="text" class="form-control" name="cart_terms_and_conditions_url" value="{{ $config->cart_terms_and_conditions_url ?? null }}" aria-describedby="shippingHelp">
					</div>
					<small id="shippingHelp" class="form-text text-muted">{{ __('Input the full url address of the Terms and Conditions page. Buyers must check to agree before completing the order.') }}</small>
				</div>

				<h4 class="mt-5">{{ __('DEFAULT DEPARTMENT (FOR TASKS / SERVICES PRODUCT TYPES)') }}</h4>
				<div class="form-group">
					<label>{{ __('Select default department') }} [<a href="{{ route('admin.tickets.departments') }}">{{ __('Manage tickets departments') }}</a>]</label>
					<div class="input-group">
						<select name="cart_default_ticket_department_id" class="form-control" aria-describedby="departmentHelp">
							<option value="">-- {{ __('No department') }}  -</option>
							@foreach($tickets_departments as $department)
							<option @if(($config->cart_default_ticket_department_id ?? null) == $department->id) selected @endif value="{{ $department->id }}">{{ $department->title }}</option>
							@endforeach
						</select>
					</div>
					<small id="departmentHelp" class="form-text text-muted">{{ __('If you sell services, a ticket is automatically created after a client who bought the service. Select default department where this ticket will be created.') }}</small>
				</div>


				<button type="submit" class="btn btn-dark">{{ __('Update') }}</button>

			</form>
		</div>
	</div>

</div>
<!-- end card-body -->