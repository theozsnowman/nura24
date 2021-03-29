<!-- FOOTER -->
<footer>

	<div class="container">
		<div class="row">

			<div class="col-lg-4 text-center-md">
				<div class="social-nav-buttons float-left mr-4 d-lg-block d-none">
					@if(isset($config->social_facebook_page))<a target="_blank" href="{{ $config->social_facebook_page }}"><img alt="Facebook" src="{{ asset("$template_path/assets/img/social/facebook.png") }}"></a>@endif
					@if(isset($config->social_twitter_page))<a target="_blank" href="{{ $config->social_twitter_page }}"><img alt="Twitter" src="{{ asset("$template_path/assets/img/social/twitter.png") }}"></a>@endif
					@if(isset($config->social_youtube_page))<a target="_blank" href="{{ $config->social_youtube_page }}"><img alt="Youtube" src="{{ asset("$template_path/assets/img/social/youtube.png") }}"></a>@endif
					@if(isset($config->social_linkedin_page))<a target="_blank" href="{{ $config->social_linkedin_page }}"><img alt="LinkedIn" src="{{ asset("$template_path/assets/img/social/linkedin.png") }}"></a>@endif
				</div>

				<div class="footer_links">
					<p>&copy; {{ lang_meta()->site_short_title }}. All Rights Reserved</p>
				</div>
			</div>
			<div class="col-lg-8 text-right text-center-md">

				@foreach (badge_pages('footer') as $page)
				<a href="{{ page_url($page->id) }}">{{ $page->title }}</a>
				@endforeach

			</div>
			<div class="col-12">
				{!! block(1)->content !!}
			</div>
		</div>
	</div>

</footer>

<!-- Javascript files-->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>

<script src="{{ asset("$template_path/assets/vendor/jquery.cookie/jquery.cookie.js") }}"></script>
<script src="{{ asset("$template_path/assets/vendor/waypoints/lib/jquery.waypoints.min.js") }}"></script>
<script src="{{ asset("$template_path/assets/vendor/owl.carousel/owl.carousel.min.js") }}"></script>
<script src="{{ asset("$template_path/assets/vendor/owl.carousel2.thumbs/owl.carousel2.thumbs.min.js") }}"></script>
<script src="{{ asset("$template_path/assets/js/jquery.parallax-1.1.3.js") }}"></script>
<script src="{{ asset("$template_path/assets/js/front.js") }}"></script>

@if (isset($config->google_analytics_ua))
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $config->google_analytics_ua }}"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', '{{ $config->google_analytics_ua }}');
</script>
@endif

{!! $config->template_global_footer_code ?? null !!}