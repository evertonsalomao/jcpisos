(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();
    
    
    // Initiate the wowjs
    new WOW().init();

    
    // Portfolio isotope and filter
    var portfolioIsotope = $('.portfolio-container').isotope({
        itemSelector: '.portfolio-item',
        layoutMode: 'fitRows'
    });
    $('#portfolio-flters li').on('click', function () {
        $("#portfolio-flters li").removeClass('active');
        $(this).addClass('active');

        portfolioIsotope.isotope({filter: $(this).data('filter')});
    });


    // Sticky Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.sticky-top').addClass('shadow-sm').css('top', '0px');
        } else {
            $('.sticky-top').removeClass('shadow-sm').css('top', '-100px');
        }
    });
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Facts counter
    $('[data-toggle="counter-up"]').counterUp({
        delay: 10,
        time: 2000
    });


    // Header carousel
    $(".header-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        loop: true,
        nav: false,
        dots: true,
        items: 1,
        dotsData: true,
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        autoplayTimeout: 9000, // 5 seconds between transitions (increased from default)
        smartSpeed: 1000,
        center: true,
        dots: false,
        loop: true,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ],
        responsive: {
            0:{
                items:1
            },
            768:{
                items:1
            },
            992: {
            items: 3,
            margin: 30
        }
        }
    });


// Produtos carousel - Configuração otimizada
$(".produtos-carousel").owlCarousel({
    autoplay: true,
    autoplayTimeout: 12000, // 5 seconds between transitions (increased from default)
    smartSpeed: 1000, // This controls the animation speed, not the delay
    center: true,
    dots: false,
    loop: true,
    nav: true,
    navText: [
        '<i class="bi bi-arrow-left"></i>',
        '<i class="bi bi-arrow-right"></i>'
    ],
    responsive: {
        0: {
            items: 1,
            margin: 10
        },
        768: {
            items: 1,
            margin: 20
        },
        992: {
            items: 3,
            margin: 30
        }
    }
});


// instalacao carousel - Configuração otimizada
$(".instalacao-carousel").owlCarousel({
    autoplay: true,
    autoplayTimeout: 12000,
    smartSpeed: 1000,
    center: false, // Alterado para false para melhor controle do quarto item
    dots: false,
    loop: true,
    nav: true,
    navText: [
        '<i class="bi bi-arrow-left"></i>',
        '<i class="bi bi-arrow-right"></i>'
    ],
    responsive: {
        0: {
            items: 1,
            margin: 10,
            startPosition: 0 // Garante posição inicial consistente
        },
        768: {
            items: 1,
            margin: 20,
            startPosition: 0
        },
        992: {
            items: 3,
            margin: 30,
            startPosition: 0 // Posição inicial para mostrar o quarto item parcialmente
        }
    },
    // onInitialized: function(event) {
       
    //     setTimeout(function() {
    //         $('.instalacao-carousel').trigger('to.owl.carousel', [3]);
    //     }, 100);
    // }
});

// Função para ajustar dinamicamente a posição da seta
function ajustarPosicaoSeta() {
    var carousel = $('.instalacao-carousel');
    var itemWidth = carousel.find('.owl-item').width();
    var margin = parseInt(carousel.find('.owl-item').css('margin-right'));
    
    // Calcula a posição exata do quarto item
    var posicaoQuartoItem = (itemWidth + margin) * 3;
    
    // Ajusta a seta para ficar sobre o quarto item
    $('.instalacao-carousel .owl-next').css({
        'right': 'calc(25% + ' + (posicaoQuartoItem * 0.1) + 'px)'
    });
}

// Ajusta a posição quando a janela é redimensionada
$(window).on('resize', function() {
    ajustarPosicaoSeta();
});

// Ajusta a posição quando o carousel é inicializado
$('.instalacao-carousel').on('initialized.owl.carousel', function() {
    ajustarPosicaoSeta();
});
  
// aplicaoes carousel - Configuração otimizada
$(".aplicacoes-carousel").owlCarousel({
    autoplay: true,
    autoplayTimeout: 12000, // 5 seconds between transitions (increased from default)
    smartSpeed: 1000, // This controls the animation speed, not the delay
    center: true,
    dots: false,
    loop: true,
    nav: true,
    navText: [
        '<i class="bi bi-arrow-left"></i>',
        '<i class="bi bi-arrow-right"></i>'
    ],
    responsive: {
        0: {
            items: 1,
            margin: 10
        },
        768: {
            items: 1,
            margin: 20
        },
        992: {
            items: 3,
            margin: 30
        }
    }
});

// aplicaoes carousel - Configuração otimizada
$(".beneficios-carousel").owlCarousel({
    autoplay: true,
    autoplayTimeout: 12000, // 5 seconds between transitions (increased from default)
    smartSpeed: 1000, // This controls the animation speed, not the delay
    center: true,
    dots: false,
    loop: true,
    nav: true,
    navText: [
        '<i class="bi bi-arrow-left"></i>',
        '<i class="bi bi-arrow-right"></i>'
    ],
    responsive: {
        0: {
            items: 1,
            margin: 10
        },
        768: {
            items: 1,
            margin: 20
        },
        992: {
            items: 3,
            margin: 30
        }
    }
});


// terraplanagem carousel - Configuração otimizada
$(".terraplanagem-carousel").owlCarousel({
    autoplay: true,
    autoplayTimeout: 12000,
    smartSpeed: 1000,
    center: false, // Alterado para false para melhor controle do quarto item
    dots: false,
    loop: true,
    nav: true,
    navText: [
        '<i class="bi bi-arrow-left"></i>',
        '<i class="bi bi-arrow-right"></i>'
    ],
    responsive: {
        0: {
            items: 1,
            margin: 10,
            
        },
        768: {
            items: 1,
            margin: 20,
            
        },
        992: {
            items: 3,
            margin: 30,
            
        }
    },
    // onInitialized: function(event) {
       
    //     setTimeout(function() {
    //         $('.instalacao-carousel').trigger('to.owl.carousel', [3]);
    //     }, 100);
    // }
});

// Função para ajustar dinamicamente a posição da seta
function ajustarPosicaoSeta() {
    var carousel = $('.terraplanagem-carousel');
    var itemWidth = carousel.find('.owl-item').width();
    var margin = parseInt(carousel.find('.owl-item').css('margin-right'));
    
    // Calcula a posição exata do quarto item
    var posicaoQuartoItem = (itemWidth + margin) * 3;
    
    // Ajusta a seta para ficar sobre o quarto item
    $('.terraplanagem-carousel .owl-next').css({
        'right': 'calc(25% + ' + (posicaoQuartoItem * 0.1) + 'px)'
    });
}

// Ajusta a posição quando a janela é redimensionada
$(window).on('resize', function() {
    ajustarPosicaoSeta();
});

// Ajusta a posição quando o carousel é inicializado
$('.terraplanagem-carousel').on('initialized.owl.carousel', function() {
    ajustarPosicaoSeta();
});




    
})(jQuery);

