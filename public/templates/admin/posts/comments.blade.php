<div class="card-header">
	<h3><i class="far fa-comment-alt"></i> {{ $comments -> total() ?? 0 }} {{ __('comments') }} - @if(!$search_post_id) {{ __('all posts') }} @else {{ $post->title }} @endif</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	<section>
        <a href="{{ route('admin.posts') }}" class="btn btn-dark mb-2 mr-2"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('posts') }}</a>

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
					<th width="400">{{ __('Post details') }}</th>
					<th>{{ __('Comment') }}</th>
					<th width="350">{{ __('Details') }}</th>
					<th width="100">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>

				@foreach ($comments as $comment)
				<tr>
					<td>
						@if ($comment->post_image)
						<span style="float: left; margin-right:10px;"><img style="max-width:60px; height:auto;" src="{{ thumb($comment->post_image) }}" /></span>
						@endif
						<h5><a target="_blank" href="{{ post_url($comment->post_id) }}">{{ $comment->post_title }}</a></h5>
					</td>

					<td>
						<div class="text-muted mb-3">{{ date_locale($comment->created_at, 'datetime') }}</div>
						{!! nl2br(e($comment->comment)) !!}						
					</td>

					<td>
						@if($comment->user_id)
							@if($comment->author_avatar) 
							<img src="{{ thumb($comment->author_avatar) }}" class="img-fluid rounded-circle" style="max-height: 35px;">
							@endif 
							<span class="author"><a target="_blank" href="{{ profile_url($comment->user_id) }}">{{ $comment->author_name }}</a></span> 
						@else
							{{ $comment->name }} ({{ __('visitor') }})<br>
							{{ $comment->email }}
						@endif
						<div class="mt-2"></div>
						IP: {{ $comment->ip }}</b>
					</td>

					<td>
						<form method="POST" action="{{ route('admin.posts.comments.show', ['id' => $comment->id, 'search_post_id'=>$search_post_id]) }}">
							{{ csrf_field() }}
							{{ method_field('DELETE') }}
							<button type="submit" class="btn btn-danger btn-sm delete-item-{{$comment->id}}"><i class="fas fa-trash-alt"></i></button>
						</form>

						<script>
							$('.delete-item-{{$comment->id}}').click(function(e){
										e.preventDefault() // Don't post the form, unless confirmed
										if (confirm('Are you sure to delete this comment?')) {
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

    {{ $comments->appends(['search_post_id' => $search_post_id])->links() }}

</div>
<!-- end card-body -->