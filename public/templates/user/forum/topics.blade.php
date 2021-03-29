<div class="account-header">
	<a class="btn btn-dark btn-sm float-right" href="{{ route('forum.topic.create') }}">{{ __('Create new topic') }}</a>

	<h3><i class="fas fa-comments"></i> {{ __('My forum topics') }} ({{ $topics->total() ?? 0 }})</h3>
</div>
<!-- end card-header -->

@if(count($topics)==0)
{{ __('No topic') }}
@else
<div class="table-responsive-md">

	<table class="table table-bordered table-hover">
		<tbody>

			@foreach ($topics as $topic)
			<tr>
				<td>
					@if($topic->status != 'active')<button class="btn btn-dark float-right btn-sm">{{ $topic->status }}</button>@endif

					<h4><a href="{{ route('forum.topic', ['id' => $topic->id, 'slug' => $topic->slug]) }}">{{ $topic->title }}</a></h4>
					<div class="text-muted text-small">{{ __('Created') }}: {{ date_locale($topic->created_at, 'datetime') }}</div>

				</td>

				<td width="350">
					<div class="text-muted text-small">
						{{ $topic->count_posts ?? 0 }} {{ __('posts') }}
						@if($topic->latest_post_created_at)<br>{{ __('Latest post') }}: {{ date_locale($topic->latest_post_created_at, 'datetime') }}@endif
					</div>
				</td>

			</tr>
			@endforeach

		</tbody>
	</table>
</div>
@endif

{{ $topics->links() }}