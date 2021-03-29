<div class="card-header">  
    <h3>{{ $product->title }}</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	<nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.cart.products') }}">{{ __('Products catalog') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Product files') }}</li>
        </ol>                                
    </nav>

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

	<div class="mb-4">
        @include('admin.cart.layouts.menu-product')
	</div>
	
	<span class="pull-right mb-3"><button data-toggle="modal" data-target="#create_product_file" class="btn btn-primary"><i class="fas fa-plus-square"></i> {{ __('Create file') }}</button></span>
	@include('admin.cart.modals.create_product_file')


	<div class="table-responsive-md">

		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Details') }}</th>
					<th width="150">{{ __('Release date') }}</th>
					<th width="150">{{ __('File version') }}</th>
					<th width="150">{{ __('Downloads') }}</th>
					<th width="100">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>

				@foreach ($files as $file)
				<tr @if ($file->active==0) class="table-warning" @endif>
					<td>
						@if ($file->active==0)
						<span class="pull-right"><button type="button" class="btn btn-warning btn-sm disabled">{{ __('Inactive') }}</button></span>
						@endif

						@if ($file->featured==1)
						<span class="pull-right"><button type="button" class="btn btn-success btn-sm disabled">{{ __('Featured') }}</button></span>
						@endif

						<h4>{{ $file->title }}</h4>
						@if ($file->description)<div class="text-small text-muted">{!! nl2br( $file->description) !!}</div>@endif
						{{ __('File') }}: <a target="_blank" href="{{ asset('uploads/'.$file->file) }}">{{ $file->file }}</a>
					</td>

					<td>
						<h4>@if($file->release_date){{ date_locale($file->release_date) }}@endif</h4>
					</td>

					<td>
						<h4>{{ $file->version ?? null }}</h4>
					</td>

					<td>
						<h4>{{ $file->count_downloads ?? 0 }} {{ __('downloads') }}</h4>
					</td>


					<td>
						<div class="d-flex">
							<button data-toggle="modal" data-target="#update_product_file_{{ $file->id }}" class="btn btn-primary btn-sm mr-2"><i class="fas fa-pen" aria-hidden="true"></i></button>
							@include('admin.cart.modals.update_product_file')

							<form method="POST" action="{{ route('admin.cart.product.files', ['id' => $product->id, 'file_id' => $file->id]) }}">
								{{ csrf_field() }}
								{{ method_field('DELETE') }}
								<button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$file->id}}"><i class="fas fa-trash-alt"></i></button>
							</form>
						</div>

						<script>
							$('.delete-item-{{$file->id}}').click(function(e){
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

	{{ $files->appends(['id' => $product->id])->links() }}

</div>
<!-- end card-body -->