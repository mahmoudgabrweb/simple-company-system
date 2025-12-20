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
                            <h2 class="m-tb0">Services</h2>
                        </div>
                    </div>
                    <!-- BREADCRUMB ROW -->

                    <div>
                        <ul class="sx-breadcrumb breadcrumb-style-2">
                            <li><a href="{{ url("/") }}">Home</a></li>
                            <li>Services</li>
                        </ul>
                    </div>

                    <!-- BREADCRUMB ROW END -->
                </div>
            </div>
        </div>
        <!-- INNER PAGE BANNER END -->
        <!-- OUR SERVICES START -->
        <div id="our-services" class="section-full  mobile-page-padding bg-white  p-t80 p-b30 bg-repeat"
             style="background-image:url({{ asset("assets/images/background/bg-5.png") }});">
            <div class="section-content">
                <div class="container">
                    <div class="row">
                        <!-- Block one -->

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

                        <!-- Block Two -->
                        {{--                        <div class="col-lg-4 col-md-6 col-sm-12 m-b30">--}}

                        {{--                            <div class="sx-icon-box-wraper icon-count-2-outer">--}}
                        {{--                                <div class="icon-count-2 bg-white">--}}
                        {{--                                    <span class="icon-count-number">02</span>--}}
                        {{--                                    <div class="icon-xl inline-icon m-b5 scale-in-center">--}}
                        {{--                                        <span class="icon-cell"><i class="flaticon-stairs"></i></span>--}}
                        {{--                                    </div>--}}
                        {{--                                    <div class="icon-content">--}}
                        {{--                                        <h4 class="sx-tilte">Interior</h4>--}}
                        {{--                                        <p>Analysis and planning services that help both the client and architects--}}
                        {{--                                            to work out the forthcoming project...</p>--}}
                        {{--                                        <div class="text-left">--}}
                        {{--                                            <a href="javascript:void(0);" data-toggle="modal"--}}
                        {{--                                               data-target="#servicesModal"--}}
                        {{--                                               class="site-button-link service-read-more"--}}
                        {{--                                               data-service-title="Interior">Read More</a>--}}
                        {{--                                        </div>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}

                        {{--                        </div>--}}
                        {{--                        <!-- Block Three -->--}}
                        {{--                        <div class="col-lg-4 col-md-6 col-sm-12 m-b30">--}}

                        {{--                            <div class="sx-icon-box-wraper icon-count-2-outer">--}}
                        {{--                                <div class="icon-count-2 bg-white">--}}
                        {{--                                    <span class="icon-count-number">03</span>--}}
                        {{--                                    <div class="icon-xl inline-icon m-b5 scale-in-center">--}}
                        {{--                                        <span class="icon-cell"><i class="flaticon-window"></i></span>--}}
                        {{--                                    </div>--}}
                        {{--                                    <div class="icon-content">--}}
                        {{--                                        <h4 class="sx-tilte">Exterior</h4>--}}
                        {{--                                        <p>We offer comprehensive Architectural Engineering Services including--}}
                        {{--                                            Interior design, Master planning, 3D modeling...</p>--}}
                        {{--                                        <div class="text-left">--}}
                        {{--                                            <a href="javascript:void(0);" data-toggle="modal"--}}
                        {{--                                               data-target="#servicesModal"--}}
                        {{--                                               class="site-button-link service-read-more"--}}
                        {{--                                               data-service-title="Exterior">Read More</a>--}}
                        {{--                                        </div>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}

                        {{--                        </div>--}}
                        {{--                        <!-- Block Four -->--}}
                        {{--                        <div class="col-lg-4 col-md-6 col-sm-12 m-b30">--}}

                        {{--                            <div class="sx-icon-box-wraper icon-count-2-outer">--}}
                        {{--                                <div class="icon-count-2 bg-white">--}}
                        {{--                                    <span class="icon-count-number">04</span>--}}
                        {{--                                    <div class="icon-xl inline-icon m-b5 scale-in-center">--}}
                        {{--                                        <span class="icon-cell"><i class="flaticon-skyline"></i></span>--}}
                        {{--                                    </div>--}}
                        {{--                                    <div class="icon-content">--}}
                        {{--                                        <h4 class="sx-tilte">Architecture</h4>--}}
                        {{--                                        <p>Project management is the process by which our team plans and executes--}}
                        {{--                                            your project. We will develop it...</p>--}}
                        {{--                                        <div class="text-left">--}}
                        {{--                                            <a href="javascript:void(0);" data-toggle="modal"--}}
                        {{--                                               data-target="#servicesModal"--}}
                        {{--                                               class="site-button-link service-read-more"--}}
                        {{--                                               data-service-title="Architecture">Read More</a>--}}
                        {{--                                        </div>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}

                        {{--                        </div>--}}
                        {{--                        <!-- Block Five -->--}}
                        {{--                        <div class="col-lg-4 col-md-6 col-sm-12 m-b30">--}}

                        {{--                            <div class="sx-icon-box-wraper icon-count-2-outer">--}}
                        {{--                                <div class="icon-count-2 bg-white">--}}
                        {{--                                    <span class="icon-count-number">05</span>--}}
                        {{--                                    <div class="icon-xl inline-icon m-b5 scale-in-center">--}}
                        {{--                                        <span class="icon-cell"><i class="flaticon-bed"></i></span>--}}
                        {{--                                    </div>--}}
                        {{--                                    <div class="icon-content">--}}
                        {{--                                        <h4 class="sx-tilte">Furniture</h4>--}}
                        {{--                                        <p>Our team also provides consultations on all architectural issues, even if--}}
                        {{--                                            you need specific info about working...</p>--}}
                        {{--                                        <div class="text-left">--}}
                        {{--                                            <a href="javascript:void(0);" data-toggle="modal"--}}
                        {{--                                               data-target="#servicesModal"--}}
                        {{--                                               class="site-button-link service-read-more"--}}
                        {{--                                               data-service-title="Furniture">Read More</a>--}}
                        {{--                                        </div>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}

                        {{--                        </div>--}}
                        {{--                        <!-- Block Six -->--}}
                        {{--                        <div class="col-lg-4 col-md-6 col-sm-12 m-b30">--}}

                        {{--                            <div class="sx-icon-box-wraper icon-count-2-outer">--}}
                        {{--                                <div class="icon-count-2 bg-white">--}}
                        {{--                                    <span class="icon-count-number">06</span>--}}
                        {{--                                    <div class="icon-xl inline-icon m-b5 scale-in-center">--}}
                        {{--                                        <span class="icon-cell"><i class="flaticon-door"></i></span>--}}
                        {{--                                    </div>--}}
                        {{--                                    <div class="icon-content">--}}
                        {{--                                        <h4 class="sx-tilte">Decoration</h4>--}}
                        {{--                                        <p>We combine Interior and Exterior Design services and often provide them--}}
                        {{--                                            as a single solution. It helps us...</p>--}}
                        {{--                                        <div class="text-left">--}}
                        {{--                                            <a href="javascript:void(0);" data-toggle="modal"--}}
                        {{--                                               data-target="#servicesModal"--}}
                        {{--                                               class="site-button-link service-read-more"--}}
                        {{--                                               data-service-title="Decoration">Read More</a>--}}
                        {{--                                        </div>--}}
                        {{--                                    </div>--}}
                        {{--                                </div>--}}
                        {{--                            </div>--}}

                        {{--                        </div>--}}

                    </div>
                </div>
            </div>
        </div>
        <!-- OUR SERVICES  END -->
    </div>

    <div id="servicesModal" class="modal fade services-modal" tabindex="-1" role="dialog"
         aria-labelledby="servicesModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document"> <!-- Required Bootstrap wrapper -->
            <div class="modal-content services-modal-content"> <!-- Required Bootstrap element -->

                <div class="modal-header services-modal-header">
                    <h2 class="modal-title" id="servicesModalTitle">Our Professional Services</h2>
                    <button type="button" class="close services-modal-close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body services-modal-body">
                    <div class="service-content">
                        <div class="service-description">
                            <h3>Comprehensive Architectural Solutions</h3>
                            <p>
                                At AIA, we pride ourselves on delivering exceptional architectural and interior
                                design
                                services that transform your vision into reality. Our team of experienced
                                professionals
                                combines creativity, technical expertise, and innovative thinking to create spaces
                                that
                                are not only beautiful but also functional and sustainable.
                            </p>

                            <h4>What We Offer</h4>
                            <p>
                                Our comprehensive range of services covers every aspect of architectural design and
                                construction. From initial concept development to final project completion, we work
                                closely with our clients to ensure their vision is brought to life with precision
                                and
                                attention to detail.
                            </p>

                            <h4>Our Approach</h4>
                            <p>
                                We believe that great architecture begins with understanding our clients' needs,
                                lifestyle, and aspirations. Our collaborative approach ensures that every project
                                reflects the unique personality and requirements of those who will inhabit the
                                space.
                                We combine traditional craftsmanship with modern technology to deliver results that
                                exceed expectations.
                            </p>

                            <h4>Quality & Innovation</h4>
                            <p>
                                Quality is at the heart of everything we do. We use only the finest materials and
                                work
                                with trusted contractors and suppliers to ensure that every project meets our
                                exacting
                                standards. Our commitment to innovation means we're always exploring new techniques,
                                materials, and technologies to deliver cutting-edge solutions.
                            </p>

                            <div class="service-highlights">
                                <div class="highlight-item">
                                    <h5>Design Excellence</h5>
                                    <p>Award-winning designs that combine aesthetics with functionality</p>
                                </div>
                                <div class="highlight-item">
                                    <h5>Sustainable Solutions</h5>
                                    <p>Environmentally conscious designs that reduce environmental impact</p>
                                </div>
                                <div class="highlight-item">
                                    <h5>Client-Focused</h5>
                                    <p>Personalized service tailored to your specific needs and budget</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection