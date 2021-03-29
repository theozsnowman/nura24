<div class="card-header">
	@include('admin.cart.layouts.menu-config')
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
		@if ($message=='updated') {{ __('Updated') }} @endif
		@if ($message=='deleted') {{ __('Deleted') }} @endif
	</div>
	@endif

	@if ($message = Session::get('error'))
	<div class="alert alert-danger">
		@if ($message=='duplicate') {{ __('Error. This gateway exists') }} @endif
		@if ($message=='delete_protected') {{ __('Error. This gateway can not be deleted') }} @endif
		@if ($message=='exists_invoices') {{ __('Error. This gateway can not be deleted because there are invoices asigned to this gateway') }} @endif
	</div>
	@endif

	<span class="pull-right mb-3"><button data-toggle="modal" data-target="#create-gateway" class="btn btn-primary"><i class="fas fa-plus-square"></i> {{ __('Create gateway') }}</button></span>
	@include('admin.cart.modals.create-gateway')


	<div class="table-responsive-md">

		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th width="50">#</th>
					<th>{{ __('Details') }}</th>
					<th width="140">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>

				@foreach ($gateways as $gateway)
				<tr @if ($gateway->active==0) class="table-warning" @endif>

					<td>
						{{ $gateway->position }}
					</td>

					<td>
						@if ($gateway->active==0) <span class="pull-right">&nbsp;<button type="button" class="btn btn-danger btn-sm disabled">{{ __('Inactive') }}</button> </span>@endif
						@if ($gateway->hidden==1) <span class="pull-right">&nbsp;<button type="button" class="btn btn-warning btn-sm disabled">{{ __('Hidden') }}</button> </span>@endif
						@if ($gateway->instant==1) <span class="pull-right">&nbsp;<button type="button" class="btn btn-success btn-sm disabled">{{ __('Instant') }}</button> </span>@endif

						@if ($gateway->logo)
						<div class="mb-3"><img style="max-width:180px; height:auto;" src="{{ asset('uploads/'.$gateway->logo) }}" /></div>
						@endif

						<h4>{{ $gateway->title }}</h4>
						@if ($gateway->vendor_email) <div class="text-muted">{{ __('Vendor email') }}: {{ $gateway->vendor_email }}</div>@endif
						@if ($gateway->checkout_file) <div class="text-muted">{{ __('Checkout file') }}: {{ $gateway->checkout_file }}</div>@endif
						@if ($gateway->client_info) <div class="text-muted">{!! nl2br($gateway->client_info) !!}</div>@endif

					</td>

					<td>
						<button data-toggle="modal" data-target="#update-gateway-{{ $gateway->id }}" class="btn btn-primary btn-sm btn-block mb-2"><i class="fas fa-pen" aria-hidden="true"></i> {{ __('Update') }}</button>
						@include('admin.cart.modals.update-gateway')

						@if($gateway->protected == 0)
						<form method="POST" action="{{ route('admin.cart.config.gateways', ['id' => $gateway->id]) }}">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}
							<button type="submit" class="float-right btn btn-danger btn-sm btn-block delete-item-{{$gateway->id}}"><i class="fas fa-times"></i> {{ __('Delete') }}</button>
						</form>


						<script>
							$('.delete-item-{{$gateway->id}}').click(function(e){
										e.preventDefault() // Don't post the form, unless confirmed
										if (confirm('Are you sure to delete this payment gateway?')) {
											$(e.target).closest('form').submit() // Post the surrounding form
										}
									});
						</script>
						@endif
					</td>
				</tr>
				@endforeach

			</tbody>
		</table>
	</div>

	{{ $gateways->links() }}

</div>
<!-- end card-body -->