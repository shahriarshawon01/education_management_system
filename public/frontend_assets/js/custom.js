//Our school life
$('.school_life').owlCarousel({
    loop:true,
    // margin:10,
    responsiveClass:true,
    nav:true,
    dots:false,
    animateOut: 'slideOutDown',
    animateIn: 'flipInX',
    smartSpeed: 1500,
    autoplay:true,
    autoplayTimeout:5000,
    autoplayHoverPause:true,
    responsive:{
        0:{
            items:1,
            nav:true
        },
        600:{
            items:1,
            nav:false
        },
        768:{
            items:2,
            nav:true,
            loop:false
        },
        1000:{
            items:3,
            nav:true,
            loop:false
        }
    }
})
