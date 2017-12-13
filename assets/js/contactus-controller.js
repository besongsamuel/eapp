/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("ContactUsController", ["$rootScope", "$scope", "eapp", function($rootScope, $scope, eapp) 
{
    $scope.officeLongitude = -72.9508469;
    $scope.officeLatitude = 45.6231815;
    $scope.officeStreet = "550 Avenue Saint-Dominique";
    $scope.officeCity = "Saint-Hyacinthe";
    $scope.officePostcode = "J2S 5M6";
    $scope.officeContact = "infos@otiprix.com";
    
    $scope.Init = function()
    {
        $rootScope.isContact = true;
        
        $scope.InitializeMap();
    };
    
    $scope.InitializeMap = function()
    {
        var locations = [
        ['<div class="infobox"><h3 class="title"><a href="about-1.html">Notre Bureau</a></h3><span>550 Avenue Saint-Dominique, </span><br>Saint-Hyacinthe, J2S 5M6</span><br>  </p></div></div></div>', 45.6231815, -72.9508469, 2]
        ];
        
        // Create the map
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 15,
            scrollwheel: true,
            navigationControl: true,
            mapTypeControl: true,
            scaleControl: false,
            draggable: true,
            styles: [ { "stylers": [ { "hue": "#000" },  {saturation: -200},
                {gamma: 0.50} ] } ],
            center: new google.maps.LatLng($scope.officeLatitude, $scope.officeLongitude),
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
            };
          })(marker, i));
        }
        
    };
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
}]);
