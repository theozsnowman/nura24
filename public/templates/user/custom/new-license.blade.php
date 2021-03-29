<style>
	.type_div {
		background-color: #f7f8f9;
		display: inline-block;
		width: 100%;
		height: auto;
		;
		min-height: 180px;
		border: 1px solid #ccc;
		border-radius: 5px;
		padding: 10px;
		color: #5f5f5f;
		font-size: 0.9em;
		font-weight: 400;
	}

	.type_div:hover {
		cursor: pointer;
	}

	#checkboxes input[type=radio] {
		display: none;
	}

	#checkboxes input[type=radio]:checked+.type_div {
		background-color: #4a5a71;
		color: #fff;
		border: 1px solid #4a5a71;
	}
</style>

<div class="account-header">
	<h3><i class="fas fa-key"></i> {{ __('Create new license for ') }} {{ $dom }}</h3>
</div>
<!-- end card-header -->

@if(logged_user()->count_unpaid_orders > 0)
<div class="alert alert-danger">
	<div class="font-weight-bold mb-2">{{ logged_user()->count_unpaid_orders }} {{ __('unpaid orders') }}.</div>
	<a href="{{ route('user.custom.orders') }}">{{ __('My orders') }}</a>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger">
	@if ($message=='select_option') {{ __('Error. Please click on a box to select an option') }} @endif
</div>
@endif

<h3>{{ __('Select license period') }}</h3>
<h5 class="mb-3">{{ __('Click on a box to select an option') }}:</h5>

<form method="post">
	@csrf

	<div id="checkboxes" class="mb-4">
		<div class="row">

			@foreach($license_plans as $plan)
			<div class="col-lg-3 col-md-6 col-sm-6 col-12">
				<input type="radio" name="plan_id" value="{{ $plan->id }}" id="{{ $plan->id }}" />
				<label class="type_div text-center" for="{{ $plan->id }}">
					<h3 class="mb-3"><i class="far fa-clock"></i> @if($plan->no_expire == 1) {{ __('Lifetime') }} @else @if($plan->months == 1) {{ __('One month') }} @else {{ $plan->months }} {{ __('Months') }} @endif
						@endif</h3>
					<h2 class="mb-3">{{ $plan->price }} USD</h2>

					<h4 class="mb-3 text-info">
						@if($plan->months == 1)						
						{{ __('monthly payment') }}
						@endif

						@if($plan->months == 6)
						<i class="fas fa-star text-warning"></i>
						{{ $plan->price_monthly }} USD / {{ __('month') }}
						@endif

						@if($plan->months == 12)
						<i class="fas fa-star text-warning"></i>
						<i class="fas fa-star text-warning"></i>
						{{ $plan->price_monthly }} USD / {{ __('month') }}					
						@endif

						@if(! $plan->months)
						<i class="fas fa-star text-warning"></i>
						<i class="fas fa-star text-warning"></i>
						<i class="fas fa-star text-warning"></i>
						{{ __('best value') }}
						@endif											
					</h4>

					<p>
						{{ __('Expire date') }}:<br>
						@php
						if($plan->no_expire == 1) $expire_date = null;
						else $expire_date = date('Y-m-d', strtotime('+'.$plan->months.' months'));
						@endphp

						@if(! $expire_date) <b>{{ __('License never expire') }}</b>
						@else <b>{{ date_locale($expire_date) }}</b>
						@endif
					</p>

					<h5 class="mb-2"><i class="fas fa-check"></i> {{ __('Select this license') }}</h5>

				</label>
			</div>
			@endforeach

		</div>
	</div>

	<div class="form-group">
		<button type="submit" class="btn btn-custom"><i class="fas fa-check"></i> {{ __('Create license key') }}</button>
	</div>

</form>