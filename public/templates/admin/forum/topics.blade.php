<div class="card-header">
	<h3><i class="far fa-comment-alt"></i> {{ __('Forum topics') }} ({{ $topics->total() ?? 0 }})</h3>
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
		@if ($message=='updated') {{ __('Updated') }} @endif
		@if ($message=='deleted') {{ __('Deleted') }} @endif
	</div>
	@endif


	<section>
        <form action="{{ route('admin.forum.topics') }}" method="get" class="form-inline">
            <input type="text" name="search_terms" placeholder="{{ __('Search author') }}" class="form-control mr-2 @if($search_terms) is-valid @endif" value="<?= $search_terms;?>" />
            <select name="search_status" class="form-control mr-2 @if($search_status) is-valid @endif">
                <option value="">- {{ __('Any status') }} -</option>
                <option @if ($search_status=='active' ) selected @endif value="active">{{ __('Active topics') }}</option>
                <option @if ($search_status=='closed' ) selected @endif value="closed">{{ __('Closed topics') }}</option>
            </select>

			<select name="search_type" class="form-control mr-2 @if($search_type) is-valid @endif">
                <option value="">- {{ __('All types') }} -</option>
				<option @if ($search_type=='question') selected="selected" @endif value="question">{{ __('Question style') }}</option>
				<option @if ($search_type=='discussion') selected="selected" @endif value="discussion">{{ __('Discussion style') }}</option>
            </select>
			
            <select name="search_sticky" class="form-control mr-2 @if($search_sticky) is-valid @endif">
                <option value="">- {{ __('All topics') }} -</option>
                <option @if ($search_sticky==1) selected="selected" @endif value="1">{{ __('Sticky topics') }}</option>
            </select>

            <button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
            <a class="btn btn-light" href="{{ route('admin.forum.topics') }}"><i class="fas fa-undo"></i></a>
        </form>
    </section>
	<div class="mb-3"></div>
	
	<div class="table-responsive-md">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Details') }}</th>
					<th width="100">{{ __('Posts') }}</th>
					<th width="300">{{ __('Author') }}</th>
					<th width="180">{{ __('Type') }}</th>
					<th width="100">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ($topics as $topic)
				<tr @if ($topic->status != 'active') class="table-warning" @endif>
					<td>
						@if ($topic->status=='closed') 
						<span class="float-right"><button class="btn btn-sm btn-warning"> {{ __('Closed') }}</button></span>
						@endif
						
						<h4><a target="_blank" href="{{ route('forum.topic', ['id' => $topic->id, 'slug' => $topic->slug]) }}">{{ $topic->title }}</a></h4>
						<div class="text-muted small">{{ __('Created at') }} {{ date_locale($topic->created_at, 'datetime') }}</div>
					</td>

					<td>
						<h4>{{ $topic->count_posts }}</h4>
					</td>

					<td>
						@if($topic->author_avatar) <img class="logged_user_avatar rounded-circle" style="max-height:20px" src="{{ thumb($topic->author_avatar) }}">@endif
						{{ $topic->author_name}}
					</td>

					<td>
						@if ($topic->type=='discussion') <button class="btn btn-light btn-sm btn-block"><i class="far fa-comment-alt"></i> {{ __('Discussion') }}</button> @endif
						@if ($topic->type=='question') <button class="btn btn-light btn-sm btn-block"><i class="far fa-question-circle"></i> {{ __('Question') }}</button> @endif
					</td>					

					<td>
						<div class="d-flex">

							<button data-toggle="modal" data-target="#update-topic-{{$topic->id}}" class="btn btn-dark btn-sm mr-3"><i class="fas fa-pen"></i></button>
							@include('admin.forum.modals.update-topic')

							<form method="POST" action="{{ route('admin.forum.topics.delete', ['id' => $topic->id]) }}">
								{{ csrf_field() }}
								<button type="submit" class="float-right btn btn-danger btn-sm delete-item-{{$topic->id}}"><i class="fas fa-trash-alt"></i></button>
							</form>
						</div>

						<script>
							$('.delete-item-{{$topic->id}}').click(function(e){
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

	{{ $topics->appends(['search_terms' => $search_terms, 'search_status' => $search_status, 'search_sticky' => $search_sticky, 'search_type' => $search_type])->links() }}
 
</div>
<!-- end card-body -->