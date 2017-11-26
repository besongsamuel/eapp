  <!-- /#wrapper -->
    <!-- jQuery -->
    <script src="<?php echo base_url("assets/contact/js/jquery.js"); ?>"></script>
	<script src="<?php echo base_url("assets/contact/js/home-scripts.js"); ?>"></script>
	<script src="<?php echo base_url("assets/contact/js/jquery.stellar.js"); ?>"></script>
	<script src="<?php echo base_url("assets/contact/js/wow.min.js"); ?>"></script>

    <!-- Map Scripts-->
    <script src="http://maps.google.com/maps/api/js?key=AIzaSyBsAk_UvdPgPQTgOvXy8oAwsQtcGxLIdjk"></script>
    <!-- Google Map -->
    <script type="text/javascript">
        (function($) {
          "use strict";
            var locations = [
            ['<div class="infobox"><h3 class="title"><a href="about-1.html">Notre Bureau</a></h3><span>550 Avenue Saint-Dominique, </span><br>Saint-Hyacinthe, J2S 5M6</span><br>  </p></div></div></div>', 45.6231815, -72.9508469, 2]
            ];

            var map = new google.maps.Map(document.getElementById('map'), {
              zoom: 15,
                scrollwheel: true,
                navigationControl: true,
                mapTypeControl: true,
                scaleControl: false,
                draggable: true,
                styles: [ { "stylers": [ { "hue": "#000" },  {saturation: -200},
                    {gamma: 0.50} ] } ],
                center: new google.maps.LatLng(45.6231815, -72.9508469),
              mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            var infowindow = new google.maps.InfoWindow();

            var marker, i;

            for (i = 0; i < locations.length; i++) {

                marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map ,
                icon: 'eapp/assets/contact/img/marker.png'
                });


              google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                  infowindow.setContent(locations[i][0]);
                  infowindow.open(map, marker);
                }
              })(marker, i));
            }
        })(jQuery);
    </script>
