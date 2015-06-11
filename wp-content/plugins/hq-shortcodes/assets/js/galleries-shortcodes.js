jQuery(document).ready(function ($) {
    // Prepare items arrays for lightbox
    $('.hq-lightbox-gallery').each(function () {
        var slides = [];
        $(this).find('.hq-slider-slide, .hq-carousel-slide, .hq-custom-gallery-slide').each(function (i) {
            $(this).attr('data-index', i);
            slides.push({
                src: $(this).children('a').attr('href')
            });
        });
        $(this).data('slides', slides);
    });
    // Enable sliders
    $('.hq-slider').each(function () {
        // Prepare data
        var $slider = $(this);
        // Apply Swiper
        var $swiper = $slider.swiper({
            wrapperClass: 'hq-slider-slides',
            slideClass: 'hq-slider-slide',
            slideActiveClass: 'hq-slider-slide-active',
            slideVisibleClass: 'hq-slider-slide-visible',
            pagination: '#' + $slider.attr('id') + ' .hq-slider-pagination',
            autoplay: $slider.data('autoplay'),
            paginationClickable: true,
            grabCursor: true,
            mode: 'horizontal',
            mousewheelControl: $slider.data('mousewheel'),
            speed: $slider.data('speed'),
            calculateHeight: $slider.hasClass('hq-slider-responsive-yes'),
            loop: true
        });
        // Prev button
        $slider.find('.hq-slider-prev').click(function (e) {
            $swiper.swipeNext();
        });
        // Next button
        $slider.find('.hq-slider-next').click(function (e) {
            $swiper.swipePrev();
        });
    });
    // Enable carousels
    $('.hq-carousel').each(function () {
        // Prepare data
        var $carousel = $(this),
                $slides = $carousel.find('.hq-carousel-slide');
        // Apply Swiper
        var $swiper = $carousel.swiper({
            wrapperClass: 'hq-carousel-slides',
            slideClass: 'hq-carousel-slide',
            slideActiveClass: 'hq-carousel-slide-active',
            slideVisibleClass: 'hq-carousel-slide-visible',
            pagination: '#' + $carousel.attr('id') + ' .hq-carousel-pagination',
            autoplay: $carousel.data('autoplay'),
            paginationClickable: true,
            grabCursor: true,
            mode: 'horizontal',
            mousewheelControl: $carousel.data('mousewheel'),
            speed: $carousel.data('speed'),
            slidesPerView: ($carousel.data('items') > $slides.length) ? $slides.length : $carousel.data('items'),
            slidesPerGroup: $carousel.data('scroll'),
            calculateHeight: $carousel.hasClass('hq-carousel-responsive-yes'),
            loop: true
        });
        // Prev button
        $carousel.find('.hq-carousel-prev').click(function (e) {
            $swiper.swipeNext();
        });
        // Next button
        $carousel.find('.hq-carousel-next').click(function (e) {
            $swiper.swipePrev();
        });
    });
    // Enable lightbox
    $('.hq-lightbox-gallery').on('click', '.hq-slider-slide, .hq-carousel-slide, .hq-custom-gallery-slide', function (e) {
        e.preventDefault();
        var slides = $(this).parents('.hq-lightbox-gallery').data('slides');
        $.magnificPopup.open({
            items: slides,
            type: 'image',
            mainClass: 'mfp-img-mobile',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0, 1],
                tPrev: hq_magnific_popup.prev,
                tNext: hq_magnific_popup.next,
                tCounter: hq_magnific_popup.counter
            },
            tClose: hq_magnific_popup.close,
            tLoading: hq_magnific_popup.loading
        }, $(this).data('index'));
    });
});