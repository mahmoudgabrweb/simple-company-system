const General = function () {

    const loadOneProjectDetails = function () {
        $(document).on("click", ".read-more-project-details", function (e) {
            e.preventDefault();
            const id = $(this).attr("data-id");
            $.ajax({
                url: `/projects/load-details/${id}`, // Use Laravel's route() helper
                type: "GET",
                success: function (response) {
                    console.log({response})
                    $("#projectModal .project-modal-content").html(response.data);
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
            initProjectSlider();
            $("#projectModal").modal("show");
        });
    };

    const initProjectSlider = function () {
        // Destroy existing carousel if it exists
        if ($('#projectSlider').hasClass('owl-loaded')) {
            $('#projectSlider').trigger('destroy.owl.carousel');
            $('#projectSlider').removeClass('owl-loaded');
        }

        // Initialize carousel with proper settings
        $('#projectSlider').owlCarousel({
            items: 1,
            loop: true,
            nav: true,
            dots: true,
            autoplay: false,
            autoplayTimeout: 5000,
            smartSpeed: 500,
            navText: ['&#8249;', '&#8250;'],
            navClass: ['owl-prev', 'owl-next'],
            dotsClass: 'owl-dots',
            dotClass: 'owl-dot',
            responsive: {
                0: {
                    items: 1,
                    nav: true,
                    dots: true
                },
                768: {
                    items: 1,
                    nav: true,
                    dots: true
                },
                1200: {
                    items: 1,
                    nav: true,
                    dots: true
                }
            }
        });
    }

    return {
        init: function () {
            console.log("ddd")
            loadOneProjectDetails();
        }
    }
}();

General.init();