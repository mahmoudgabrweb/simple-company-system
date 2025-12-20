@extends("front.main")

@section("content")
    <div class="page-content">
        <!-- INNER PAGE BANNER -->
        <div class="sx-bnr-inr overlay-wraper bg-parallax bg-top-center" data-stellar-background-ratio="0.5"
             style="background-image:url({{ asset("assets/images/banner/1.png") }});">
            <div class="overlay-main bg-black opacity-07"></div>
            <div class="container">
                <div class="sx-bnr-inr-entry">
                    <div class="banner-title-outer">
                        <div class="banner-title-name">
                            <h2 class="m-tb0">About Us</h2>
                        </div>
                    </div>
                    <!-- BREADCRUMB ROW -->

                    <div>
                        <ul class="sx-breadcrumb breadcrumb-style-2">
                            <li><a href="javascript:void(0);">Home</a></li>
                            <li>About Us</li>
                        </ul>
                    </div>

                    <!-- BREADCRUMB ROW END -->
                </div>
            </div>
        </div>
        <!-- INNER PAGE BANNER END -->
        <!-- ABOUT COMPANY START -->
        <div id="about-us" class="section-full mobile-page-padding p-t80 p-b50 bg-gray">
            <div class="container">
                <div class="section-content">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12">

                            <div class="about-home-3 m-b30 bg-white">
                                <h3 class="m-t0 m-b20 sx-tilte">{{ loadSettings()['about_title'] }}</h3>
                                <p>{{ loadSettings()['about_body'] }}</p>

                                <ul class="list-angle-right anchor-line">
                                    @for($i = 1; $i <= 4; $i++)
                                        @if(isset(loadSettings()["about_element_$i"]) && loadSettings()["about_element_$i"] != "")
                                            <li><a href="javascript:void(0);">{{ loadSettings()["about_element_$i"] }}</a>
                                            </li>
                                        @endif
                                    @endfor
                                </ul>

                                <div class="text-left">
                                    <a href="{{ url("about-us") }}" class="site-button btn-half"><span>Read More</span></a>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-12">
                            <div class="video-section-full-v2">
                                <div class="video-section-full bg-no-repeat bg-cover bg-center overlay-wraper m-b30"
                                     style="background-image:url({{ asset("assets/images/video-bg.jpg") }})">
                                    <div class="overlay-main bg-black opacity-04"></div>
                                    <div class="video-section-inner">
                                        <div class="video-section-content">
                                            <a href="{{ loadSettings()['about_video_url'] }}?color=ffffff&amp;title=0&amp;byline=0&amp;portrait=0"
                                               class="mfp-video play-now">
                                                <i class="icon fa fa-play"></i>
                                                <span class="ripple"></span>
                                            </a>
                                            <div class="video-section-bottom">
                                                <h3 class="sx-title text-white">{{ loadSettings()['about_video_title'] }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="breadcumb-shape ShapeAni background-image"
                 style="background-image: url(./images/scale.png);"></div>

        </div>
        <!-- ABOUT COMPANY END -->

        <!-- CLIENT LOGO SECTION START -->
        <div id="client-logo" class="section-full bg-white our-clients">
            <div class="container">
                <div class="section-head">
                    <div class="sx-separator-outer separator-center">
                        <div class="sx-separator bg-white bg-moving bg-repeat-x"
                             style="background-image:url({{ asset("assets/images/background/cross-line2.png") }})">
                            <h3 class="sep-line-one">Our Clients</h3>
                        </div>
                    </div>
                </div>

                <div class="section-content p-tb10 owl-btn-vertical-center">
                    <div class="owl-carousel home-client-carousel-2">

                        @foreach($clients as $client)
                            <div class="item">
                                <a href="javascript:;" class="client-logo-pic">
                                    <img src="{{ Storage::url($client->image) }}" alt="{{ $client->name }}">
                                </a>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
        <!-- CLIENT LOGO  SECTION End -->
    </div>
@endsection