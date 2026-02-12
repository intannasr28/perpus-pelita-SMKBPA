
/*=============================================================
    Authour URI: www.binarycart.com
    Version: 1.1
    License: MIT
    
    http://opensource.org/licenses/MIT

    100% To use For Personal And Commercial Use.
   
    ========================================================  */

(function ($) {
    "use strict";
    var mainApp = {

        main_fun: function () {
            /*====================================
            METIS MENU 
            ======================================*/
            $('#main-menu').metisMenu();

            /*====================================
              LOAD APPROPRIATE MENU BAR
           ======================================*/
            $(window).bind("load resize", function () {
                if ($(this).width() < 768) {
                    $('div.sidebar-collapse').addClass('collapse')
                } else {
                    $('div.sidebar-collapse').removeClass('collapse')
                }
            });

            /*====================================
            MORRIS BAR CHART
         ======================================*/
            Morris.Bar({
                element: 'morris-bar-chart',
                data: [{
                    y: '2006',
                    a: 100,
                    b: 90
                }, {
                    y: '2007',
                    a: 75,
                    b: 65
                }, {
                    y: '2008',
                    a: 50,
                    b: 40
                }, {
                    y: '2009',
                    a: 75,
                    b: 65
                }, {
                    y: '2010',
                    a: 50,
                    b: 40
                }, {
                    y: '2011',
                    a: 75,
                    b: 65
                }, {
                    y: '2012',
                    a: 100,
                    b: 90
                }],
                xkey: 'y',
                ykeys: ['a', 'b'],
                labels: ['Series A', 'Series B'],
                hideHover: 'auto',
                resize: true
            });

            /*====================================
          MORRIS DONUT CHART
       ======================================*/
            Morris.Donut({
                element: 'morris-donut-chart',
                data: [{
                    label: "Download Sales",
                    value: 12
                }, {
                    label: "In-Store Sales",
                    value: 30
                }, {
                    label: "Mail-Order Sales",
                    value: 20
                }],
                resize: true
            });

            /*====================================
         MORRIS AREA CHART
      ======================================*/

            Morris.Area({
                element: 'morris-area-chart',
                data: [{
                    period: '2010 Q1',
                    iphone: 2666,
                    ipad: null,
                    itouch: 2647
                }, {
                    period: '2010 Q2',
                    iphone: 2778,
                    ipad: 2294,
                    itouch: 2441
                }, {
                    period: '2010 Q3',
                    iphone: 4912,
                    ipad: 1969,
                    itouch: 2501
                }, {
                    period: '2010 Q4',
                    iphone: 3767,
                    ipad: 3597,
                    itouch: 5689
                }, {
                    period: '2011 Q1',
                    iphone: 6810,
                    ipad: 1914,
                    itouch: 2293
                }, {
                    period: '2011 Q2',
                    iphone: 5670,
                    ipad: 4293,
                    itouch: 1881
                }, {
                    period: '2011 Q3',
                    iphone: 4820,
                    ipad: 3795,
                    itouch: 1588
                }, {
                    period: '2011 Q4',
                    iphone: 15073,
                    ipad: 5967,
                    itouch: 5175
                }, {
                    period: '2012 Q1',
                    iphone: 10687,
                    ipad: 4460,
                    itouch: 2028
                }, {
                    period: '2012 Q2',
                    iphone: 8432,
                    ipad: 5713,
                    itouch: 1791
                }],
                xkey: 'period',
                ykeys: ['iphone', 'ipad', 'itouch'],
                labels: ['iPhone', 'iPad', 'iPod Touch'],
                pointSize: 2,
                hideHover: 'auto',
                resize: true
            });

            /*====================================
    MORRIS LINE CHART
 ======================================*/
            Morris.Line({
                element: 'morris-line-chart',
                data: [{
                    y: '2006',
                    a: 100,
                    b: 90
                }, {
                    y: '2007',
                    a: 75,
                    b: 65
                }, {
                    y: '2008',
                    a: 50,
                    b: 40
                }, {
                    y: '2009',
                    a: 75,
                    b: 65
                }, {
                    y: '2010',
                    a: 50,
                    b: 40
                }, {
                    y: '2011',
                    a: 75,
                    b: 65
                }, {
                    y: '2012',
                    a: 100,
                    b: 90
                }],
                xkey: 'y',
                ykeys: ['a', 'b'],
                labels: ['Series A', 'Series B'],
                hideHover: 'auto',
                resize: true
            });
           
     
        },

        initialization: function () {
            mainApp.main_fun();

        }

    }
    // Initializing ///

    $(document).ready(function () {
        mainApp.main_fun();
        
        // Enhanced Interactive Effects
        // =============================
        
        // Smooth page transition animation
        $('body').fadeIn(300);
        
        // Sidebar menu item hover effect with smooth animation
        $('.sidebar-menu > li > a').hover(
            function() {
                $(this).addClass('active-menu-item');
            },
            function() {
                if (!$(this).parent().hasClass('active')) {
                    $(this).removeClass('active-menu-item');
                }
            }
        );
        
        // Button hover effect with ripple
        $('.btn').on('mouseenter', function() {
            if (!$(this).is(':disabled')) {
                $(this).css({
                    'transform': 'translateY(-2px)',
                    'box-shadow': '0 6px 14px rgba(0,0,0,0.15)'
                });
            }
        }).on('mouseleave', function() {
            $(this).css({
                'transform': 'translateY(0)',
                'box-shadow': '0 2px 4px rgba(0,0,0,0.1)'
            });
        });
        
        // Small box hover effect
        $('.small-box').hover(
            function() {
                $(this).find('.small-box-footer').slideDown(200);
            },
            function() {
                $(this).find('.small-box-footer').slideUp(200);
            }
        );
        
        // Table row hover effect
        $('.table-striped tbody tr').hover(
            function() {
                $(this).css('background-color', 'rgba(30, 136, 229, 0.2)');
            },
            function() {
                $(this).css('background-color', '');
            }
        );
        
        // Form input focus effect
        $('.form-control').on('focus', function() {
            $(this).parent().addClass('focused');
        }).on('blur', function() {
            $(this).parent().removeClass('focused');
        });
        
        // Smooth scroll to top button
        if ($('.scroll-to-top').length) {
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    $('.scroll-to-top').fadeIn();
                } else {
                    $('.scroll-to-top').fadeOut();
                }
            });
            
            $('.scroll-to-top').click(function() {
                $('html, body').animate({scrollTop: 0}, 800);
                return false;
            });
        }
        
        // Enhanced DataTable styling
        if ($.fn.DataTable) {
            $('.datatable').DataTable({
                'aaSorting': [],
                'columnDefs': [{
                    'defaultContent': '-',
                    'targets': '_all'
                }],
                'responsive': true,
                'dom': '<"top"f>rt<"bottom"lpi><"clear">'
            });
        }
        
        // Add smooth transition to modals
        $('.modal').on('show.bs.modal', function() {
            $(this).find('.modal-dialog').css({
                'opacity': '0',
                'transform': 'scale(0.7)'
            }).animate({opacity: 1}, 300).css({
                'transform': 'scale(1)'
            }, 300);
        });
        
        // Tooltip and Popover initialization
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
        
        // Menu item active state
        $('a').on('click', function() {
            var href = $(this).attr('href');
            if (href && href.indexOf('page=') > -1) {
                $('.sidebar-menu li a').removeClass('active-menu-item');
                $(this).addClass('active-menu-item');
            }
        });
        
        // Loading animation
        $(document).on('ajaxStart', function() {
            $('body').css('opacity', '0.7');
        }).on('ajaxStop', function() {
            $('body').css('opacity', '1');
        });
    });

}(jQuery));
