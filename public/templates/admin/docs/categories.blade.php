<div class="card-header">
	<h3><i class="far fa-folder-open"></i> {{ __('All categories') }} ({{ $count_categories ?? 0 }})</h3>
</div>
<!-- end card-header -->

<div class="card-body">

	<nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.docs') }}">{{ __('Knowledge Base') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Categories') }}</li>
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

	@if ($message = Session::get('error'))
	<div class="alert alert-danger">
		@if ($message=='duplicate') {{ __('Error. This category with this URL structure exist') }} @endif
	</div>
	@endif

	<span class="pull-right mb-3"><button data-toggle="modal" data-target="#create_categ" class="btn btn-primary"><i class="fas fa-plus-square"></i> {{ __('Create category') }}</button></span>
	@include('admin.docs.modals.create_categ')	

	<section>
		<form action="{{ route('admin.docs.categ') }}" method="get" class="form-inline">			
			@if(count(sys_langs())>1)
			<select name="search_lang_id" class="form-control @if($search_lang_id) is-valid @endif mr-2">
				<option selected="selected" value="">- {{ __('Any language') }} -</option>
				@foreach (sys_langs() as $lang)
				<option @if($search_lang_id==$lang->id) selected @endif value="{{ $lang->id }}"> {{ $lang->name }}</option>
				@endforeach
			</select>
			@endif

			<button class="btn btn-dark mr-2" type="submit"><i class="fas fa-check"></i> {{ __('Apply') }}</button>
			<a class="btn btn-light" href="{{ route('admin.docs.categ') }}"><i class="fas fa-undo"></i></a>
		</form>
	</section>
	<div class="mb-3"></div>
		
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
				
				@include('admin.docs.loops.categories-loop', $categ)

				@endforeach		

			</tbody>
		</table>
	</div>

</div>
<!-- end card-body -->