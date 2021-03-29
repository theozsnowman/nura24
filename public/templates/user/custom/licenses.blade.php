<div class="account-header">
	<h3><i class="fas fa-key"></i> {{ __('Licenses for ') }} {{ $dom }} ({{ $licenses->total() ?? 0 }})</h3>
</div>
<!-- end card-header -->

@if(logged_user()->count_unpaid_orders > 0)
<div class="alert alert-danger">
	<div class="font-weight-bold mb-2">{{ logged_user()->count_unpaid_orders }} {{ __('unpaid orders') }}.</div>
	<a href="{{ route('user.orders') }}">{{ __('My orders') }}</a>
</div>
@endif

@if ($message = Session::get('success'))
<div class="alert alert-info">
	@if ($message=='created') {{ __('License created') }} @endif
</div>
@endif


<div class="mb-4">
	<a class="btn btn-custom btn-lg" href="{{ route('user.custom.licenses.new', ['dom' => $dom]) }}"><i class="fas fa-key"></i> {{ __('Create new license key') }}</a>
</div>

@if($licenses->total() == 0)
{{ __("You dont' have any license for ") }} {{ $dom }}
@else

<div class="table-responsive-md">

	<table class="table table-bordered table-hover">
		<thead>
			<tr>
				<th>{{ __('Domain') }}</th>
				<th width="360">{{ __('License key') }}</th>
				<th width="250">{{ __('Expire') }}</th>
			</tr>
		</thead>

		<tbody>

			@foreach ($licenses as $license)
			<tr>

				<td>
					<h4>{{ $license->domain }}</h4>
					{{ __('Created at') }} {{ date_locale($license->created_at, 'datetime') }}										
				</td>

				<td>
					@if($license->is_paid == 1)
					<h5>{{ $license->license_key }}</h5>
					@else 
					<a href="{{ route('user.orders') }}" class="font-weight-bold text-danger">{{ __('You must pay the order to view license key') }}</a>
					@endif
				</td>

				<td>
					@if(! $license->expire_at)
					{{ __('No expiration') }}
					@else
					{{ date_locale($license->expire_at) }}
					@endif
				</td>								
			</tr>
			@endforeach

		</tbody>
	</table>
</div>

{{ $licenses->links() }}

@endif