@if (count(slides()) > 0)
<section @if ($config->slider_main_background) style='background: url("/uploads/{{ str_replace('\\','/', $config->slider_main_background) }}") center center repeat; background-size: cover;'@endif
    class="relative-positioned">
    <!-- Carousel Start-->
    <div class="home-carousel">
        <div class="dark-mask mask-primary"></div>
        <div class="container">
            <div class="homepage owl-carousel">

                @foreach (slides() as $slide)
                <div class="item">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-4 col-sm-12 col-xs-12 d-md-block d-none">
                            @if ($slide->image)
                            @if ($slide->url)
                            <a @if ($slide->target=='blank') target="_blank" @endif href="{{ $slide->url }}" title="{{ $slide->title }}"><img src="{{ asset("uploads/$slide->image") }}" alt="{{ $slide->title }}"
                                    class="img-fluid custom_shadow"></a>
                            @else
                            <img src="{{ asset("uploads/$slide->image") }}" alt="{{ $slide->title }}" class="img-fluid custom_shadow">
                            @endif
                            @endif
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-8 col-md-12 col-xs-12">
                            <h2 class="text-white">{{ $slide->title }}</h2>
                            <p>{!! $slide->content !!}</p>
                            @if ($slide->url)
                            <a @if ($slide->target=='blank') target="_blank" @endif class="btn btn-template-outlined" href="{{ $slide->url }}" title="{{ $slide->title }}">Read More</a>
                            @endif
                        </div>

                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
    <!-- Carousel End-->
</section>

@else
<section style="background-color: {{ $config->slider_background_color ?? '#fff'}}">
    <div class="container">
        @if ($config->slider_main_background ?? null)<img src="/uploads/{{ str_replace('\\','/', $config->slider_main_background) }}" class="img-fluid mx-auto d-block" style='max-height: 50vh; object-fit: cover; overflow: hidden;'>@endif
    </div>
</section>

@endif