<div class="card-header">
	<h3><i class="far fa-folder-open"></i> {{ __('All categories') }} ({{ $count_categories ?? 0 }})</h3>
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
		@if ($message=='duplicate') {{ __('Error. There is another category with this URL structure') }} @endif
	</div>
	@endif

	<span class="pull-right mb-3"><button data-toggle="modal" data-target="#create-categ" class="btn btn-primary"><i class="fas fa-plus-square"></i> {{ __('Create category') }}</button></span>
	@include('admin.cart.modals.create-categ')	

	<div class="table-responsive-md">

		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Details') }}</th>
					<th width="200">{{ __('Products type') }}</th>		
					<th width="150">{{ __('Statistics') }}</th>		
					<th width="100">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>

				@foreach ($categories as $categ)								
				
				@include('admin.cart.loops.categories-loop', $categ)

				@endforeach		

			</tbody>
		</table>
	</div>

</div>
<!-- end card-body -->