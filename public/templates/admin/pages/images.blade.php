<div class="card-header">
	<h3><i class="far fa-file-image"></i> {{ __('Images') }} - {{ $page->title }}" ({{ $images-> total() }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	<nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.pages') }}">{{ __('Static pages') }}</a></li>
			<li class="breadcrumb-item"><a href="{{ route('admin.pages.show', ['id' => $page->id]) }}">{{ $page->title }}</a></li>
            <li class="breadcrumb-item active">{{ __('Images') }}</li>
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


	<span class="pull-right mb-3"><button data-toggle="modal" data-target="#create_image" class="btn btn-primary"><i class="fas fa-plus-square"></i> {{ __('Add image') }}</button></span>
	@include('admin.pages.modals.create_image')	


	<div class="table-responsive-md">

		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Image') }}</th>					
					<th width="100">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>

				@foreach ($images as $image)
				<tr>
					<td>
						@if ($image->file)
                        <span class="float-left mr-3"><a target="_blank" href="{{ image($image->file) }}"><img style="max-width:130px; height:auto;" src="{{ thumb($image->file) }}" /></a></span>
						@endif
						<h5>{{ __('Image ID') }}: {{ $image->id }}</h5>
						{{ $image->description ?? null }}
					</td>
				
					<td>
						<div class="d-flex">							
							<form method="POST" action="{{ route('admin.pages.images.delete', ['id' => $page->id, 'image_id' => $image->id]) }}">
								{{ csrf_field() }}
								{{ method_field('DELETE') }}
								<button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$image->id}}"><i class="fas fa-trash-alt"></i></button>
							</form>							
						</div>

						<script>
							$('.delete-item-{{$image->id}}').click(function(e){
									e.preventDefault() // Don't post the form, unless confirmed
									if (confirm("{{ __('Are you sure to delete this item?') }}")) {
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

	{{ $images->appends(['id' => $id])->links() }}

</div>
<!-- end card-body -->