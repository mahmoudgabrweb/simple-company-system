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
                            <h2 class="m-tb0">Projects</h2>
                        </div>
                    </div>
                    <!-- BREADCRUMB ROW -->

                    <div>
                        <ul class="sx-breadcrumb breadcrumb-style-2">
                            <li><a href="javascript:void(0);">Home</a></li>
                            <li>Projects</li>
                        </ul>
                    </div>

                    <!-- BREADCRUMB ROW END -->
                </div>
            </div>
        </div>
        <!-- INNER PAGE BANNER END -->

        <!-- OUR aia-projects START -->
        <div id="aia-projects" class="section-full mobile-page-padding bg-gray p-t80 p-b50">
            <div class="container">
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
                                                <a href="javascript:void(0);" title="READ MORE" rel="bookmark" data-id="{{ $project->id }}"
                                                   class="site-button-link project-read-more read-more-project-details">Read More</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-centers">
                            {{ $projects->links() }}
{{--                            <ul class="pagination m-t30 m-b0">--}}
{{--                                <li><a href="#">«</a></li>--}}
{{--                                <li class="active"><a href="#">1</a></li>--}}
{{--                                <li><a href="#">2</a></li>--}}
{{--                                <li><a href="#">3</a></li>--}}
{{--                                <li><a href="#">4</a></li>--}}
{{--                                <li><a href="#">5</a></li>--}}
{{--                                <li><a href="#">»</a></li>--}}
{{--                            </ul>--}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- OUR PROJECTS END -->
    </div>
@endsection