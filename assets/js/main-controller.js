jQuery(document).ready(function($){
    
    
    
    $('.product-carousel').owlCarousel({
        loop:true,
        nav:true,
        autoplay:true,
        autoplayTimeout: 1000,
        autoplayHoverPause:true,
        margin:0,
        responsiveClass:true,
        
        responsive:{
            0:{
                items:1
            },
            600:{
                items:3
            },
            1000:{
                items:6
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
                items:1
            },
            600:{
                items:4
            },
            1000:{
                items:6
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

// Create eapp service to get and update our data
eappApp.factory('eapp', ['$http','$rootScope', function($http, $rootScope)
{
    var eappService = {};
    
    eappService.getProduct = function(productId)
    {
        return $http.post(eappService.getSiteUrl().concat("cart/get_product/").concat(productId.toString()), null);
    };
    
    eappService.getSiteUrl = function()
    {
        var siteName = window.location.hostname.toString();
        
        if(siteName == "localhost")
        {
            siteName = siteName.concat("/eapp/");
        }
        
        return "http://" + siteName + "index.php/";
    };
    
    eappService.getLatestProducts = function()
    {
        return $http.post(eappService.getSiteUrl().concat("cart/get_latest_products"), null);
    };

    return eappService;
}]);

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
  };
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

eappApp.controller('HomeController', ["$scope", "$http", function($scope, $http) 
{
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
}]);

eappApp.controller('AccountController', ["$scope", "$http", "$mdToast", "$q", "$rootScope", "$mdDialog", function($scope, $http, $mdToast, $q, $rootScope, $mdDialog) 
{
    
    $scope.querySearch = function(searchProductText)
    {
    	var q = $q.defer();
        var formData = new FormData();
        formData.append("name", searchProductText);

        $http.post( $scope.site_url.concat("/admin/searchProducts"), formData, {
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}
        }).then(function(response)
        {
            var array = $.map(response.data, function(value, index) {
                    return [value];
            });
            q.resolve( array );

        });

        return q.promise;
    };
	
    $rootScope.product_selected = function(item)
    {
        if(typeof item === 'undefined')
            return;
            
        $rootScope.selectedProduct = item;
    };
    
    $scope.getUserListStorePrices = function()
    {
        var stores = [];

        if($scope.loggedUser !== null && typeof $scope.loggedUser !== "undefined" && typeof $scope.loggedUser.grocery_list !== "undefined")
        {
            for(var i in $scope.loggedUser.grocery_list)
            {
                var product = $scope.loggedUser.grocery_list[i];
                
                for (var x in product.store)
                {
                    var productStore = product.store[x];
                    
                    var index = stores.map(function(e) { return e.id; }).indexOf(productStore.id);

                    if(index === -1) 
                    {
                        productStore.price = 0;
                        productStore.count = 0;
                        stores.push(productStore);
                        index = stores.length - 1;
                    }

                    if(typeof productStore.store_product !== "undefined")
                    {
                        stores[index].price += parseFloat(productStore.store_product.price);
                        stores[index].count++;
                    }
                }
                
            }
        }
	    
        // remove all stores with no items 
        var index = stores.map(function(e) { return e.count; }).indexOf(0);

        while(index > -1)
        {
            stores.splice(index, 1);
            var index = stores.map(function(e) { return e.count; }).indexOf(0);
        }    

        return stores;
    };
	
    $scope.flyers_count = function()
    {
        var count = 0;
        
        return count;
    };
    
    $scope.coupons_count = function()
    {
        var count = 0;
        
        return count;
    };
    
    $scope.clearMyList = function($event)
    {
		var confirmDialog = $rootScope.createConfirmDIalog($event, "Cela effacera tous les contenus de votre liste d'épicerie.");
		
		$mdDialog.show(confirmDialog).then(function() 
        {
            $http.post($rootScope.site_url.concat("/cart/destroy"), null).then(function(response)
            {
                $rootScope.myCategories = [];
				$scope.loggedUser.grocery_list = [];
				$rootScope.saveMyList();
				
            });

        });
        
    };
    
    $scope.getProductList = function()
    {
        var result = [];
        
        for(var index in $scope.myCategories)
        {
            for(var i in $scope.myCategories[index].products)
            {
                var data = 
                {
                        id : $scope.myCategories[index].products[i].id,
                        quantity : $scope.myCategories[index].products[i].quantity
                };    
                result.push(data);
            }
        }
        
        return result;
    };
    
    $rootScope.createConfirmDIalog = function(ev, contentText) 
    {
        // Appending dialog to document.body to cover sidenav in docs app
        var confirm = $mdDialog.confirm()
              .title('Êtes-vous sûr?')
              .textContent(contentText)
              .ariaLabel('Êtes-vous sûr?')
              .targetEvent(ev)
              .ok('Oui')
              .cancel('Non');
      
        return confirm;

        
    };
    
    $rootScope.optimizeMyList = function($event)
    {
        var confirmDialog = $rootScope.createConfirmDIalog($event, "Cela effacera tous les contenus de votre panier.");
        
        $mdDialog.show(confirmDialog).then(function() 
        {
            $http.post($rootScope.site_url.concat("/cart/destroy"), null).then(function(response)
            {
                $rootScope.cart = [];
                var items = [];
                
                // add cart contents from my list
                for(var index in $rootScope.myCategories)
                {
                    for(var i in $rootScope.myCategories[index].products)
                    {
                        var product = $rootScope.myCategories[index].products[i];
                        
                        var item = 
                        {
                            product_id : product.id
                        };
                        
                        items.push(item);
                    }
                }
                
                var formData = new FormData();
                formData.append("items", JSON.stringify(items));

                $http.post($rootScope.site_url.concat("/cart/insert_batch"), 
                formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(function(response)
                {
                    window.location = $rootScope.site_url.concat("/cart");
                });
            });

        });
        
        
    };
    
    $rootScope.removeProductFromList = function(product_id, $event, showDialog)
    {
        var confirmDialog = $rootScope.createConfirmDIalog ($event, "Ce produit sera supprimé de votre liste.");
        
        if(showDialog)
        {
            $mdDialog.show(confirmDialog).then(function() 
            {
                $rootScope.removeFromList(product_id);
                $rootScope.saveMyList();
                
            }, function() 
            {
                
            });
        }
        else
        {
            $rootScope.removeFromList(product_id);
            $rootScope.saveMyList();
        }
    };
    
    $rootScope.removeFromList = function(product_id)
    {
        for(var index in $rootScope.myCategories)
        {
            var pos = $rootScope.myCategories[index].products.map(function(e) { return e.id; }).indexOf(product_id);
            if(pos > -1)
            {
                $rootScope.myCategories[index].products.splice(pos, 1);

                if($rootScope.myCategories[index].products.length === 0)
                {
                    $rootScope.myCategories.splice(index, 1);
                }

                break;
            }
        }
    };
  
    $rootScope.saveMyList = function()
    {
        var formData = new FormData();
        formData.append("my_list", JSON.stringify($scope.getProductList()));
        // Send request to server to get optimized list 	
        $http.post( $scope.site_url.concat("/account/save_user_list"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            if(!response.data.success)
            {
                $scope.showSimpleToast("une erreur inattendue est apparue. Veuillez réessayer plus tard.", "mainmenu-area");
            }

            $scope.registering_user = false;
        });
    };
   
   $rootScope.showSimpleToast = function(message, parent_id) {
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
            person :  $scope.base_url + "/assets/icons/ic_person_white_24px.svg",
            flag :  $scope.base_url + "/assets/icons/ic_flag_white_24px.svg",
            place :  $scope.base_url + "/assets/icons/ic_place_white_24px.svg",
            phone :  $scope.base_url + "/assets/icons/ic_local_phone_white_24px.svg",
            email :  $scope.base_url + "/assets/icons/ic_email_white_24px.svg",
            lock :  $scope.base_url + "/assets/icons/ic_lock_white_24px.svg",
            favorite :  $scope.base_url + "/assets/icons/ic_favorite_white_24px.svg",
            delete :  $scope.base_url + "/assets/icons/ic_delete_white_24px.svg",
            add :  $scope.base_url + "/assets/icons/ic_add_circle_white_24px.svg",
            search :  $scope.base_url + "/assets/icons/ic_search_black_24px.svg",
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
    
    $scope.max_stores = 3;

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
                $scope.showSimpleToast("Vous ne pouvez pas sélectionner plus de "+$scope.max_stores+" magasins.", "select-store-box");
            }

        }
    };
    
    $scope.submit_favorite_stores = function()
    {
        $scope.listChangedSuccess = false;
        if($scope.selected_retailers.length < $scope.max_stores)
        {
            $scope.showSimpleToast("Vous devez sélectionner au moins "+$scope.max_stores+" magasins.", "select-store-box");
        }
        else
        {
            $scope.registering_user = true;
            var formData = new FormData();
            formData.append("selected_retailers", JSON.stringify($scope.selected_retailers));
            formData.append("email", $scope.registered_email);
            // Send request to server to get optimized list 	
            $http.post( $scope.site_url.concat("/account/submit_favorite_stores"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                if(response.data.success)
                {
                    // redirect to login. 
                    if(!$scope.isUserLogged)
                    {
                        window.sessionStorage.setItem("accountCreated", "Votre compte a été créé avec succès.");
                        window.location =  $scope.site_url.concat("/account/login");
                    }
                    else
                    {
                        $scope.listChangedSuccess = true;
                        $scope.listChangedSuccessMessage = "Votre liste de magasins a été modifiée.";
                    }
                    
                }
                else
                {
                    $scope.showSimpleToast("une erreur inattendue est apparue. Veuillez réessayer plus tard.", "select-store-box");
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

            $http.post(
                 $scope.site_url.concat("/account/registration"),formData,
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
                                window.location =  $scope.site_url.concat("/account/select_store");
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
            $http.post( $scope.site_url.concat("/account/perform_login"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                if(response.data.success)
                {
                    // redirect to home page. 
                    window.location =  $scope.site_url.concat("/" + response.data.redirect);
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
        $http.post( $scope.site_url.concat("/account/logout"), 
        null, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            // redirect to home page. 
            window.location =  $scope.site_url.concat("/home");
            
        });
    };
    
    $scope.saveProfile = function()
    {
        if(!$scope.userInfoForm.$valid)
        {
            return;
        }
        
        $scope.saveProfileError = false;
        $scope.saveProfileSucess = false;
        var formData = new FormData();
        formData.append("profile[firstname]", $scope.loggedUser.profile.firstname);
        formData.append("profile[lastname]", $scope.loggedUser.profile.lastname);
        formData.append("profile[country]", $scope.loggedUser.profile.country);
        formData.append("profile[state]", $scope.loggedUser.profile.state);
        formData.append("profile[city]", $scope.loggedUser.profile.city);
        formData.append("profile[address]", $scope.loggedUser.profile.address);
        formData.append("profile[postcode]", $scope.loggedUser.profile.postcode);
        formData.append("profile[phone1]", $scope.loggedUser.profile.phone1);
        formData.append("profile[phone2]", $scope.loggedUser.profile.phone2);
        
        $http.post( $scope.site_url.concat("/account/save_profile"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            if(response.data.success)
            {
                $scope.saveProfileSucess = true;
                
                $scope.loggedUser = response.data.user;
                
                $scope.saveProfileSuccessMessage = "Les informations de votre profil ont été modifiées.";
            }
            else
            {
                $scope.saveProfileError = true;
                $scope.saveProfileSuccessError = "Une erreur de serveur est survenue. Veuillez réessayer plus tard.";
            }
            
        });
    };
    
    $scope.changePassword = function()
    {
        if(!$scope.userSecurityForm.$valid)
        {
            return;
        }
        
        $scope.changePasswordError = false;
        $scope.changePasswordSuccess = false;
        var formData = new FormData();
        formData.append("old_password", $scope.old_password);
        formData.append("password", $scope.password);
        
        $http.post( $scope.site_url.concat("/account/change_password"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            if(response.data.success)
            {
                $scope.changePasswordSuccess = true;
                $scope.changePasswordSuccessMessage = response.data.message;
            }
            else
            {
                $scope.changePasswordError = true;
                $scope.changePasswordErrorMessage = response.data.message;
            }
        });
    };
    
    $scope.changeSecurityQuestion = function()
    {
        if(!$scope.securityQuestionForm.$valid)
        {
            return;
        }
        
        $scope.changeSecurityQuestionError = false;
        $scope.changeSecurityQuestionSuccess = false;
        var formData = new FormData();
        formData.append("security_question_answer", $scope.loggedUser.security_question_answer);
        formData.append("security_question_id", $scope.loggedUser.security_question_id);
        
        $http.post( $scope.site_url.concat("/account/change_security_qa"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            if(response.data.success)
            {
                $scope.changeSecurityQuestionSuccess = true;
                $scope.changeSecurityQuestionSuccessMessage = response.data.message;
            }
            else
            {
                $scope.changeSecurityQuestionError = true;
                $scope.changeSecurityQuestionErrorMessage = response.data.message;
            }
        });
    };
    
    $scope.sendVerificationCode = function()
    {
        $scope.phoneNumberError = null;

        var isValid = $("#phone").intlTelInput("isValidNumber");

        if(isValid)
        {
            var intlNumber = $("#phone").intlTelInput("getNumber");

            $.ajax({
                type: 'POST',
                url:   $scope.site_url.concat("/account/send_verification"),
                data: { number : intlNumber},
                success: function(response)
                {
                    if(Boolean(response.toString()))
                    {
                        var accountScope = angular.element($("#admin-container")).scope();

                        accountScope.$apply(function()
                        {
                            accountScope.enterVerificationNumber = false;
                        });

                    }

                },
                async:true
            });
        }
        else
        {
            var error = $("#phone").intlTelInput("getValidationError");

            switch(error)
            {
                case intlTelInputUtils.validationError.IS_POSSIBLE:
                    $scope.phoneNumberError = "";
                    break;
                case intlTelInputUtils.validationError.INVALID_COUNTRY_CODE:
                    $scope.phoneNumberError = "Le pays n'est pas valide";
                    break;
                case intlTelInputUtils.validationError.TOO_SHORT:
                    $scope.phoneNumberError = "Le numéro de téléphone entré est trop court";
                    break;
                case intlTelInputUtils.validationError.TOO_LONG:
                    $scope.phoneNumberError = "Le numéro de téléphone entré est trop long";
                    break;
                case intlTelInputUtils.validationError.NOT_A_NUMBER:
                    $scope.phoneNumberError = "Le numéro de téléphone entré n'est pas valide.";
                    break;
                default:
                    $scope.phoneNumberError = "Le numéro de téléphone entré n'est pas valide.";
                        break;
            }
        }


    };
    
    $scope.validateCode = function()
    {
        $scope.validateCodeMessage = null;

        $.ajax({
            type: 'POST',
            url:   $scope.site_url.concat("/account/validate_code"),
            data: { code : $scope.verificationCode},
            success: function(response)
            {
                var result = JSON.parse(response);
                
                if(result.success)
                {
                    
                    var accountScope = angular.element($("#admin-container")).scope();
                    accountScope.$apply(function()
                    {
                        $scope.loggedUser.phone_verified = 1;
                        accountScope.enterVerificationNumber = true;
                        accountScope.validateCodeMessage = result.message;
                    });

                }
                else
                {
                    var accountScope = angular.element($("#admin-container")).scope();
                    
                    accountScope.$apply(function()
                    {
                        accountScope.validateCodeMessage = result.message;
                    });
                    
                }

            },
            async:true
        });
        
    };
   
}]);


