// Set the date we're counting down to
var countDownDate = new Date("Jan 5, 2018 15:37:25").getTime();

// Update the count down every 1 second
var x = setInterval(function() {

  // Get todays date and time
  var now = new Date().getTime();

  // Find the distance between now an the count down date
  var distance = countDownDate - now;

  // Time calculations for days, hours, minutes and seconds
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

  // Display the result in an element with id="demo"
  document.getElementById("time").innerHTML = days + "d " + hours + "h "
  + minutes + "m " + seconds + "s ";

  // If the count down is finished, write some text 
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("time").innerHTML = "EXPIRED";
  }
}, 1000);

var pageApp = angular.module('pageApp', ['ngMaterial']);

pageApp.controller('AccountController', function($scope, $http, appService) 
{
    $scope.login = function()
    {
        if($scope.loginForm.$valid)
        {
            var formData = new FormData();
            formData.append("email", $scope.user.email);
            formData.append("password", $scope.user.password);
            formData.append("rememberme", $scope.user.rememberme ? 1 : 0);
            
            
            // Send request to server to get optimized list 	
            $http.post( appService.siteUrl.concat("/account/perform_login"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                if(response.data.success)
                {
                    // redirect to home page. 
                    window.location =  appService.siteUrl.concat("/" + response.data.redirect);
                }
                else
                {
                    $scope.message = response.data.message;
                }
            });
        }
    };
});


