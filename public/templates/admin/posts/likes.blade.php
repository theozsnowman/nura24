<div class="card-header">
	<h3><i class="far fa-thumbs-up"></i> {{ __('Likes') }} ({{ $likes->total() ?? 0 }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	<section>
        <a href="{{ route('admin.posts') }}" class="btn btn-dark mb-2 mr-2"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('Posts') }}</a>

        @if(logged_user()->role == 'admin')
        <a href="{{ route('admin.posts.categ') }}" class="btn btn-dark mr-2 mb-2"><i class="fas fa-sitemap"></i> {{ __('Categories') }}</a>
        @endif

        <a href="{{ route('admin.posts.comments') }}" class="btn btn-dark mr-2 mb-2"><i class="fas fa-comment"></i> {{ __('Comments') }}</a>
        <a href="{{ route('admin.posts.likes') }}" class="btn btn-dark mr-2 mb-2"><i class="fas fa-thumbs-up"></i> {{ __('Likes') }}</a>

        @if(logged_user()->role == 'admin')
        <a href="{{ route('admin.posts.config') }}" class="btn btn-dark mb-2"><i class="fas fa-cog"></i></a>
        @endif         
    </section>

	<div class="mb-3"></div>
	
	@if ($message = Session::get('success'))
	<div class="alert alert-success">
		@if ($message=='deleted') {{ __('Deleted') }} @endif
	</div>
	@endif

	<div class="table-responsive-md">

		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Details') }}</th>
					<th width="250">{{ __('Rating') }}</th>
					<th width="100">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>

				@foreach ($likes as $like)
				<tr>
					<td>
						@if ($like->post_image)
						<span style="float: left; margin-right:10px;"><img style="max-width:110px; height:auto;" src="{{ thumb($like->post_image) }}" /></span>
						@endif
						<h5><a target="_blank" href="{{ post_url($like->post_id) }}">{{ $like->post_title }}</a></h5>
						<h5 class="mt-2">{{ $like->post_count_likes ?? 0 }} {{ __('likes') }}</h5>
					</td>

					<td>
						<b>{{ date_locale($like->created_at) }}</b><br>
						<b>IP: {{ $like->ip }}</b>
					</td>

					<td>
						<form method="POST" action="{{ route('admin.posts.likes.show', ['id' => $like->id, 'search_post_id' => $search_post_id]) }}">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}
							<button type="submit" class="btn btn-danger btn-sm delete-item-{{$like->id}}"><i class="fas fa-trash-alt"></i></button>
						</form>

						<script>
							$('.delete-item-{{$like->id}}').click(function(e){
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

	{{ $likes->appends(['search_post_id' => $search_post_id])->links() }}

</div>
<!-- end card-body -->
