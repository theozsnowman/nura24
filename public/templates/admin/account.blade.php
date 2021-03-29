<!doctype html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ Auth::user()->name }} - Admin area</title>

    <!-- Switchery css -->
    <link href="{{ asset('assets/plugins/switchery/switchery.min.css') }}" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.css') }}">

    <!-- Styles -->
    <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">

    <!-- Favicon -->
    @if($config->favicon)
    <link rel="shortcut icon" href="{{ image($config->favicon) }}">@endif

    <!-- Modernizr -->
    <script src="{{ asset('assets/js/modernizr.min.js') }}"></script>

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>

    <!-- Moment -->
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <!-- Text editor-->
    <script src="{{ asset('assets/plugins/trumbowyg/trumbowyg.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/trumbowyg/plugins/base64/trumbowyg.base64.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/trumbowyg/plugins/colors/trumbowyg.colors.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/prism/prism.js') }}"></script>
    <script src="{{ asset('assets/plugins/trumbowyg/plugins/highlight/trumbowyg.highlight.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/trumbowyg/plugins/noembed/trumbowyg.noembed.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/trumbowyg/plugins/table/trumbowyg.table.min.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('assets/plugins/prism/prism.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/trumbowyg/plugins/highlight/ui/trumbowyg.highlight.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/trumbowyg/plugins/table/ui/trumbowyg.table.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/trumbowyg/ui/trumbowyg.min.css') }}">
   
    <!-- Tags -->
    <script src="{{ asset('assets/plugins/tags-input-autocomplete/dist/jquery.tagsinput.min.js') }}"></script>
    <link href="{{ asset('assets/plugins/tags-input-autocomplete/dist/jquery.tagsinput.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <!-- Star ratings -->
    <link href="{{ asset("assets/plugins/star-rating/css/star-rating.css") }}" rel="stylesheet">
    <link href="{{ asset("assets/plugins/star-rating/themes/krajee-fas/theme.css") }}" rel="stylesheet">
    <script src="{{ asset("assets/plugins/star-rating/js/star-rating.js") }}" type="text/javascript"></script>
    <script src="{{ asset("assets/plugins/star-rating/themes/krajee-fas/theme.js") }}" type="text/javascript"></script>

    <!-- DateTime picker -->
    <script src="{{ asset("assets/plugins/datepicker/gijgo.min.js") }}" type="text/javascript"></script>
    <link href="{{ asset("assets/plugins/datepicker/gijgo.min.css") }}" rel="stylesheet" type="text/css" />

    <!-- Toggle -->
    <script src="{{ asset("assets/plugins/bootstrap4-toggle/bootstrap4-toggle.min.js") }}" type="text/javascript"></script>
    <link href="{{ asset("assets/plugins/bootstrap4-toggle/bootstrap4-toggle.min.css") }}" rel="stylesheet" type="text/css" />

    <style>
    .trumbowyg-modal {
        position: absolute;
       top: 100% !important;
       z-index: +1;
    }
    </style>

</head>

<body class="adminbody">

    <div id="main">

        @include('admin.navigation')

        @if(logged_user()->role == 'admin' || logged_user()->role == 'manager' || logged_user()->role == 'internal')
        @include('admin.sidebar')
        @endif

        <div class="content-page">

            <div class="content">

                <div class="row">

                    <div class="col-12">

                        <div class="card mb-3">

                            @include("admin.{$view_file}")

                        </div><!-- end card -->

                    </div><!-- end col -->

                </div><!-- end row -->

            </div><!-- END content -->

        </div><!-- END content-page -->

        @include('admin.footer')

    </div>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

    <script src="{{ asset('assets/js/detect.js') }}"></script>
    <script src="{{ asset('assets/js/fastclick.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.blockUI.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.nicescroll.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.scrollTo.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/switchery/switchery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/admin.js') }}"></script>

    <script>
    $(document).ready(function() {
        'use strict';        

	    $('.editor').trumbowyg({
            removeformatPasted: true,
            semantic: false,
            resetCss: false,
            autogrow: false,
            btns: [
                ['viewHTML','formatting', 'highlight'], 
                ['strong', 'em', 'link', 'insertImage', 'noembed', 'table'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList', 'horizontalRule', 'removeformat'],
                ['foreColor', 'backColor'], ['fullscreen']
            ]
        });	
        

        $('.select2').select2();

        $('.select_account').select2({  
            minimumInputLength: 2,             
            ajax: {
                url: "{{ route('admin.ajax.accounts')}}",
                dataType: 'json',
                delay: 20,
                cache: true
            }
        });

        $('.select_user').select2({  
            minimumInputLength: 2,             
            ajax: {
                url: "{{ route('admin.ajax.users')}}",
                dataType: 'json',
                delay: 20,
                cache: true
            }
        });

        $('.select_internal').select2({  
            minimumInputLength: 2,            
            ajax: {
                url: "{{ route('admin.ajax.internals')}}",
                dataType: 'json',
                delay: 20,
                cache: true
            }
        });

        bsCustomFileInput.init();

        $('.tagsinput').tagsInput({
            'width': 'auto',
            'defaultText': "{{ __('Add a tag') }}",
            'autocomplete_url': "{{ route('admin.ajax.tags') }}"
        });
        
        

        $(".ratings").rating({
            size: 'sm',
            showClear : 0,
            showCaption: 0,
            filledStar: '<i class="fas fa-star"></i>',
            emptyStar: '<i class="far fa-star"></i>'
        });

    });
    </script>   

</body>

</html>