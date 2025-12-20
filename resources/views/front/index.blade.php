@extends("front.main")

@section("content")
    <!-- CONTENT START -->
    <div class="page-content">
        <!-- SLIDER START -->
        <div id="heroVideo" class="hero hero-video">
            <!-- Video Start -->
            <div class="hero-bg-video">
                <video autoplay="" muted="" loop="" id="myVideo">
                    <source src="{{ loadSettings()['slider_video'] }}" type="video/mp4">
                </video>

            </div>
            <!-- Video End -->
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-10">
                        <!-- Hero Content Start -->
                        <div class="hero-content">
                            <!-- Section Title Start -->
                            <div class="section-title">
                                <h3 class="wow fadeInUp"
                                    style="visibility: visible; animation-name: fadeInUp;">{{ loadSettings()['slider_header'] }}</h3>
                                <h1 class="text-anime-style-2" data-cursor="-opaque">
                                    <div style="position:relative;display:inline-block;">
                                        @foreach(explode(" ", loadSettings()['slider_title']) as $word)
                                            <div style="position:relative;display:inline-block;">
                                                @foreach(str_split($word) as $letter)
                                                    <div
                                                            style="position: relative; display: inline-block; opacity: 1; visibility: inherit; transform: translate(0px, 0px);">
                                                        {{ $letter }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </h1>
                                <p class="wow fadeInUp" data-wow-delay="0.2s"
                                   style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">{{ loadSettings()['slider_description'] }}</p>
                            </div>
                            <!-- Section Title End -->
                        </div>
                        <!-- Hero Content End -->
                    </div>
                </div>
            </div>
            <div class="breadcumb-shape ShapeAni background-image"
                 style="background-image: url({{ asset("assets/images/scale.png") }});"></div>
        </div>
        <!-- SLIDER END -->
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
        <!-- OUR SERVICES START -->
        <div id="our-services" class="section-full  mobile-page-padding bg-white  p-t80 p-b30 bg-repeat"
             style="background-image:url({{ asset("assets/images/background/bg-5.png") }});">
            <div class="section-content">
                <div class="container">
                    <!-- TITLE START -->
                    <div class="section-head">
                        <div class="sx-separator-outer separator-left">
                            <div class="sx-separator bg-white bg-moving bg-repeat-x"
                                 style="background-image:url({{ asset("assets/images/background/cross-line2.png") }})">
                                <h3 class="sep-line-one ">All Services</h3>
                            </div>
                        </div>
                    </div>
                    <!-- TITLE END -->
                    <div class="row">

                        @foreach($services as $index => $service)
                            <div class="col-lg-4 col-md-6 col-sm-12 m-b30">
                                <div class="sx-icon-box-wraper  icon-count-2-outer">
                                    <div class="icon-count-2 bg-white">
                                        <span class="icon-count-number">0{{ ++$index }}</span>
                                        <div class="icon-xl inline-icon m-b5 scale-in-center">
                                            <span class="icon-cell"><i class="{{ $service->icon }}"></i></span>
                                        </div>
                                        <div class="icon-content">
                                            <h4 class="sx-tilte">{{ $service->title }}</h4>
                                            <p>{{ $service->short_description }} ...</p>
                                            <div class="text-left">
                                                <a href="javascript:void(0);" data-toggle="modal"
                                                   data-target="#servicesModal"
                                                   class="site-button-link service-read-more"
                                                   data-service-title="{{ $service->title }}">Read More</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
                <div class="breadcumb-shape ShapeAni background-image"
                     style="background-image: url(./images/scale.png);"></div>

            </div>

            <div class="hilite-title text-left p-l50 text-uppercase">
                <strong>Services</strong>
            </div>


        </div>
        <!-- OUR SERVICES  END -->
        <!-- OUR aia-projects START -->
        <div id="aia-projects" class="section-full mobile-page-padding bg-gray p-t80 p-b50">
            <div class="container">
                <!-- TITLE START -->
                <div class="section-head">
                    <div class="sx-separator-outer separator-center">
                        <div class="sx-separator bg-white bg-moving bg-repeat-x"
                             style="background-image:url({{ asset("assets/images/background/cross-line2.png") }})">
                            <h3 class="sep-line-one">Latest Projects</h3>
                        </div>
                    </div>
                </div>
                <!-- TITLE END -->

                <!-- IMAGE CAROUSEL START -->
                <div class="section-content">
                    <div class="row">
                        @foreach($projects as $project)
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="aia-projects-post aia-projects-lg date-style-3 block-shadow">
                                    <div class="sx-post-media sx-img-effect zoom-slow">
                                        <div class="sx-thum-bx sx-img-overlay1 sx-img-effect yt-thum-box">
                                            <img src="{{ Storage::url($project->image) }}" alt="">
                                        </div>
                                    </div>
                                    <div class="sx-post-info  bg-white">
                                        <div class="sx-post-title ">
                                            <h4 class="post-title">
                                                <a href="javascript:void(0);">{{ $project->name }}</a>
                                            </h4>
                                        </div>
                                        <div class="sx-post-text">
                                            <p>{{ $project->short_description }}</p>
                                        </div>
                                        <div class="clearfix">
                                            <div class="sx-post-readmore pull-left">
                                                <a href="javascript:void(0);" title="READ MORE" rel="bookmark"
                                                   data-id="{{ $project->id }}"
                                                   class="site-button-link project-read-more read-more-project-details">Read
                                                    More</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="breadcumb-shape ShapeAni background-image"
                 style="background-image: url(./images/scale.png);"></div>

            <div class="hilite-title text-left p-l50 text-uppercase">
                <strong>Projects</strong>
            </div>

        </div>
        <!-- OUR PROJECTS END -->
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
    <!-- CONTENT END -->
@endsection