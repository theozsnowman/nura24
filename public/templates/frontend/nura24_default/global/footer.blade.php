<!-- ======= Footer ======= -->
<footer id="footer">

	<div class="footer-top">
		<div class="container">
			<div class="row">

				<div class="col-lg-3 col-md-6 footer-contact">
					<div class="title">Navigation</div>
					<ul>
						<li><a href="{{ homepage() }}">Home</a></li>
						<li><a href="#">Blog</a></li>
						<li><a href="#">Shop</a></li>
						<li><a href="#">Forum</a></li>
					</ul>
				</div>

				<div class="col-lg-3 col-md-6 footer-contact">
					<div class="title">Support</div>
					<ul>
						<li><a href="{{ contact_url() }}">Contact Us</a></li>
						<li><a href="{{ docs_url() }}">Knowledge Base</a></li>
						<li><a href="{{ faq_url() }}">F.A.Q.</a></li>
						<li><a href="{{ account_url() }}">Support Tickets</a></li>
					</ul>
				</div>

				<div class="col-lg-3 col-md-6 footer-links">
					<div class="title">Useful links</div>
					<ul>
						@foreach (pages('footer') as $page)
						<li><i class="bx bx-chevron-right"></i> <a href="{{ page_url($page->id) }}">{{ $page->title }}</a></li>
						@endforeach
					</ul>
				</div>

				<div class="col-lg-3 col-md-6 footer-links">
					<div class="title">Social Networks</div>
					<p>Cras fermentum odio eu feugiat lide par naso tierra videa magna derita valies</p>
					<div class="social-links mt-3">
						<a href="#" class="twitter"><i class="bx bxl-twitter"></i></a>
						<a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
						<a href="#" class="instagram"><i class="bx bxl-instagram"></i></a>
						<a href="#" class="google-plus"><i class="bx bxl-skype"></i></a>
						<a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="footer-bottom">
		<div class="container text-center">
			<div class="copyright">
				Powered by <a href="https://nura24.com/">Nura24 Suite</a>
			</div>
		</div>
	</div>

</footer><!-- End Footer -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="{{ asset("$template_path/assets/vendor/aos/aos.js") }}"></script>
<script src="{{ asset("$template_path/assets/vendor/bootstrap/js/bootstrap.bundle.min.js") }}"></script>
<script src="{{ asset("$template_path/assets/vendor/isotope-layout/isotope.pkgd.min.js") }}"></script>
<script src="{{ asset("$template_path/assets/vendor/swiper/swiper-bundle.min.js") }}"></script>
<script src="{{ asset("$template_path/assets/vendor/waypoints/noframework.waypoints.js") }}"></script>

<!-- Template Main JS File -->
<script src="{{ asset("$template_path/assets/js/main.js") }}"></script>

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