jQuery(document).ready(function($){
    
    
    
    $('.product-carousel').owlCarousel({
        loop:true,
        nav:true,
        autoplay:true,
        autoplayTimeout: 1000,
        autoplayHoverPause:true,
        margin:0,
        responsiveClass:true,
        navText : ['Précédent', 'Suivant'],
        
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

angular.isNullOrUndefined = function(value)
{
    return angular.isUndefined(value) || value === null;
};

angular.getSearchParam = function(name, url)
{
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
};

// Define the `eapp Application` module
var eappApp = angular.module('eappApp', ['ngMaterial', 'md.data.table', 'lfNgMdFileInput', 'ngMessages', 'ngSanitize', 'mdCountrySelect', 'ngNotificationsBar', 'ngRoute', 'ngAnimate', 'angularCountryState']);

eappApp.filter('titlecase', function() {
    return function (input) {
        var smallWords = /^(a|an|and|as|at|but|by|en|for|if|in|nor|of|on|or|per|the|to|vs?\.?|via)$/i;

        input = input.toLowerCase();
        return input.replace(/[A-Za-z0-9\u00C0-\u00FF]+[^\s-]*/g, function(match, index, title) {
            if (index > 0 && index + match.length !== title.length &&
                match.search(smallWords) > -1 && title.charAt(index - 2) !== ":" &&
                (title.charAt(index + match.length) !== '-' || title.charAt(index - 1) === '-') &&
                title.charAt(index - 1).search(/[^\s-]/) < 0) {
                return match.toLowerCase();
            }

            if (match.substr(1).search(/[A-Z]|\../) > -1) {
                return match;
            }

            return match.charAt(0).toUpperCase() + match.substr(1);
        });
    }
});

eappApp.component("resultFilter", 
{
    templateUrl : "resultFilter.html",
    controller : ResultFilterController,
    bindings : 
    {
        resultSet : '<',
        onSettingsChanged : '&',
        ready : '='
    }
});

function ResultFilterController($scope)
{
    var ctrl = this;
    
    ctrl.$onInit = function()
    {
        ctrl.settings = ctrl.resultSet;
        $scope.settings = ctrl.resultSet;
    };
    
    $scope.$watch("settings", function()
    {
        // Get selected items
        $scope.selectedItems = {};
        
        for(var x in $scope.settings)
        {
            for(var y in $scope.settings[x])
            {
                var type = $scope.settings[x][y].type;
                        
                switch(type)
                {
                    case "ORIGIN":
                        type = 'Origin';
                        break;
                    case "STORE":
                        type = 'Magasin';
                        break;
                    case "CATEGORY":
                        type = 'Catégorie';
                        break;
                    case "BRAND":
                        type = 'Marque';
                        break;
                }
                
                if($scope.settings[x][y].selected)
                {
                    if(angular.isNullOrUndefined($scope.selectedItems[$scope.settings[x][y].type]))
                    {
                        
                        
                        $scope.selectedItems[$scope.settings[x][y].type] = { name : type, items : [] };
                    }
                    
                    $scope.selectedItems[$scope.settings[x][y].type].items.push($scope.settings[x][y]);
                }
            }
        }
        
    });
    
    ctrl.$onChanges = function(newSetting)
    {
        $scope.settings = newSetting.resultSet.currentValue;
    };
    
    ctrl.change = function(item)
    {
        ctrl.onSettingsChanged({ item : item});
    };
    
    $scope.removeFromFilter = function(item)
    {
        item.selected = false;
        ctrl.onSettingsChanged({ item : item});
    };
};

eappApp.component("settingsItem", 
{
    templateUrl : "settingsItem.html",
    controller : SettingsItemController,
    bindings : 
    {
        settingsObject : '<',
        name : '@',
        onChange : '&',
        ready : '='
    }
});

function SettingsItemController($scope)
{
    ctrl = this;
    
    ctrl.getData = function(allData)
    {
        var data = [];
        
        for(var x in allData)
        {
            //if(!allData[x].selected)
            {
                data.push(allData[x]);
            }
        }
        
        return data;
    };
    
    ctrl.$onInit = function()
    {
        ctrl.data = ctrl.getData(ctrl.settingsObject);
        
        $scope.data = ctrl.getData(ctrl.settingsObject);
        
        $scope.moreLessLabel = "Plus";
    };
    
    ctrl.$onChanges = function(newData)
    {
        $scope.data = ctrl.getData(newData.settingsObject.currentValue);
        ctrl.data = ctrl.getData(newData.settingsObject.currentValue);
    };
    
    ctrl.change = function(item)
    {
        ctrl.onChange({item : item});
    };
    
    $scope.showHideDetails = function(event)
    {
        
        if($scope.moreLessLabel == "Plus")
        {
            $scope.moreLessLabel = "Moins";
        }
        else
        {
            $scope.moreLessLabel = "Plus";
        }
    };
}

// configure our routes
eappApp.config(function($routeProvider) 
{
    $routeProvider

    // route for the home page
    .when('/cart', {
        templateUrl : 'pages/cart.html',
        controller  : 'CartController'
    });

});

// Create eapp service to get and update our data
eappApp.factory('eapp', ['$http','$rootScope', '$mdDialog', function($http, $rootScope, $mdDialog)
{
    var eappService = {};
    
    eappService.showAlert = function(ev, title, message) 
    {
        
        var scrollTop = $(document).scrollTop();
        // Appending dialog to document.body to cover sidenav in docs app
        // Modal dialogs should fully cover application
        // to prevent interaction outside of dialog
        $mdDialog.show(
                
                $mdDialog.alert()
                .title(title)
                .textContent(message)
                .ok('Ok')
                .targetEvent(ev)
        )
        .finally(function()
        {
            $(document).scrollTop(scrollTop);
        });
    };
    
    eappService.scrollTo = function(divID)
    {
        $('html, body').animate({
            scrollTop: $("#" + divID).offset().top
        }, 2000);
    };
    
    eappService.getUrlParameter = function(sParam) 
    {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };
    
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
        
        return "http://" + siteName + "/index.php/";
    };
    
    eappService.getBaseUrl = function()
    {
        var siteName = window.location.hostname.toString();
        
        if(siteName == "localhost")
        {
            siteName = siteName.concat("/eapp/");
        }
        
        return "http://" + siteName + "/";
    };
    
    
    eappService.siteUrl = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/site_url"), null);
    };
    
    eappService.baseUrl = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/base_url"), null);
    };
    
    eappService.getRetailers = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_retailers"), null);
    };
    
    eappService.getBrands = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_brands"), null);
    };
    
    eappService.getLatestProducts = function()
    {
        return $http.post(eappService.getSiteUrl().concat("cart/get_latest_products"), null);
    };
    
    eappService.getCategoryProducts = function(id, query, resultsFilter)
    {
        var formData = new FormData();
        formData.append("page", query.page);
        formData.append("limit", query.limit);
        formData.append("filter", query.filter);
        formData.append("order", query.order);
        
        if(!angular.isNullOrUndefined(resultsFilter))
        {
            resultsFilter.categories = null;
        }
        
        formData.append("resultsFilter", JSON.stringify(resultsFilter));
        
        if(!angular.isNullOrUndefined(id))
        {
            formData.append("category_id", id);
        }
        
        return $http.post(eappService.getSiteUrl().concat("/shop/get_store_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.getFlyerProducts = function(id, query, resultsFilter)
    {
        var formData = new FormData();
        formData.append("page", query.page);
        formData.append("limit", query.limit);
        formData.append("filter", query.filter);
        formData.append("order", query.order);
        if(!angular.isNullOrUndefined(resultsFilter))
        {
            resultsFilter.stores = null;
        }
        formData.append("resultsFilter", JSON.stringify(resultsFilter));
        
        if(!angular.isNullOrUndefined(id))
        {
            formData.append("store_id", id);
        }
        
        return $http.post(eappService.getSiteUrl().concat("/shop/get_store_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.getStoreProducts = function(query, resultsFilter)
    {
        var formData = new FormData();
        formData.append("page", query.page);
        formData.append("limit", query.limit);
        formData.append("filter", query.filter);
        formData.append("order", query.order);
        formData.append("resultsFilter", JSON.stringify(resultsFilter));
        
        return $http.post(eappService.getSiteUrl().concat("/shop/get_store_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.addProductToList = function(product)
    {
        var formData = new FormData();
        formData.append("product_id", product.id);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/add_product_to_list"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.removeProductFromList = function(product)
    {
        var formData = new FormData();
        formData.append("product_id", product.id);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/remove_product_from_list"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getCart = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_cart_contents"), null);
    };
    
    eappService.removeFromCart = function(rowid)
    {
        var formData = new FormData();
        formData.append("rowid", rowid);
        
        return $http.post(eappService.getSiteUrl().concat("cart/remove"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.updateCart = function(item)
    {
        var formData = new FormData();
        formData.append("item", JSON.stringify(item));
        
        return $http.post(eappService.getSiteUrl().concat("cart/update"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.addToCart = function(productID, storeProductID, longitude, latitude, quantity)
    {
        var formData = new FormData();
        formData.append("product_id", productID);
        formData.append("store_product_id", storeProductID);
        formData.append("longitude", longitude);
        formData.append("latitude", latitude);
        formData.append("quantity", quantity);
        
        return $http.post(eappService.getSiteUrl().concat("cart/insert"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.clearCart = function()
    {
        return $http.post(eappService.getSiteUrl().concat("cart/destroy"), null);
    };
    
    eappService.changeDistance = function(distToChange, newValue)
    {
        var formData = new FormData();
        formData.append("distance_to_change", distToChange);
        formData.append("value", newValue);
        return $http.post(eappService.getSiteUrl().concat("eapp/change_distance"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getCategories = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_categories"), null);  
    };
    
    eappService.getCloseRetailers = function(distance)
    {
        var formData = new FormData();
        formData.append("distance", distance);
        formData.append("longitude", parseFloat($rootScope.longitude));
        formData.append("latitude", parseFloat($rootScope.latitude));
        
        return $http.post(eappService.getSiteUrl().concat("eapp/get_close_retailers"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.recordHit = function(tableName, id)
    {
        var formData = new FormData();
        formData.append("table_name", tableName);
        formData.append("id", id);
        return $http.post(eappService.getSiteUrl().concat("admin/hit"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.saveFavoriteStores = function(favoriteStores)
    {
        var formData = new FormData();
        formData.append("selected_retailers", favoriteStores);
        return $http.post(eappService.getSiteUrl().concat("account/save_favorite_stores"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getFavoriteStores = function()
    {
        return $http.post(eappService.getSiteUrl().concat("account/get_favorite_stores"), null);
    };
    
    eappService.updateUserProfile = function(userObject)
    {
        var formData = new FormData();
        formData.append("profile[firstname]", userObject.profile.firstname);
        formData.append("profile[lastname]", userObject.profile.lastname);
        formData.append("profile[country]", userObject.profile.country);
        formData.append("profile[state]", userObject.profile.state);
        formData.append("profile[city]", userObject.profile.city);
        formData.append("profile[address]", userObject.profile.address);
        formData.append("profile[postcode]", userObject.profile.postcode);
        
        return $http.post(eappService.getSiteUrl().concat("account/save_profile"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.registerUser = function(user)
    {
        // Create form data
        var formData = new FormData();
        formData.append("account[email]", user.email);
        formData.append("account[password]", user.password);
        formData.append("account[security_question_id]", user.security_question_id);
        formData.append("account[security_question_answer]", user.security_question_answer);

        formData.append("profile[firstname]", user.firstname);
        formData.append("profile[lastname]", user.lastname);
        formData.append("profile[country]", user.country);
        formData.append("profile[state]", user.state);
        formData.append("profile[city]", user.city);
        formData.append("profile[address]", user.address);
        formData.append("profile[postcode]", user.postcode);
        
        return $http.post(eappService.getSiteUrl().concat("account/registration"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.getSecurityQuestions = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_security_questions"), null);
    };
    
    eappService.getLatestProducts = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_latest_products"), null);
    };
    
    eappService.sendPasswordReset = function(email)
    {
        var formData = new FormData();
        formData.append("email", email);
        
        return $http.post(eappService.getSiteUrl().concat("account/send_password_reset"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.resetPassword = function(password, reset_token)
    {
        var formData = new FormData();
        formData.append("password", password);
        formData.append("reset_token", reset_token);
        return $http.post(eappService.getSiteUrl().concat("account/modify_password"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.sendVerification = function(phone_number)
    {
        var formData = new FormData();
        formData.append("number", phone_number);
        
        return $http.post(eappService.getSiteUrl().concat("/account/send_verification"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.validateCode = function(code)
    {
        var formData = new FormData();
        formData.append("code", code);
        
        return $http.post(eappService.getSiteUrl().concat("/account/validate_code"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.viewProduct = function($scope, product_id, ev)
    {
        // Get the latest products
        var promise = eappService.getProduct(product_id);
    
        promise.then(function(response)
        {
            $scope.storeProduct = response.data;
            
            $scope.scrollTop = $(document).scrollTop();
            
            // Open dialog
            $mdDialog.show({
                controller: ViewProductController,
                templateUrl:  eappService.getBaseUrl() + 'assets/templates/otiprix-product.html',
                parent: angular.element(document.body),
                targetEvent: ev,
                clickOutsideToClose:true,
                disableParentScroll : true,
                preserveScope:true,
                scope : $scope,
                fullscreen: true,
                onRemoving : function()
                {
                    // Restore scroll
                    $(document).scrollTop($scope.scrollTop);
                },
                onShowing : function()
                {
                    $scope.RelatedProductsAvailable = !angular.isNullOrUndefined($scope.storeProduct.similar_products) && $scope.storeProduct.similar_products.length > 0;
                }
            })
            .then(function(answer) {

            }, function() {

            });
        },
        function(errorResponse)
        {
            $scope.storeProduct = null;
        });
    };
    
    function ViewProductController($scope, $mdDialog)
    {
        
        $scope.close = function() 
        {
            $mdDialog.cancel();
        };
    }
    
    eappService.getUnitCompareUnits = function(id)
    {
        var formData = new FormData();
        formData.append("id", id);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/get_unit_compareunits"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getCompareUnitUnits = function(id)
    {
        var formData = new FormData();
        formData.append("id", id);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/get_compareunit_units"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getUnits = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_units"), null);
    };
    
    eappService.getCompareUnits = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_compareunits"), null);
    };
    
    eappService.getUnitCompareUnit = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_unit_compareunit"), null);
    };
    
    eappService.getProductUnitCompareUnit = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_product_unit_compareunit"), null);
    };
    
    eappService.getUserOptimizations = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_user_optimizations"), null);
    };
    
    eappService.getStoreProduct = function(spID)
    {
        var formData = new FormData();
        formData.append("id", spID);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/get_store_product"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.subscribe = function(email)
    {
        var formData = new FormData();
        formData.append("email", email);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/subscribe"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.unsubscribe = function(token)
    {
        var formData = new FormData();
        formData.append("token", token);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/unsubscribe"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getUnsubscribeEmailFromToken = function(token)
    {
        var formData = new FormData();
        formData.append("token", token);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/get_email_from_unsubscribe_token"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
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

eappApp.factory('Form', [ '$http', 'notifications', 'eapp', function($http, notifications, eapp) 
{
    this.postForm = function (formData, url, redirect_url, ev) 
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
                
                eapp.showAlert(ev, "Success", response.data.message);
            }
            else
            {
                var message = response.data.message + "\n\n";
                
                for(var x in response.data.errors)
                {
                    message += "\n" + response.data.errors[x];
                }
                
                message += '\n';
                
                eapp.showAlert(ev, "Error", message);
            }
            
        }, 
        function errorCallback(response) 
        {
            eapp.showAlert(ev, "Error", "An unknown error occured. ");
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
  
}]);

eappApp.controller('HomeController', ["$scope", "$http", function($scope, $http) 
{
    
}]);




