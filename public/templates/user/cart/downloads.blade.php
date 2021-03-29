<div class="account-header">
	<h3><i class="fas fa-download"></i> {{ __('My downloads') }} - {{ count($downloads) ?? 0 }}</h3>
</div>

@if(logged_user()->count_unpaid_orders>0)
<div class="alert alert-danger">
    <a class="font-weight-bold text-danger" href="{{ route('user.orders', ['lang' => $lang]) }}">{{ logged_user()->count_unpaid_orders }} {{ __('unpaid orders') }}</a>
    <hr>
    {{ __('Products or services related to unpaid order will be delivered after you pay the order') }}.<br>
    {{ __('If you ordered downloadable products (software), the downloads will be available automatically right after payment') }}.<br>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger">
	@if ($message=='error_file') {{ __('Error. This file is not available for download') }} @endif
</div>
@endif


<div class="table-responsive-md">

	<table class="table table-bordered table-hover">
		<thead>
			<tr>
				<th>{{ __('File details') }}</th>
				<th width="200">{{ __('Download') }}</th>
			</tr>
		</thead>

		<tbody>

			@foreach ($downloads as $download)
			<tr>
				<td>
					<h4>{{ $download->title }}</h4>
					{{ __('Version') }}: {{ $download->version }}
				</td>

				<td>
					<a href="{{ route('user.download', ['id' => $download->id]) }}" class="btn btn-success btn-block"><i class="fas fa-download"></i> {{ __('DOWNLOAD') }}</a>
				</td>

			</tr>
			@endforeach

		</tbody>
	</table>

</div>