define(["jquery", "owlcarousel", "domReady!"], function ($) {
    $('.sm_megamenu_menu > li > div').parent().addClass('parent-item');

    var full_width = $('body').innerWidth();
    $('.full-content').css({'width': full_width});

    $(window).resize(function () {
        var full_width = $('body').innerWidth();
        $('.full-content').css({'width': full_width});
    });

    $('body').bind('touchstart', function () {
    }); // Fix hover on IOS

    // HOME PAGE 1

    $(".home-page-1 .sm-imageslider-content").owlCarousel({
        items: 1,
        autoplay: true,
        loop: true,
        nav: false,
        dots: true,
        autoplayHoverPause: true,
        margin: 1
    });

    $(".home-page-1 .featured-deals-w").owlCarousel({
        responsive: {
            0: {
                items: 1
            },
            480: {
                items: 1
            },
            768: {
                items: 1
            },
            992: {
                items: 1
            },
            1200: {
                items: 1
            }
        },

        autoplay: false,
        loop: false,
        nav: true,
        dots: false,
        autoplaySpeed: 500,
        navSpeed: 500,
        dotsSpeed: 500,
        autoplayHoverPause: true,
        margin: 30,
    });

    $(".home-page-1 .list-deals-w").owlCarousel({
        responsive: {
            0: {
                items: 1
            },
            480: {
                items: 2
            },
            768: {
                items: 4
            },
            991: {
                items: 5
            },
            1200: {
                items: 6
            }
        },

        autoplay: false,
        loop: false,
        nav: true,
        dots: false,
        autoplaySpeed: 500,
        navSpeed: 500,
        dotsSpeed: 500,
        autoplayHoverPause: true,
        margin: 10,
    });

    $(".home-page-1 .slider-1 .owl-carousel").owlCarousel({
        responsive: {
            0: {
                items: 1
            },
            480: {
                items: 1
            },
            768: {
                items: 1
            },
            991: {
                items: 1
            },
            1200: {
                items: 1
            }
        },

        autoplay: false,
        loop: false,
        nav: true,
        dots: false,
        autoplaySpeed: 500,
        navSpeed: 500,
        dotsSpeed: 500,
        autoplayHoverPause: true,
        margin: 30,
    });

    $(".home-page-1 .slider-2 .owl-carousel").owlCarousel({
        responsive: {
            0: {
                items: 1
            },
            480: {
                items: 1
            },
            768: {
                items: 1
            },
            991: {
                items: 1
            },
            1200: {
                items: 1
            }
        },

        autoplay: false,
        loop: false,
        nav: true,
        dots: false,
        autoplaySpeed: 500,
        navSpeed: 500,
        dotsSpeed: 500,
        autoplayHoverPause: true,
        margin: 30,
    });

    // HOME PAGE 2

    $(".slidershow-id2").owlCarousel({
        items: 1,
        autoplay: true,
        loop: true,
        nav: false,
        dots: true,
        autoplayHoverPause: true,
        margin: 1
    });

    // HOME 3
    setTimeout(function () {
        $(".slidershow-id3").owlCarousel({
            center: false,
            margin: 30,
            items: 1,
            nav: false,
            loop: true,
            dots: true,
            dotsSpeed: 1000,
            navText: ['&#139;', '&#155;'],
            slideBy: 1,
            autoplay: false,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            autoplaySpeed: 800,
            navSpeed: 1000,
            mouseDrag: true,
            responsiveRefreshRate: 100,
        });
    }, 1000);

    // HOME 3

    $(".slidershow-id4").owlCarousel({
        items: 1,
        autoplay: true,
        loop: true,
        nav: false,
        dots: true,
        autoplayHoverPause: true,
        margin: 1
    });

    // HOME 5
    setTimeout(function () {
        $(".slidershow-id5").owlCarousel({
            center: false,
            margin: 0,
            items: 1,
            nav: false,
            loop: true,
            dots: true,
            dotsSpeed: 1000,
            navText: ['&#139;', '&#155;'],
            slideBy: 1,
            autoplay: false,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            autoplaySpeed: 800,
            navSpeed: 1000,
            mouseDrag: true,
            responsiveRefreshRate: 100,
        });
    }, 1000);

    $(".id5-deals .owl-carousel").owlCarousel({
        responsive: {
            0: {
                items: 1
            },
            480: {
                items: 2
            },
            768: {
                items: 3
            },
            991: {
                items: 4
            },
            1200: {
                items: 4
            }
        },

        autoplay: false,
        loop: false,
        nav: true,
        dots: false,
        autoplaySpeed: 500,
        navSpeed: 500,
        dotsSpeed: 500,
        autoplayHoverPause: true,
        margin: 30,
    });

    // HOME PAGE 6

    $(".home-page-6 .sm-imageslider-content").owlCarousel({
        items: 1,
        autoplay: true,
        loop: true,
        nav: true,
        dots: true,
        autoplayHoverPause: true,
        margin: 1
    });

    // HOME PAGE 7

    $(".home-page-7 .cslider-items-container").owlCarousel({
        responsive: {
            0: {
                items: 1
            },
            480: {
                items: 2
            },
            768: {
                items: 3
            },
            991: {
                items: 4
            },
            1200: {
                items: 4
            }
        },

        autoplay: false,
        loop: false,
        nav: true,
        dots: false,
        autoplaySpeed: 500,
        navSpeed: 500,
        dotsSpeed: 500,
        autoplayHoverPause: true,
        margin: 30,
    });

});

