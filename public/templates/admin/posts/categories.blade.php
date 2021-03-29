<div class="card-header">
	<h3><i class="far fa-edit"></i> {{ __('All categories') }} ({{ $count_categories ?? 0 }})</h3>
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
		@if ($message=='duplicate') {{ __('Error. This category with this URL structure exist') }} @endif
		@if ($message=='length') {{ __('Error. Slug length must be minimum 3 characters') }} @endif
	</div>
	@endif

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
    	
	<span class="pull-right mb-3 mt-2"><button data-toggle="modal" data-target="#create-categ" class="btn btn-primary"><i class="fas fa-plus-square"></i> {{ __('Create category') }}</button></span>
	@include('admin.posts.modals.create-categ')

	@if(count(sys_langs())>1)
	<section>
		<form action="{{ route('admin.posts.categ') }}" method="get" class="form-inline">

			<select name="search_lang_id" class="form-control @if($search_lang_id) is-valid @endif mr-2">
				<option selected="selected" value="">- {{ __('Any language') }} -</option>
				@foreach (sys_langs() as $lang)
				<option @if($search_lang_id==$lang->id) selected @endif value="{{ $lang->id }}"> {{ $lang->name }}</option>
				@endforeach
			</select>

			<button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
			<a class="btn btn-light" href="{{ route('admin.posts.categ') }}"><i class="fas fa-undo"></i></a>
		</form>
	</section>
	@endif

	<div class="table-responsive-md">

		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>{{ __('Details') }}</th>
					<th width="150">{{ __('Statistics') }}</th>
					@if(count(sys_langs())>1)
					<th width="160">{{ __('Language') }}</th>
					@endif
					<th width="100">{{ __('Actions') }}</th>
				</tr>
			</thead>

			<tbody>

				@foreach ($categories as $categ)

				@include('admin.posts.loops.categories-loop', $categ)

				@endforeach

			</tbody>
		</table>
	</div>

</div>
<!-- end card-body -->