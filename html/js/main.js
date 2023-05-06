// PRELOADER

$(window).on('load', function () {
    $('.preloader').delay(1000).fadeOut(500);
});

// STICKY NAVIGATION

$(function() {
    var sticky = $("nav");

    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        if (scroll >= 50) {
            sticky.addClass("stickynav");
        } else {
            sticky.removeClass("stickynav");
        }
    });
});

// GAMES SLIDER (SLICK JS)

$(document).ready(function(){
    $('.games').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        dots: false,
        arrows: false,
        autoplaySpeed: 2500,
        responsive: [
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 992,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                }
            },

        ]
    });
});