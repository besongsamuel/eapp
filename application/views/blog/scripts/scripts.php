    <script src="<?php echo base_url("assets/blog/js/retina.js"); ?>"></script>
    <script src="<?php echo base_url("assets/blog/"); ?>js/wow.min.js"></script>
    <script src="<?php echo base_url("assets/blog/"); ?>js/jquery.stellar.js"></script>
    <script src="<?php echo base_url("assets/blog/"); ?>js/jquery.fitvids.js"></script>
    
    <script>
        $(document).ready(function () {
            // Target your .container, .wrapper, .post, etc.
            $(".blog-item").fitVids();
        });
    </script>
    
    <!-- Portfolio -->
    <script src="<?php echo base_url("assets/blog/js/jquery.isotope.min.js"); ?>"></script>
    <script src="<?php echo base_url("assets/blog/js/portfolio_01.js"); ?>"></script>
    <!-- Carousel -->
    <script src="<?php echo base_url("assets/blog/js/owl.carousel.js"); ?>"></script>
    <script src="<?php echo base_url("assets/blog/js/owl-scripts.js"); ?>"></script>
    <!-- FlexSlider Scripts-->
    <script src="<?php echo base_url("assets/blog/js/jquery.flexslider.js"); ?>"></script>
    <script type="text/javascript">
        (function ($) {
            "use strict";
            $('.flexslider').flexslider({
                animation: 'fade',
                slideshow: true,
                controlNav: false,
                animationLoop: true
            });
        })(jQuery);
    </script>
