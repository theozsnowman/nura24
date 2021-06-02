<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('Contact us') }} - {{ site()->short_title }}</title>
    <meta name="description" content="{{ __('Contact form') }} - {{ site()->short_title }}">

    @include("{$template_view}.global.head")

    @if($config->contact_recaptcha_enabled ?? null ==1)
    <script src="https://www.google.com/recaptcha/api.js?render={{ $config->google_recaptcha_site_key }}"></script>
    <script>
        grecaptcha.ready(function () {
                grecaptcha.execute('{{ $config->google_recaptcha_site_key }}', { action: 'contact' }).then(function (token) {
                    var recaptchaResponse = document.getElementById('recaptchaResponse');
                    recaptchaResponse.value = token;
                });
            });
    </script>
    @endif

    <style>
        .maparea {
            display: block;
            width: 100%;
            height: 400px;
            background: #58B;
            overflow: hidden;
        }

        iframe {
            width: 100%;
            border: 0;
        }
    </style>
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.global.navigation")

            @if($config->contact_map_enabled ?? null == 1)
            <section class="maparea">
                <div style="width: 100%"><iframe height="400"
                        src="https://maps.google.com/maps?height=400&amp;hl=en&amp;q={{ $config->contact_map_address }}&amp;ie=UTF8&amp;t=&amp;z=17&amp;iwloc=B&amp;output=embed"></iframe></div>
            </section>
            @endif

            <section>

                <div class="container">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="heading">
                                <h3 class="font-weight-bold">{{ __('Contact us') }}</h3>
                            </div>

                            {!! $config->contact_page_text ?? null !!}

                            @if(($config->contact_form_enabled ?? null) == 1)
                            <hr>

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
                                @if ($message=='sent') {{ __('Message sent! Thank you') }} @endif
                            </div>
                            @endif

                            @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                @if ($message=='recaptcha_error') {{ __('Antispam error') }} @endif
                            </div>
                            @endif

                            <h4>{{ __('Contact Form') }}</h4>
                            <form role="form" method="post">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="firstname">{{ __('Your name') }}</label>
                                            <input id="name" type="text" name="name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">{{ __('Email') }}</label>
                                            <input id="email" type="email" name="email" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="subject">{{ __('Subject') }}</label>
                                            <input id="subject" type="text" name="subject" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="message">{{ __('Message') }}</label>
                                            <textarea id="message" class="form-control" name="message" rows="5" required></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                                        <button type="submit" class="btn btn-template-outlined">{{ __('Send message') }}</button>
                                    </div>

                                </div>
                            </form>
                            @endif

                        </div>
                    </div>

                </div>

            </section>

        </div>

        @include("{$template_view}.global.footer")

    </div>

</body>

</html>