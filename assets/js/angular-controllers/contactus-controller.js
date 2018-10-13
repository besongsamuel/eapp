/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("ContactUsController", ["$rootScope", "$scope", "eapp", "$http", function($rootScope, $scope, eapp, $http) 
{
    $scope.officeLongitude = -75.697530;
    $scope.officeLatitude = 45.495940;
    $scope.officeStreet = "76 Rue Jean-Perrin";
    $scope.officeCity = "Gatineau, QC";
    $scope.officePostcode = "J8V 2R2";
    $scope.officeContact = "infos@otiprix.com";
    
    $scope.Init = function()
    {
        $rootScope.isContact = true;
        
        $scope.InitializeMap();
    };
    
    $scope.InitializeMap = function()
    {
        var locations = [
        ['<div class="infobox"><h3 class="title"><a href="about-1.html">Notre Bureau</a></h3><span>76 Rue Jean-Perrin, </span><br>Gatineau, J8V 2R2</span><br>  </p></div></div></div>', 45.495940, -75.697530, 2]
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
            icon: $scope.base_url.concat('/assets/img/marker.png')
            });


          google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
              infowindow.setContent(locations[i][0]);
              infowindow.open(map, marker);
            };
          })(marker, i));
        }
        
    };
    
    $scope.contact = 
    {
        name : "",
        email : "",
        subject : "",
        comment : ""
    };
    
    $scope.contactus = function()
    {
        if($scope.contactusForm.$valid)
        {
            var formData = new FormData();
            formData.append("name", $scope.contact.name);
            formData.append("email", $scope.contact.email);
            formData.append("subject", $scope.contact.subject);
            formData.append("comment", $scope.contact.comment);

            $http.post( $scope.site_url.concat("/home/contactus"), formData, {
                    transformRequest: angular.identity,
                    headers: {'Content-Type': undefined}
            }).then(function(response)
            {
                if(response.data.result)
                {
                    $scope.message = "Votre message a bien été envoyé.";
                    $scope.contact = 
                    {
                        name : "",
                        email : "",
                        subject : "",
                        comment : ""
                    };
                    $scope.contactusForm.$setPristine();
                    $scope.contactusForm.$setValidity();
                    $scope.contactusForm.$setUntouched();
                }
                else
                {
                    $scope.errorMessage = "Une erreur de serveur inattendue s'est produite. Veuillez réessayer plus tard.";
                }

            });
        }        
    };
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
}]);

