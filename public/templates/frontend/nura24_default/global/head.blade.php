<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="author" content="{{ $config->site_meta_author }}">

<!-- Favicon -->
@if($config->favicon)<link rel="shortcut icon" href="{{ asset('/uploads/'.$config->favicon) }}">@endif

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

<!-- Vendor CSS Files -->
<link href="{{ asset("$template_path/assets/vendor/aos/aos.css") }}" rel="stylesheet">
<link href="{{ asset("$template_path/assets/vendor/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet">
<link href="{{ asset("$template_path/assets/vendor/bootstrap-icons/bootstrap-icons.css") }}" rel="stylesheet">
<link href="{{ asset("$template_path/assets/vendor/boxicons/css/boxicons.min.css") }}" rel="stylesheet">
<link href="{{ asset("$template_path/assets/vendor/swiper/swiper-bundle.min.css") }}" rel="stylesheet">

<!-- Template Main CSS File -->
<link rel="stylesheet" href="{{ asset("$template_path/assets/css/style.css") }}">

<!-- Forum style-->
<link rel="stylesheet" href="{{ asset("$template_path/assets/css/forum.css") }}">

{!! $config->template_global_head_code ?? null !!}