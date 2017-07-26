jQuery(document).ready(function($){
    
    // jQuery sticky Menu
    $(".mainmenu-area").sticky({topSpacing:0});
    
    $('.product-carousel').owlCarousel({
        loop:true,
        nav:true,
        margin:20,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
            },
            600:{
                items:3,
            },
            1000:{
                items:5,
            }
        }
    });  
    
    $('.related-products-carousel').owlCarousel({
        loop:true,
        nav:true,
        margin:20,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
            },
            600:{
                items:2,
            },
            1000:{
                items:2,
            },
            1200:{
                items:3,
            }
        }
    });  
    
    $('.brand-list').owlCarousel({
        loop:true,
        nav:true,
        margin:20,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
            },
            600:{
                items:3,
            },
            1000:{
                items:4,
            }
        }
    });    
       
});

function convert_to_string_date(date)
{
    return date.getFullYear().toString() + "-" + date.getMonth().toString() + "-" + date.getDate().toString();
}

// Define the `eapp Application` module
var eappApp = angular.module('eappApp', ['ngMaterial', 'md.data.table', 'lfNgMdFileInput', 'ngMessages', 'ngSanitize', 'mdCountrySelect', 'ngNotificationsBar', 'ngAnimate', 'angularCountryState']);

eappApp.directive('equals', function() {
  return {
    restrict: 'A', // only activate on element attribute
    require: '?ngModel', // get a hold of NgModelController
    link: function(scope, elem, attrs, ngModel) {
      if(!ngModel) return; // do nothing if no ng-model

      // watch own value and re-validate on change
      scope.$watch(attrs.ngModel, function() {
        validate();
      });

      // observe the other value and re-validate on change
      attrs.$observe('equals', function (val) {
        validate();
      });

      var validate = function() {
        // values
        var val1 = ngModel.$viewValue;
        var val2 = attrs.equals;

        // set validity
        ngModel.$setValidity('equals', ! val1 || ! val2 || val1 === val2);
      };
    }
  }
});

eappApp.filter('trustUrl', function ($sce) {
    return function(url) {
      var trustedurl =  $sce.trustAsResourceUrl(url);
      
      return trustedurl;
    };
});

eappApp.factory('Form', [ '$http', 'notifications', function($http, notifications) 
{
    this.postForm = function (formData, url, redirect_url) 
    {       
        $http({
            url: url,
            method: 'POST',
            data: formData,
            //assign content-type as undefined, the browser
            //will assign the correct boundary for us
            headers: { 'Content-Type': undefined},
            //prevents serializing payload.  don't do it.
            transformRequest: angular.identity
        }).
        then(
        function successCallback(response) 
        {
            
            if(response.data.success)
            {
                if(redirect_url != null)
                {
                    window.location.href = redirect_url;
                }
                
                notifications.showSuccess(response.data.message);
            }
            else
            {
                notifications.showError(response.data.message);
            }
            
        }, 
        function errorCallback(response) 
        {
            notifications.showError("An unexpected server error occured. Please try again later. ");
        });
    };
    
    return this;
}]);

eappApp.controller('ProductsController', ['$scope','$rootScope', function($scope, $rootScope) {
  
    /**
     * This are the products displayed on the home page. The most recent products.
     */
    $scope.products = [];
    
    /**
     * Products currently in the cart
     */
    $scope.cart_items = [];
    
    $scope.add_to_cart = function(product_id)
    {
        
    };
    
    $scope.remove_to_cart = function(product_id)
    {
        
    };
    
    $scope.cart_total = function()
    {
        
    };
  
}]);

eappApp.controller('HomeController', ["$scope", function($scope) 
{
  
}]);

eappApp.controller('AccountController', ["$scope", "$http", "$mdToast", function($scope, $http, $mdToast) 
{
   
   $scope.showSimpleToast = function(message, parent_id) {
        $mdToast.show(
          $mdToast.simple()
            .textContent(message)
            .position("left bottom")
            .hideDelay(3000)
            .parent(document.getElementById(parent_id))
        );
    };
    
    $scope.load_icons = function()
    {
        $scope.icons = 
        {
            person : "http://" + $scope.base_url + "/assets/icons/ic_person_white_24px.svg",
            flag : "http://" + $scope.base_url + "/assets/icons/ic_flag_white_24px.svg",
            place : "http://" + $scope.base_url + "/assets/icons/ic_place_white_24px.svg",
            phone : "http://" + $scope.base_url + "/assets/icons/ic_local_phone_white_24px.svg",
            email : "http://" + $scope.base_url + "/assets/icons/ic_email_white_24px.svg",
            lock : "http://" + $scope.base_url + "/assets/icons/ic_lock_white_24px.svg"
        };
    };
    
   $scope.registering_user = false;
   
   $scope.securityQuestions = 
    [
        "Choisissez une question",
        "La destination de votre premier voyage",
        "Quel était l'héros de votre enfance",
        "Le prénom de votre meilleur ami",
        "Le prénom de votre premier amour",
        "Le deuxième prenom de votre plus jeune enfant"
    ];
    
    $scope.user = 
    {
        email : '',
        password : '',
        security_question_id : 1,
        security_question_answer : '',
        firstname : '',
        lastname : '',
        country : 'Canada',
        state : 'Quebec',
        city : '',
        address : '',
        postcode : '',
        phone1 : '',
        phone2 : '',
	rememberme : false

    };
	
    $scope.message = null;
    
    $scope.retailers = [];
        
    $scope.selected_retailers = [];
    
    $scope.max_stores = 5;

    $scope.select_retailer = function($event)
    {
        var element = $event.target;
        
        if($(element).hasClass( "check" ))
        {
            var index = $scope.selected_retailers.indexOf(parseInt(element.id));
            if (index > -1) 
            {
                $scope.selected_retailers.splice(index, 1);
            }
            $(element).toggleClass("check");
        }
        else
        {
            if($scope.selected_retailers.length < $scope.max_stores)
            {
                $scope.selected_retailers.push(parseInt(element.id));
                $(element).toggleClass("check");
            }
            else
            {
                $scope.showSimpleToast("Vous ne pouvez pas sélectionner plus de "+$scope.max_stores+" magasins.", "signupform");
            }

        }
    };
    
    $scope.submit_favorite_stores = function()
    {
        if($scope.selected_retailers.length < $scope.max_stores)
        {
            $scope.showSimpleToast("Vous devez sélectionner au moins "+$scope.max_stores+" magasins.", "signupform");
        }
        else
        {
            $scope.registering_user = true;
            var formData = new FormData();
            formData.append("selected_retailers", JSON.stringify($scope.selected_retailers));
            formData.append("email", $scope.registered_email);
            // Send request to server to get optimized list 	
            $http.post("http://"+ $scope.site_url.concat("/account/submit_favorite_stores"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                if(response.data.success)
                {
                    // redirect to login. 
                    window.sessionStorage.setItem("accountCreated", "Votre compte a été créé avec succès.");
                    window.location = "http://" + $scope.site_url.concat("/account/login");
                }
                else
                {
                    $scope.showSimpleToast("une erreur inattendue est apparue. Veuillez réessayer plus tard.", "signupform");
                }
                
                $scope.registering_user = false;
            });
        }
    };
     
    $scope.register = function()
    {
        
        $scope.message = null;
        
        if($scope.signupForm.$valid)
        {
            
            // Create form data
            var formData = new FormData();
            formData.append("account[email]", $scope.user.email);
            formData.append("account[password]", $scope.user.password);
            formData.append("account[security_question_id]", $scope.user.security_question_id);
            formData.append("account[security_question_answer]", $scope.user.security_question_answer);

            formData.append("profile[firstname]", $scope.user.firstname);
            formData.append("profile[lastname]", $scope.user.lastname);
            formData.append("profile[country]", $scope.user.country);
            formData.append("profile[state]", $scope.user.state);
            formData.append("profile[city]", $scope.user.city);
            formData.append("profile[address]", $scope.user.address);
            formData.append("profile[postcode]", $scope.user.postcode);
            formData.append("profile[phone1]", $scope.user.phone1);
            formData.append("profile[phone2]", $scope.user.phone2);
	    

            $http.post(
                "http://" + $scope.site_url.concat("/account/registration"),formData,
                {
                        transformRequest: angular.identity,
                        headers: {'Content-Type': undefined}
                }).then
                (
                        function(result)
                        {
                            
                            if(result.data.success)
                            {
                                window.sessionStorage.setItem("registered_email", $scope.user.email);
                                // Redirect to select store page.
                                window.location = "http://" + $scope.site_url.concat("/account/select_store");
                            }

                            if(!result.data.success)
                            {
                                $scope.message = result.data.message;
                                document.getElementById("error_message").scrollIntoView();
                            }
                        },
                        function(error)
                        {
                            if(!error.data.success)
                            {
                                $scope.message = error.data.message;
                            }
                        }
                );
        }
            
    };
    
    $scope.login = function()
    {
        if($scope.loginForm.$valid)
        {
            var formData = new FormData();
            formData.append("email", $scope.user.email);
            formData.append("password", $scope.user.password);
            formData.append("rememberme", $scope.user.rememberme ? 1 : 0);
            // Send request to server to get optimized list 	
            $http.post("http://"+ $scope.site_url.concat("/account/perform_login"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                if(response.data.success)
                {
                    // redirect to home page. 
                    window.location = "http://" + $scope.site_url.concat("/" + response.data.redirect);
                }
                else
                {
                    $scope.message = response.data.message;
                }
            });
        }
    };
    
    $scope.logout = function()
    {
        // Send request to server to get optimized list 	
        $http.post("http://"+ $scope.site_url.concat("/account/logout"), 
        null, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            // redirect to home page. 
            window.location = "http://" + $scope.site_url.concat("/home");
            
        });
    };
   
}]);


