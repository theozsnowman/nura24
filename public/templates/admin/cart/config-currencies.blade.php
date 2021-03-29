<div class="card-header">
	@include('admin.cart.layouts.menu-config')
</div>
<!-- end card-header -->

<div class="card-body">

	@if ($message = Session::get('success'))
	<div class="alert alert-success">
		@if ($message=='created') {{ __('Creates') }} @endif
		@if ($message=='updated') {{ __('Updated') }} @endif
		@if ($message=='deleted') {{ __('Deleted') }} @endif
	</div>
	@endif

	@if ($message = Session::get('error'))
	<div class="alert alert-danger">
		@if ($message=='duplicate') {{ __('Error. This currency exist') }} @endif
		@if ($message=='error_delete') {{ __('Error. This currency can not be deleted') }} @endif
	</div>
	@endif

	<form action="{{ route('admin.cart.config.currencies', ['id' => $default->id]) }}" method="post">
		@csrf
		@method('PUT')

		<h3>{{ __('Default currency') }}</h3>
		<div class='alert alert-info mb-3'>{{ __('Choose the currency the store will be priced in') }}</div>

		<h4>{{ __('Preview') }}: {{ price('9999.99', 1) }}</h4>

		<div class="row">
			<div class="col-lg-3 col-12">
				<div class="form-group">
					<label>{{ __('Currency code') }}</label>
					<input class="form-control" name="code" type="text" required value="{{ $default->code }}" />
					<span class="form-text text-muted">{{ __('Example') }}: USD</span>
				</div>
			</div>

			<div class="col-lg-3 col-12">
				<div class="form-group">
					<label>{{ __('Currency symbol') }}</label>
					<input class="form-control" name="symbol" type="text" required value="{{ $default->symbol }}" />
					<span class="form-text text-muted">{{ __('Example') }}: $</span>
				</div>
			</div>

			<div class="col-lg-3 col-12">
				<div class="form-group">
					<label>{{ __('Currency label') }}</label>
					<input class="form-control" name="label" type="text" required value="{{ $default->label }}" />
					<span class="form-text text-muted">{{ __('Example') }}: US Dollar</span>
				</div>
			</div>
		
			<div class="col-lg-3 col-12">
				<div class="form-group">
					<label>{{ __('Currency display style') }}</label>
					<select name="style" class="form-control">
						<option @if ($default->style=="value_code") selected @endif value="value_code">{{ __('VALUE') }} {{ __('CODE') }} (18 USD)</option>
						<option @if ($default->style=="code_value") selected @endif value="code_value">{{ __('CODE') }} {{ __('VALUE') }} (USD 18)</option>
						<option @if ($default->style=="value_symbol") selected @endif value="value_symbol">{{ __('VALUE') }} {{ __('SYMBOL') }} (18 $)</option>
						<option @if ($default->style=="symbol_value") selected @endif value="symbol_value">{{ __('SYMBOL') }} {{ __('VALUE') }} ($ 18)</option>
					</select>
				</div>
			</div>

			<div class="col-lg-3 col-12">
				<div class="form-group">
					<label>{{ __('Thousand separator') }}</label>
					<select name="t_separator" class="form-control">
						<option @if (!$default->t_separator) selected @endif value="">- {{ __('no separator') }} -</option>
						<option @if ($default->t_separator==",") selected @endif value=",">,</option>
						<option @if ($default->t_separator==".") selected @endif value=".">.</option>
					</select>
					<span class="form-text text-muted">{{ __('Choose the character to use for the thousand separator') }}</span>
				</div>
			</div>

			<div class="col-lg-3 col-12">
				<div class="form-group">
					<label>{{ __('Decimal separator') }}</label>
					<select name="d_separator" class="form-control">
						<option @if ($default->d_separator==".") selected @endif value=".">. ({{ __('dot') }})</option>
						<option @if ($default->d_separator==",") selected @endif value=",">, ({{ __('comma') }})</option>
					</select>
					<span class="form-text text-muted">{{ __('Choose the character to use for the decimal separator') }}</span>
				</div>
			</div>

			<div class="col-lg-3 col-12">
				<div class="form-group">
					<label>{{ __('Condensed') }}</label>
					<select name="condensed" class="form-control">
						<option @if ($default->condensed=="1") selected @endif value="1">{{ __('Yes') }}</option>
						<option @if ($default->condensed=="0") selected @endif value="0">{{ __('No') }}</option>
					</select>
					<span class="form-text text-muted">{{ __('If no, a space will be added between currency and value') }}</span>
				</div>
			</div>
		</div>

		<input type="hidden" name="is_default" value="1">
		<input type="hidden" name="active" value="1">
		<input type="hidden" name="hidden" value="0">
		<input type="hidden" name="conversion_rate" value="1">

		<button type="submit" class="btn btn-primary">{{ __('Update default currency') }}</button>

	</form>


</div>
<!-- end card-body -->