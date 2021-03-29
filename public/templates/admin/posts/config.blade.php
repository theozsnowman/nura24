<div class="card-header">
	<h3><i class="fas fa-cog"></i> {{ __('Posts settings') }}</h3>
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
		@if ($message=='updated') {{ __('Updated') }} @endif
	</div>
	@endif

	<form method="post">
		@csrf

		<div class="form-row">

			<div class="col-12">
				<h3>{{ __('Blog settings') }}</h3>
			</div>

			<div class="form-group col-md-3 col-12">
				<label>{{ __('Posts per page') }}</label>
				<input type="integer" name="posts_per_page" class="form-control" value="{{ $config->posts_per_page ?? 12 }}">					
			</div>

			<div class="form-group col-md-3 col-12">
				<label>{{ __('Posts comments per page') }}</label>
				<input type="integer" name="posts_comments_per_page" class="form-control" value="{{ $config->posts_comments_per_page ?? 20 }}">					
			</div>

			<div class="col-12">
				<hr>
				<h3>{{ __('Comments settings') }}</h3>
			</div>

			<div class="form-group col-md-3 col-12">
				<label>{{ __('Enable / disable posts comments') }}</label>
				<select name="posts_comments_disabled" class="form-control" required>
					<option @if(($config->posts_comments_disabled ?? null) == 0) selected @endif value="0">{{ __('Comments enabled') }}</option>
					<option @if(($config->posts_comments_disabled ?? null) == 1) selected @endif value="1">{{ __('Comments disabled') }}</option>
				</select>
			</div>

			<div class="form-group col-md-3 col-12">
				<label>{{ __('Comments permission (if comments are enabled)') }}</label>
				<select name="posts_comments_require_login" class="form-control" required>
					<option @if(($config->posts_comments_require_login ?? null) == 0) selected @endif value="0">{{ __('Anyone can post comments') }}</option>
					<option @if(($config->posts_comments_require_login ?? null) == 1) selected @endif value="1">{{ __('Only registered users can post comments') }}</option>
				</select>
			</div>

			<div class="form-group col-md-3 col-12">
				<label>{{ __('Add antispam to comments form') }}</label>
				<select name="posts_comments_antispam_enabled" class="form-control" required>
					<option @if(($config->posts_comments_antispam_enabled ?? null) == 0) selected @endif value="0">{{ __('Disabled') }}</option>
					<option @if(($config->posts_comments_antispam_enabled ?? null) == 1) selected @endif value="1">{{ __('Enabled for visitors') }}</option>
				</select>
			</div>


			<div class="form-group col-md-3 col-12">
				<label>{{ __('Comments order') }}</label>
				<select name="posts_comments_order" class="form-control" required>
					<option @if(($config->posts_comments_order ?? null) == 'old') selected @endif value="old">{{ __('Latest comments are displayed last') }}</option>
					<option @if(($config->posts_comments_order ?? null) == 'new') selected @endif value="new">{{ __('Latest comments are displayed first') }}</option>
				</select>
			</div>

			<div class="col-12">
				<hr>
				<h3>{{ __('Likes settings') }}</h3>
			</div>

			<div class="form-group col-md-3 col-12">
				<label>{{ __('Enable / disable posts likes') }}</label>
				<select name="posts_likes_disabled" class="form-control" required>
					<option @if(($config->posts_likes_disabled ?? null) == 0) selected @endif value="0">{{ __('Likes enabled') }}</option>
					<option @if(($config->posts_likes_disabled ?? null) == 1) selected @endif value="1">{{ __('Likes disabled') }}</option>
				</select>
			</div>

			<div class="form-group col-md-3 col-12">
				<label>{{ __('Likes permission (if likes are enabled)') }}</label>
				<select name="posts_likes_require_login" class="form-control" required>
					<option @if(($config->posts_likes_require_login ?? null) == 0) selected @endif value="0">{{ __('Anyone can like posts') }}</option>
					<option @if(($config->posts_likes_require_login ?? null) == 1) selected @endif value="1">{{ __('Only logged users can like posts') }}</option>				
				</select>
			</div>
		
		</div>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
		</div>

	</form>

</div>
<!-- end card-body -->