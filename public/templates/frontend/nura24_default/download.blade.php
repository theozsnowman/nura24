<!DOCTYPE html>
<html lang="{{ $locale }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $download->meta_title ?? $download->title }}</title>
    <meta name="description" content="{{ $download->meta_description ?? $download->title }}">

    @include("{$template_view}.global.head")
</head>

<body>

    <div id="all">

        <div id="content-wrap">

            @include("{$template_view}.global.navigation")

            <section>

                <div class="container">

                    <h1>{{ __('Download') }} {{ $download->title }}</h1>

                    @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        @if ($message == 'downloaded') {{ __('File downloaded') }} @endif
                    </div>
                    @endif

                    @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                        @if ($message == 'login_required') <b>{{ __('Error. You must be logged to download this file') }}.</b><br><a href="{{ route('login') }}">{{ __('Login') }}</a> {{ __('or') }} <a
                            href="{{ route('register') }}">{{ __('register new account') }}</a> @endif
                        @if ($message == 'no_file') {{ __('Error. No file') }}@endif
                    </div>
                    @endif

                    @if($download->summary)<div class="small text-muted mb-3">{{ $download->summary }}</div>@endif

                    @if(count($files) == 0)
                    <div class="alert alert-danger">{{ __('No files available at this moment') }}</div>
                    @else

                    <div class="table-responsive-md">
                        <table class="table table-bordered table-hover">

                            <thead>
                                <tr>
                                    <th>{{ __('File') }}</th>
                                    <th width="250">{{ __('Release date') }}</th>
                                    <th width="150">{{ __('Version') }}</th>
                                    <th width="200">{{ __('Download') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($files as $file)
                                <tr>
                                    <td>
                                        <h4>{{ $file->title }}</h4>
                                    </td>

                                    <td>
                                        @if($file->release_date)<h5>{{ date_locale($file->release_date) }}</h5>@endif
                                    </td>

                                    <td>
                                        @if($file->version)<h5>{{ $file->version }}</h5>@endif
                                    </td>
                                    <td>
                                        <a class="btn btn-success btn-block" href="{{ route('download.get', ['hash' => $file->hash]) }}"><i class="fas fa-download"></i> {{ __('Download') }}</a>
                                        <div class="mt-3 text-muted text-small">
                                            {{ $file->count_downloads ?? 0 }} {{ __('downloads') }}
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    {{ $files->links() }}
                    @endif

                    @if($download->content)<div class="mt-4 mb-3">{!! $download->content !!}</div>@endif

                </div>

            </section>

        </div>

        @include("{$template_view}.global.footer")

    </div>

</body>

</html>