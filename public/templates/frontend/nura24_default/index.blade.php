<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
	<title>{{ site()->meta_title }}</title>
	<meta content="" name="{{ site()->meta_description }}">

	@include("{$template_view}.global.head")
</head>

<body>

	<div id="all">

		<div id="content-wrap">

			@include("{$template_view}.global.navigation")

			<section>

				<div class="container">
				
					<div class="row">

						{{-- Display first post only --}}
						@foreach (posts() as $post)
						@if($loop->first)

						<div class="col-md-7 col-12">
							<div class="box-post-first">
								@if($post->image)
								<a title="{{ $post->title }}" href="{{ post_url($post->id) }}">
									<img src="{{ thumb($post->image) }}" alt="{{ $post->title }}" class="img-fluid"></a>
								@endif
							</div>
						</div>

						<div class="col-md-5 col-12">
							<div class="box-post-first">

								<div class="title">
									<a title="{{ $post->title }}" href="{{ post_url($post->id) }}">{{ $post->title }}</a>
								</div>

								<div class="categ">
									<a title="{{ $post->categ_title }}" href="{{ posts_url($post->categ_id) }}">{{ $post->categ_title }}</a>
								</div>								

								@if($post->summary)
								<div class="summary">
									{{ $post->summary }}
								</div>
								@endif

								<div class="info">
									@if($post->author_avatar) <img src="{{ thumb($post->author_avatar) }}" alt="{{ $post->author_name }}" class="img-fluid rounded-circle float-start me-2">@endif
									<div class="author"><a href="{{ profile_url($post->user_id) }}">{{ $post->author_name }}</a></div>
									{{ date_locale($post->created_at) }} <i class="bi bi-dot"></i> {{ $post->minutes_to_read }} {{ __('minutes read') }}
								</div>

							</div>
						</div>
						@endif
						@endforeach

					</div>

					<hr class="mb-4">

					<div class="float-end">
						<a class="btn btn-light btn-sm" href="{{ posts_url() }}" title="{{ __('View all posts') }}">{{ __('View all posts') }}</a>
					</div>

					<h4>{{ __('Latest posts') }}</h4>

					<div class="row mt-3">
						{{-- Exclude first post then display 9 posts --}}
						@foreach (posts() as $post)
						@if(! $loop->first && $loop->index < 10) <div class="col-lg-4 col-md-6 col-12">
							<div class="box-post mb-4">
								@if($post->image)
								<a title="{{ $post->title }}" href="{{ post_url($post->id) }}">
									<img src="{{ thumb($post->image) }}" alt="{{ $post->title }}" class="img-fluid"></a>
								@endif

								<div class="categ">
									<a title="{{ $post->categ_title }}" href="{{ posts_url($post->categ_id) }}">{{ $post->categ_title }}</a>
								</div>

								<div class="title">
									<a title="{{ $post->title }}" href="{{ post_url($post->id) }}">{{ $post->title }}</a>
								</div>

								@if($post->summary)
								<div class="summary">
									{{ $post->summary }}
								</div>
								@endif

								<div class="info">
									@if($post->author_avatar) <img src="{{ thumb($post->author_avatar) }}" alt="{{ $post->author_name }}" class="img-fluid rounded-circle float-start me-2">@endif
									<div class="author"><a href="{{ profile_url($post->user_id) }}">{{ $post->author_name }}</a></div>
									{{ date_locale($post->created_at) }} <i class="bi bi-dot"></i> {{ $post->minutes_to_read }} {{ __('minutes read') }}
								</div>
							</div>
					</div>
					@endif
					@endforeach
				</div>

		</div>

		</section>

	</div>

	@include("{$template_view}.global.footer")

	</div>

</body>

</html>