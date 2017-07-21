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
var eappApp = angular.module('eappApp', ['ngMaterial', 'md.data.table', 'lfNgMdFileInput', 'mdCountrySelect', 'ngNotificationsBar', 'ngAnimate', 'angularCountryState']);

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

eappApp.controller('AccountController', ["$scope", "$http", function($scope, "$http") 
{
   $scope.showHints = true;
   
   $scope.securityQuestions = 
    [
        "Choisissez une question",
        "La destination de votre premier voyage",
        "Quel était l'héros de votre enfance",
        "Le prénom de votre meilleur ami",
        "Le prénom de votre premier amour",
        "Le deuxième prenom de votre plus jeune enfant"
    ];
	
	$scope.register = function()
	{
		if(signupForm.$valid)
		{
			// Create form data
			var formData = new FormData();
			formData.append("account[email]", $scope.user.email);
			formData.append("account[password]", $scope.user.email);
			formData.append("account[security_question_id]", $scope.user.security_question_id);
			formData.append("account[security_question_answer]", $scope.user.security_question_answer);
			
			formData.append("profile[firstname]", $scope.user.firstname);
			formData.append("profile[lastname]", $scope.user.lastname);
			formData.append("profile[country]", $scope.user.country);
			formData.append("profile[state]", $scope.user.state);
			formData.append("profile[city]", $scope.user.city);
			formData.append("profile[address]", $scope.user.address);
			formData.append("profile[postcode]", $scope.user.postcode);
			formData.append("profile[phone1]", $scope.user.postcode);
			formData.append("profile[phone2]", $scope.user.postcode);
			
			$http.post
			(
				"http://" + $scope.site_url.concat("/account/registration"),
				{
					transformRequest: angular.identity,
					headers: {'Content-Type': undefined}
				}.then
				(
					function(result)
					{
						if(result.data.success)
						{
							// Redirect to confirmation page. 
						}
						
						if(!result.data.success)
						{
						
						}
					},
					function(error)
					{
					
					},
				)
			);
		}
		
		if(!signupForm.$valid)
		{
			// This will cause the form to show errors
			$scope.showHinds = false;
		}
	};
   
}]);


