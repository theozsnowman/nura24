<div class="account-header">
	<h3><i class="fas fa-globe"></i> {{ __('Domains') }} ({{ $domains->total() ?? 0 }})</h3>
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
	@if ($message=='created') {{ __('Domain added') }} @endif
	@if ($message=='deleted') {{ __('Domain deleted') }} @endif
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-info">
	@if ($message=='invalid_domain') {{ __('Error. Invalid domain name') }} @endif
	@if ($message=='exists_licenses') {{ __('Error. Domain can not be deleted because there are licenses for this domain') }} @endif
</div>
@endif


<div class="mb-4">
	<a data-toggle="modal" data-target="#create-domain" class="btn btn-custom btn-lg" href="#"><i class="fas fa-globe"></i> {{ __('Add new domain') }}</a>
	@include('user.custom.modals.create-domain')
</div>

@if($domains->total() == 0)
{{ __("You dont' have any domain.") }}
@else

<div class="table-responsive-md">

	<table class="table table-bordered table-hover">
		<thead>
			<tr>
				<th>{{ __('Domain') }}</th>
				<th width="340">{{ __('License keys') }}</th>
				<th width="50"></th>
			</tr>
		</thead>

		<tbody>

			@foreach ($domains as $domain)
			<tr>

				<td>
					<h4>{{ $domain->domain }}</h4>
					{{ __('Created at') }} {{ date_locale($domain->created_at, 'datetime') }}
				</td>

				<td>
				<a class="btn btn-dark btn-block" href="{{ route('user.custom.licenses', ['dom' => $domain->domain])}}"><i class="fas fa-key"></i> {{ __('Manage license keys') }}</a>
				</td>

				<td>
					<form method="POST" action="{{ route('user.custom.domains.show', ['dom' => $domain->domain]) }}">
						{{ csrf_field() }}
						{{ method_field('DELETE') }}
						<button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$domain->domain}}"><i class="fas fa-trash-alt"></i></button>
					</form>

					<script>
						$('.delete-item-{{$domain->domain}}').click(function(e){
							e.preventDefault() // Don't post the form, unless confirmed
							if (confirm('Are you sure to delete this domain?')) {
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

{{ $domains->links() }}

@endif