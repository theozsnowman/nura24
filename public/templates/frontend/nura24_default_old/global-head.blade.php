<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="author" content="{{ $config->site_meta_author }}">

<!-- Favicon -->
@if($config->favicon)<link rel="shortcut icon" href="{{ asset('/uploads/'.$config->favicon) }}">@endif

<!-- Bootstrap CSS-->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

<!-- Font Awesome -->
<link rel="stylesheet" href="{{ asset("$template_path/assets/font-awesome/css/all.min.css") }}">

<!-- Google fonts -->
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Arimo:wght@300;400;600&display=swap" rel="stylesheet">

<!-- owl carousel-->
<link rel="stylesheet" href="{{ asset("$template_path/assets/vendor/owl.carousel/assets/owl.carousel.css") }}">
<link rel="stylesheet" href="{{ asset("$template_path/assets/vendor/owl.carousel/assets/owl.theme.default.css") }}">

<!-- Main style-->
<link rel="stylesheet" href="{{ asset("$template_path/assets/css/style.css") }}">

<!-- Cart style-->
<link rel="stylesheet" href="{{ asset("$template_path/assets/css/cart.css") }}">

<!-- Forum style-->
<link rel="stylesheet" href="{{ asset("$template_path/assets/css/forum.css") }}">

<!-- jquery -->
<script src="{{ asset("$template_path/assets/vendor/jquery/jquery.min.js") }}"></script>

{!! $config->template_global_head_code ?? null !!}