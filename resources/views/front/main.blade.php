<!DOCTYPE html>
<html lang="en">

<head>
    <!-- META -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords"
          content="interior design, landscape design, home decor, modern interiors, luxury interiors, garden design, outdoor spaces, architecture, space planning, residential design, commercial interiors, sustainable design"/>
    <meta name="author" content="bugzii.com"/>
    <meta name="robots" content="index, follow"/>
    <meta name="description"
          content="Transform your home or workspace with our professional interior and landscape design services. We specialize in creating modern, elegant, and functional spaces that reflect your lifestyle and vision."/>
    <!-- FAVICONS ICON -->
    <link rel="icon" href="{{ asset("assets/images/favicon.png") }}" type="image/x-icon"/>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset("assets/images/favicon.png") }}"/>
    <!-- PAGE TITLE HERE -->
    <title>AIA | Home</title>
    <!-- MOBILE SPECIFIC -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- BOOTSTRAP STYLE SHEET -->
    <link rel="stylesheet" type="text/css" href="{{ asset("assets/css/bootstrap.min.css") }}">
    <!-- FONTAWESOME STYLE SHEET -->
    <link rel="stylesheet" type="text/css" href="{{ asset("assets/css/fontawesome/css/font-awesome.min.css") }}"/>
    <!-- OWL CAROUSEL STYLE SHEET -->
    <link rel="stylesheet" type="text/css" href="{{ asset("assets/css/owl.carousel.min.css") }}">
    <!-- MAGNIFIC POPUP STYLE SHEET -->
    <link rel="stylesheet" type="text/css" href="{{ asset("assets/css/magnific-popup.min.css") }}">
    <!-- FLATICON STYLE SHEET -->
    <link rel="stylesheet" type="text/css" href="{{ asset("assets/css/flaticon.min.css") }}">
    <!-- MAIN STYLE SHEET -->
    <link rel="stylesheet" type="text/css" href="{{ asset("assets/css/style.min.css") }}">
</head>

<body>

<div class="page-wraper">
    <!-- HEADER START -->
    <header class="site-header header-style-1 nav-wide mobile-sider-drawer-menu">
        <div class="top-bar bg-gray">
            <div class="container">
                <div class="d-flex justify-content-end">
                    <ul class="list-unstyled e-p-bx">
                        <li><span>Mail us:</span>{{ loadSettings()['email_address'] }}</li>
                        <li><span>Call us:</span>{{ loadSettings()['phone_number'] }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="sticky-header main-bar-wraper navbar-expand-lg">
            <div class="main-bar header-left-gray-block bg-white">
                <div class="container clearfix">
                    <div class="logo-header">
                        <div class="logo-header-inner logo-header-one">
                            <a href="{{ url("/") }}">
                                <img src="{{ asset("assets/images/logo.png") }}" alt="Logo" class="logo-default"/>
                                <img src="{{ asset("assets/images/logo-white.png") }}" alt="White Logo"
                                     class="logo-white"
                                     style="display:none;"/>
                            </a>
                        </div>
                    </div>
                    <!-- NAV Toggle Button -->
                    <button id="mobile-side-drawer" data-target=".header-nav" data-toggle="collapse" type="button"
                            class="navbar-toggler collapsed">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar icon-bar-first"></span>
                        <span class="icon-bar icon-bar-two"></span>
                        <span class="icon-bar icon-bar-three"></span>
                    </button>

                    <!-- EXTRA NAV -->
                    <div class="extra-nav">
                        <div class="extra-cell">
                            <div class="contact-slide-show">
                                <a href="{{ Storage::url(loadSettings()['portfolio']) }}" target="_blank"
                                   class="get-in-touch-btn from-top">Get Our Portfolio</a>
                            </div>
                        </div>
                    </div>
                    <!-- EXTRA Nav -->

                    <!-- MAIN NAVIGATION -->
                    <div class="header-nav nav-dark navbar-collapse collapse justify-content-start collapse">
                        <ul class=" nav navbar-nav">
                            <li class="active"><a href="{{ url("/") }}">Home</a></li>
                            <li><a href="{{ url("projects") }}">Our Projects</a></li>
                            <li><a href="{{ url("services") }}">Our Services</a></li>
                            <li><a href="{{ url("about-us") }}">About us</a></li>
                            <li><a href="{{ url("contact-us") }}">Contact us</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- HEADER END -->

    @yield("content")

    <!-- FOOTER START -->
    <footer class="site-footer footer-large  footer-dark	footer-wide">
        <!-- FOOTER BLOCKES START -->
        <div class="footer-top overlay-wraper bg-cover"
             style="background-image:url({{ asset("assets/images/background/f-bg.jpg") }})">
            <div class="overlay-main sx-bg-secondry opacity-08"></div>
            <div class="container">
                <div class="row">
                    <!-- ABOUT COMPANY -->
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="widget widget_about">
                            <!--<h4 class="widget-title">About Company</h4>-->
                            <div class="logo-footer clearfix p-b15">
                                <a href="{{ url("/") }}"><img src="{{ asset("assets/images/logo-white.png") }}" alt=""></a>
                            </div>
                            <p>{{ loadSettings()['about_body'] }}</p>

                            <ul class="social-icons  sx-social-links">
                                {{--                                <li><a href="javascript:void(0);" class="fa fa-behance"></a></li>--}}
                                <li><a href="{{ loadSettings()['facebook_url'] }}" target="_blank"
                                       class="fa fa-facebook"></a></li>
                                <li><a href="{{ loadSettings()['youtube_url'] }}" target="_blank"
                                       class="fa fa-youtube"></a></li>
                                <li><a href="{{ loadSettings()['instagram_url'] }}" target="_blank"
                                       class="fa fa-instagram"></a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- USEFUL LINKS -->
                    <div class="col-lg-3 col-md-6 col-sm-12 footer-col-3">
                        <div class="widget widget_services inline-links">
                            <h5 class="widget-title">Useful links</h5>
                            <ul>
                                <li><a href="{{ url("about-us") }}">About</a></li>
                                <li><a href="{{ url("services") }}">Services</a></li>
                                <li><a href="{{ url("projects") }}">Projects</a></li>
                                <li><a href="{{ url("services") }}">aia-Services</a></li>
                                <li><a href="{{ url("contact-us") }}">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- CONTACT US -->
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="widget widget_address_outer">
                            <h5 class="widget-title">Contact Us</h5>
                            <ul class="widget_address">
                                <li>{{ loadSettings()['address'] }}</li>
                                <li>{{ loadSettings()['email_address'] }}</li>
                                <li>{{ loadSettings()['phone_number'] }}</li>
                            </ul>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <!-- FOOTER COPYRIGHT -->
        <div class="footer-bottom overlay-wraper">
            <div class="overlay-main"></div>
            <div class="container">
                <div class="row">
                    <div class="sx-footer-bot-center text-center  col-12">
                            <span class="copyrights-text">©
                                <script>document.write(new Date().getFullYear());</script> all rights reserved for <a
                                        href="#" target="_blank" rel="noopener noreferrer">AIA</a>.
                            </span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- FOOTER END -->
    <!-- BUTTON TOP START -->
    <button class="scroltop"><span class="fa fa-angle-up  relative" id="btn-vibrate"></span></button>
    <!-- PROJECT DETAILS MODAL START -->
    <div class="modal fade project-modal" id="projectModal" tabindex="-1" role="dialog"
         aria-labelledby="projectModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document"> <!-- Required wrapper -->
            <div class="modal-content project-modal-content"> <!-- Keep .modal-content -->
            </div>
        </div>
    </div>
    <!-- PROJECT DETAILS MODAL END -->
    <!-- SERVICES MODAL START -->
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
    <!-- SERVICES MODAL END -->
</div> <!-- LOADING AREA START ===== -->
<div class="loading-area">
    <div class="loading-box"></div>
    <div class="loading-pic">
        <div class="cssload-spinner">
            <div class="cssload-cube cssload-cube0"></div>
            <div class="cssload-cube cssload-cube1"></div>
            <div class="cssload-cube cssload-cube2"></div>
            <div class="cssload-cube cssload-cube3"></div>
            <div class="cssload-cube cssload-cube4"></div>
            <div class="cssload-cube cssload-cube5"></div>
            <div class="cssload-cube cssload-cube6"></div>
            <div class="cssload-cube cssload-cube7"></div>
            <div class="cssload-cube cssload-cube8"></div>
            <div class="cssload-cube cssload-cube9"></div>
            <div class="cssload-cube cssload-cube10"></div>
            <div class="cssload-cube cssload-cube11"></div>
            <div class="cssload-cube cssload-cube12"></div>
            <div class="cssload-cube cssload-cube13"></div>
            <div class="cssload-cube cssload-cube14"></div>
            <div class="cssload-cube cssload-cube15"></div>
        </div>
    </div>
</div>
<!-- LOADING AREA  END ====== -->

<!-- SOCIAL MEDIA CHAT WIDGET -->
<div class="social-chat-widget" id="socialChatWidget">
    <div class="social-chat-main" id="socialChatMain">
        <i class="fa fa-comments"></i>
    </div>
    <div class="social-chat-icons" id="socialChatIcons">
        <a href="https://wa.me/{{ loadSettings()['phone_number'] }}" target="_blank" class="social-icon whatsapp"
           data-tooltip="Chat on WhatsApp">
            <i class="fa fa-whatsapp"></i>
        </a>
        <a href="tel:{{ loadSettings()['phone_number'] }}" class="social-icon phone" data-tooltip="Call Us Now">
            <i class="fa fa-phone"></i>
        </a>
        <a href="{{ loadSettings()['facebook_url'] }}" target="_blank" class="social-icon facebook"
           data-tooltip="Message on Facebook">
            <i class="fa fa-facebook-f"></i>
        </a>
    </div>
</div>

<!-- JAVASCRIPT  FILES ========================================= -->
<script src="{{ asset("assets/js/jquery-1.12.4.min.js") }}"></script><!-- JQUERY.MIN JS -->
<script src="{{ asset("assets/js/popper.min.js") }}"></script><!-- POPPER.MIN JS -->
<script src="{{ asset("assets/js/bootstrap.min.js") }}"></script><!-- BOOTSTRAP.MIN JS -->
<script src="{{ asset("assets/js/magnific-popup.min.js") }}"></script><!-- MAGNIFIC-POPUP JS -->
<script src="{{ asset("assets/js/waypoints.min.js") }}"></script><!-- WAYPOINTS JS -->
<script src="{{ asset("assets/js/waypoints-sticky.min.js") }}"></script><!-- sticky header JS -->
<script src="{{ asset("assets/js/owl.carousel.min.js") }}"></script><!-- OWL  SLIDER  -->
<script src="{{ asset("assets/js/theia-sticky-sidebar.js") }}"></script><!--sticky content-->
<script src="{{ asset("assets/js/custom.js") }}"></script><!-- CUSTOM FUCTIONS  -->
<script src="{{ asset("assets/js/general.js") }}"></script>
</body>

</html>