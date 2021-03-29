<div class="card-header">
	<h3><i class="fas fa-globe"></i> {{ __('Languages and Locale') }}</h3>
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
		@if ($message=='created') {{ __('Created') }} @endif
		@if ($message=='updated') {{ __('Updated') }} @endif
		@if ($message=='deleted') {{ __('Deleted') }} @endif
	</div>
	@endif

	@if ($message = Session::get('error'))
	<div class="alert alert-danger">
		@if ($message=='duplicate') {{ __('Error. This language exist') }} @endif
		@if ($message=='exists_content') {{ __('Error. This language can not be deleted because there is content in this language. You can make this language inactive') }} @endif
		@if ($message=='default') {{ __('Error. Default language can not be deleted') }} @endif
	</div>
	@endif	
	
	@if(check_access('translates'))
	<span class="pull-right mb-3 ml-2"><a class="btn btn-dark" href="{{ route('admin.translates') }}"><i class="fas fa-flag" aria-hidden="true"></i> {{ __('Manage translates') }}</a></span>
	@endif

	<span class="pull-right mb-3"><button class="btn btn-primary" data-toggle="modal" data-target="#create-lang"><i class="fas fa-plus" aria-hidden="true"></i> {{ __('Add language') }}</button></span>
	@include('admin.core.modals.create-lang')

	<div class="table-responsive">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Details') }}</th>
					<th width="80">{{ __('Code') }}</th>
					<th width="300">{{ __('Locale') }}</th>
					<th width="120">{{ __('Status') }}</th>
					<th width="160">{{ __('Actions') }}</th>
				</tr>
			</thead>
			<tbody>

				@foreach ($langs as $lang)
				<tr @if($lang->status != 'active') class="bg-light" @endif>
					<td>
						@if ($lang->is_default==1) <span class="pull-right">&nbsp;<button type="button" class="btn btn-success btn-sm disabled">{{ __('Default Language') }}</button> </span> @endif
						<h4>{{ $lang->name }}</h4>

						<div class="small texx-muted">
							<b>{{ __('Site short title') }}</b>: {{ $lang->site_short_title }}
							<br>
							<b>{{ __('Homepage meta title') }}</b>: {{ $lang->homepage_meta_title }}
							<br>
							<b>{{ __('Homepage meta description') }}</b>: {{ $lang->homepage_meta_description }}
						</div>
					</td>

					<td>
						<h5>{{ $lang->code }}</h5>
					</td>

					<td>
						<div class="small texx-muted">
							<b>{{ __('Code') }}: {{ $lang->locale }}</b>
							<div class="mb-2"></div>
							{{ __('Date format') }}:
							@php
							setlocale(LC_TIME, $lang->locale ?? 'ro_RO');
							@endphp
							{{ strftime ($lang->date_format ?? '%e %b %Y', strtotime('2019-12-30')) }}

							<div class="mb-2"></div>
							{{ __('Timezone') }}: {{ $lang->timezone ?? 'Europe/London'}}
							<div class="mb-2"></div>
							{{ __('Currency style') }}:
							@if ($lang->currency_display_style=='value_code') {{ __('VALUE CODE') }} (18 USD) @endif
							@if ($lang->currency_display_style=='code_value') {{ __('CODE VALUE') }} (USD 18) @endif
							@if ($lang->currency_display_style=='value_symbol') {{ __('VALUE SYMBOL') }} (18 $) @endif
							@if ($lang->currency_display_style=='symbol_value') {{ __('SYMBOL VALUE') }} ($ 18) @endif
							@if ($lang->currency_display_style=='value_name') {{ __('VALUE NAME') }} (18 US Dollar) @endif
							@if ($lang->currency_display_style=='name_value') {{ __('NAME VALUE') }} (US Dollar 18) @endif
							@if ($lang->currency_display_style=='condensed') {{ __('CONDENSED') }} (USD18) @endif
						</div>
					</td>

					<td>
						@if ($lang->status=='active')<button type="button" class="btn btn-success btn-sm disabled btn-block">{{ __('Active') }}</button>@endif
						@if ($lang->status=='inactive')<button type="button" class="btn btn-warning btn-sm disabled btn-block">{{ __('Inactive') }}</button>@endif						
						@if ($lang->status=='disabled')<button type="button" class="btn btn-danger btn-sm disabled btn-block">{{ __('Disabled') }}</button>@endif						
					</td>

					<td>
						<a class="btn btn-dark btn-block" href="{{ route('admin.translate_lang', ['id' => $lang->id]) }}"><i class="fas fa-flag"></i> {{ __('Translates') }}</a>

						<button data-toggle="modal" data-target="#update-lang-{{ $lang->id }}" class="btn btn-primary btn-sm btn-block mt-2"><i class="fas fa-edit"></i> {{ __('Update') }}</button>
						@include('admin.core.modals.update-lang')

						@if ($lang->is_default!=1)
						<form method="POST" action="{{ route('admin.config.langs.show', ['id' => $lang->id]) }}">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}
							<button type="submit" class="float-right btn btn-danger btn-sm btn-block mt-2 delete-item-{{$lang->id}}"><i class="fas fa-trash-alt"></i> {{ __('Delete') }}</button>
						</form>
						<script>
							$('.delete-item-{{$lang->id}}').click(function(e){
											e.preventDefault() // Don't post the form, unless confirmed
											if (confirm("{{ __('Are you sure to delete this item?') }}'")) {
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

</div>
<!-- end card-body -->