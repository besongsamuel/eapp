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
    return angular.isUndefined(value) || value === null || value == "undefined";
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
var eappApp = angular.module('eappApp', ['ngMaterial', 'vsGoogleAutocomplete', 'md.data.table', 'lfNgMdFileInput', 'ngMessages', 'ngSanitize', 'mdCountrySelect', 'ngNotificationsBar', 'ngRoute', 'ngAnimate', 'angularCountryState']);

eappApp.config(function($mdThemingProvider)
{
    $mdThemingProvider.definePalette('otiprixPalette', {
    '50': 'e0f2f1',
    '100': 'b2dfdb',
    '200': '80cbc4',
    '300': '4db6ac',
    '400': '26a69a',
    '500': '00b893',
    '600': '00897b',
    '700': '00796b',
    '800': '00695c',
    '900': '004d40',
    'A100': 'a7ffeb',
    'A200': '64ffda',
    'A400': '1de9b6',
    'A700': '00bfa5',
    'contrastDefaultColor': 'light',    // whether, by default, text (contrast)
                                        // on this palette should be dark or light

    'contrastDarkColors': ['50', '100', //hues which contrast should be 'dark' by default
     '200', '300', '400', 'A100'],
    'contrastLightColors': undefined    // could also specify this if default was 'dark'
  });

  $mdThemingProvider.theme('default')
    .primaryPalette('otiprixPalette');
});

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
    };
});

eappApp.directive("currentAddress", function()
{
    return {
        template : '<div class="col-sm-12 col-md-12 layout-padding" layout-align="center center"><p style="text-align : center;" ng-hide="isUserLogged">Résultats optimisés pour {{currentAddress}} | <a ng-href="{{changeLocationUrl}}" >Changer</a></p></div>'
    };
});


eappApp.component("quantityInput", 
{
    templateUrl : "quantityInput.html",
    controller : QuantityInputController,
    bindings : 
    {
        quantity : '=',
        onChange : '&'
    }
});

function QuantityInputController($scope)
{
    var ctrl = this;
    
    ctrl.add = function(ev)
    {
        $scope.quantity = $scope.quantity + 1; 
        
        $("#" + ctrl.id).attr('disabled', false);
        
        ctrl.quantity = $scope.quantity;
        
        ctrl.onChange({quantity : ctrl.quantity});
        
    };
    
    $scope.$watch("quantity", function()
    {
        ctrl.quantity = $scope.quantity;
        
        ctrl.onChange({quantity : ctrl.quantity});
    });
    
    ctrl.subtract = function(ev)
    {
        if($scope.quantity > 0)
        {
           $scope.quantity = $scope.quantity - 1; 
        }
        
        if(parseInt($scope.quantity) === 0)
        {
            $("#" + ctrl.id).attr('disabled', true);
        }
        
        ctrl.quantity = $scope.quantity;
        
        ctrl.onChange({quantity : ctrl.quantity});
    };
        
    ctrl.$onInit = function()
    {
        ctrl.id = "btn_" + Math.random().toString(36).substr(2, 9);;
        
        $("#btn-minus").attr("id", ctrl.id);
        
        $scope.quantity = ctrl.quantity;
    };
}

eappApp.component("resultFilter", 
{
    templateUrl : "resultFilter.html",
    controller : ResultFilterController,
    bindings : 
    {
        resultSet : '<',
        distance : '<',
        onSettingsChanged : '&',
        ready : '=',
        type : '@',
        onRefresh : '&',
        viewConfig : '=',
        onDistanceChanged : '&',
        isUserLogged : '<'
    }
});

function ResultFilterController($scope)
{
    var ctrl = this;
    
    $scope.trueVal = true;
    
    $scope.falseVal = false;
	
    $scope.viewConfig = {};
    
    ctrl.$onInit = function()
    {
        ctrl.settings = ctrl.resultSet;
        $scope.settings = ctrl.resultSet;
        $scope.distance = ctrl.distance;
        $scope.viewConfig = ctrl.viewConfig;
        $scope.isUserLogged = ctrl.isUserLogged;
                
        if(ctrl.type === 'CART')
        {
            $scope.viewConfig.viewAll = !$scope.viewConfig.optimizedCart;
            $scope.showAllResultsCaption = "Voir la liste originale";
            $scope.showOptimizedResultsCaption = "Voir la liste optimisée";
            $scope.isCart = true;
        }
        else
        {
            $scope.showAllResultsCaption = "Voir tout les produits";
            $scope.showOptimizedResultsCaption = "Voir produits optimisé";
        }
    };
    
    $scope.$watch("settings", function()
    {
        // Get selected items
        $scope.selectedItems = {};
        
        for(var x in $scope.settings)
        {
            for(var y in $scope.settings[x].values)
            {
                var type = $scope.settings[x].values[y].type;
                
                if($scope.settings[x].values[y].selected)
                {
                    if(angular.isNullOrUndefined($scope.selectedItems[$scope.settings[x].values[y].type]))
                    {
                        $scope.selectedItems[$scope.settings[x].values[y].type] = { name : $scope.settings[x].setting.caption, items : [] };
                    }
                    
                    $scope.selectedItems[$scope.settings[x].values[y].type].items.push($scope.settings[x].values[y]);
                }
            }
        }
    });
    
    $scope.getDisplayName = function(name)
    {
        if(name == "0" || name == "1")
        {
            if(name == "0")
            {
                return "Non";
            }
            else
            {
                return "Oui";
            }
        }
        else
        {
            return name;
        }
    };
    
    ctrl.$onChanges = function(changesObj)
    {
        if(!angular.isNullOrUndefined(changesObj.resultSet))
        {
            $scope.settings = changesObj.resultSet.currentValue;
        }
        
        if(!angular.isNullOrUndefined(changesObj.distance))
        {
            $scope.distance = changesObj.distance.currentValue;
        }
        
        if(!angular.isNullOrUndefined(changesObj.isUserLogged))
        {
            $scope.isUserLogged = changesObj.isUserLogged.currentValue;
        }
    };
    
    ctrl.distanceChanged = function()
    {
        ctrl.onDistanceChanged({distance : $scope.distance});
    };
    
    ctrl.change = function(item)
    {
        ctrl.onSettingsChanged({ item : item});
    };
    
    ctrl.refresh = function()
    {
        $scope.viewConfig.optimizedCart = !$scope.viewConfig.viewAll;
        
        ctrl.onRefresh({ viewConfig : $scope.viewConfig });
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
    
    $scope.getDisplayName = function(name)
    {
        if(name == "0" || name == "1")
        {
            if(name == "0")
            {
                return "Non";
            }
            else
            {
                return "Oui";
            }
        }
        else
        {
            return name;
        }
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

// Component to Select from user grocery list
eappApp.component("addToList", 
{
    template : "<a style='text-align : center;' href ng-click='$ctrl.selectListItem($event)'>{{$ctrl.caption}}</a>",
    controller : function($mdDialog, $scope)
    {
        var ctrl = this;
        
        ctrl.$onInit = function()
        {
            $scope.loggedUser = ctrl.loggedUser;
            $scope.product = ctrl.product;
        };
        
        ctrl.selectListItem = function(ev)
        {
            var scrollTop = $(document).scrollTop();
            
            $mdDialog.show({
                controller: DialogController,
                templateUrl: '/templates/selectUserGroceryLists.html',
                parent: angular.element(document.body),
                targetEvent: ev,
                locals : 
                {
                    grocery_lists : $scope.loggedUser.grocery_lists,
                    product : $scope.product
                },
                clickOutsideToClose:true,
                fullscreen: false
            })
            .then(function(answer) 
            {
                $(document).scrollTop(scrollTop);
            }, function() 
            {
                $(document).scrollTop(scrollTop);
            });
        };
        
        function DialogController($rootScope, $scope, $mdDialog, grocery_lists, product, eapp) 
        {
            $scope.grocery_lists = grocery_lists;
            
            $scope.refresh = function()
            {
              for(var i in $scope.grocery_lists)
                {
                    $scope.grocery_lists[i].selected = false;

                    for(var j in $scope.grocery_lists[i].products)
                    {
                        if(parseInt($scope.grocery_lists[i].products[j].id) === parseInt(product.id))
                        {
                            $scope.grocery_lists[i].selected = true;
                            continue;
                        }
                    }
                }  
            };
            
            $scope.refresh();
            
            $scope.product = product;
            
            $scope.creatingNew = false;
            
            $scope.name = "Nouvelle liste";
            
            $scope.hide = function() {
              $mdDialog.hide();
            };

            $scope.cancel = function() {
              $mdDialog.cancel();
            };

            $scope.answer = function(answer) {
              $mdDialog.hide(answer);
            };
            
            $scope.createList = function()
            {
                var createNewListPromise = eapp.createNewList($scope.name);
                
                createNewListPromise.then(function(response)
                {
                    if(response.data.success)
                    {
                        var newList = response.data.data;
                        newList.selected = false;
                        $rootScope.loggedUser.grocery_lists.push(newList);
                        $scope.grocery_lists = $rootScope.loggedUser.grocery_lists;
                        $scope.successMessage = response.data.message;
                    }
                    else
                    {
                        $scope.errorMessage = response.data.message;
                    }
                    
                    $scope.creatingNew = false;
                });
            };
            
            $scope.addToList = function(item)
            {
                $scope.successMessage = null;
                $scope.errorMessage =  null;
                if(item.selected)
                {
                    eapp.addProductToList($scope.product, item.id).then(function(response)
                    {
                        $rootScope.loggedUser.grocery_lists = response.data.grocery_lists;
                        $scope.grocery_lists = response.data.grocery_lists;
                        $scope.refresh();
                    });
                }
                else
                {
                    eapp.removeProductFromList($scope.product, item.id).then(function(response)
                    {
                        $rootScope.loggedUser.grocery_lists = response.data.grocery_lists;
                        $scope.grocery_lists = response.data.grocery_lists;
                        $scope.refresh();
                    });
                }
            };
        }
    },
    bindings : 
    {
        caption : '@',
        loggedUser : '<',
        product : '<'
    }
});

eappApp.component("imageUpload", 
{
    templateUrl : "templates/imageUpload.html",
    controller : function($scope)
    {
        var ctrl = this;
        
        $scope.removeImage = function()
        {
            $('.product-pic').attr('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==');
            $scope.hasImage = false;
            
            ctrl.onFileRemoved({});
        };
        
        ctrl.$onInit = function()
        {
            $scope.hasImage = false;
            
            if($('.product-pic'))
            {
                $('.product-pic').on('load', function()
                {
                    if($('.product-pic').attr('src') != 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==')
                    {
                        $scope.hasImage = true;
                    }
                    
                    $scope.$apply();
                });
            }
            
            $('.product-pic').attr('src', ctrl.image);
        };
        
        var readURL = function(input) 
        {
            if (input.files && input.files[0]) 
            {
                var reader = new FileReader();

                reader.onload = function (e) 
                {
                    $('.product-pic').attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
                
                ctrl.onFileSelected({ file : input.files[0]});
                
                $scope.hasImage = true;
                
                $scope.$apply();
                                
            }
        };
    
        $(".file-upload").on('change', function()
        {
            readURL(this);
        });
    
        $(".upload-button").on('click', function() 
        {
           $(".file-upload").click();
        });
    },
    bindings : 
    {
        caption : '@',
        name : '@',
        id : '@',
        image : '<',
        onFileSelected : '&',
        onFileRemoved : '&'
    }
});

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
    
    eappService.createPrompt = function(ev, caption, description, placeHolder, initialValue, yesLabel = 'Oui', noLabel = 'Non')
    {
        var prompt = $mdDialog.prompt()
            .title(caption)
            .textContent(description)
            .placeholder(placeHolder)
            .ariaLabel(placeHolder)
            .initialValue(initialValue)
            .targetEvent(ev)
            .required(true)
            .ok(yesLabel)
            .cancel(noLabel);
    
        return prompt;
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
    
    eappService.getOtiprixProduct = function(productId)
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_otiprix_product/").concat(productId.toString()), null);
    };
    
    eappService.getSiteUrl = function()
    {
        var siteName = window.location.hostname.toString();
        
        if(siteName == "localhost")
        {
            siteName = siteName.concat("/eapp/");
        }
        
        return location.protocol.concat("//", siteName, "/index.php/");
    };
    
    eappService.getBaseUrl = function()
    {
        var siteName = window.location.hostname.toString();
        
        if(siteName == "localhost")
        {
            siteName = siteName.concat("/eapp/");
        }
        
        return location.protocol.concat("//", siteName, "/");
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
    
    eappService.getProductsCount = function(filter)
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_products_count/", filter), null);
    };
    
    eappService.deleteProduct = function(product_id)
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/delete_product/", product_id), null);
    };
    
    eappService.deleteSubCategory = function(product_id)
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/delete_sub_category/", product_id), null);
    };
    
    eappService.getProducts = function(query)
    {
        var formData = new FormData();
        
        formData.append("query", JSON.stringify(query));
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/get_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getLatestProducts = function()
    {
        return $http.post(eappService.getSiteUrl().concat("cart/get_latest_products"), null);
    };
    
    eappService.getCategoryProducts = function(id, query, resultsFilter, viewConfig)
    {
        var formData = new FormData();
        formData.append("page", query.page);
        formData.append("limit", query.limit);
        formData.append("filter", query.filter);
        formData.append("order", query.order);
        // User's longitude
        formData.append("longitude", $rootScope.longitude);
        // user's latitude
        formData.append("latitude", $rootScope.latitude);
        formData.append("viewConfig", JSON.stringify(viewConfig));
        
        if(!angular.isNullOrUndefined(resultsFilter))
        {
            resultsFilter.categories = null;
        }
        
        formData.append("resultsFilter", JSON.stringify(resultsFilter));
        
        if(!angular.isNullOrUndefined(id))
        {
            formData.append("category_id", id);
        }
        formData.append("distance", $rootScope.getCartDistance());
        
        return $http.post(eappService.getSiteUrl().concat("/shop/get_store_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.getFlyerProducts = function(id, query, resultsFilter, viewConfig)
    {
        var formData = new FormData();
        formData.append("page", query.page);
        formData.append("limit", query.limit);
        formData.append("filter", query.filter);
        formData.append("order", query.order);
        formData.append("viewConfig", JSON.stringify(viewConfig));
        // User's longitude
        formData.append("longitude", $rootScope.longitude);
        // user's latitude
        formData.append("latitude", $rootScope.latitude);
        if(!angular.isNullOrUndefined(resultsFilter))
        {
            resultsFilter.stores = null;
        }
        formData.append("resultsFilter", JSON.stringify(resultsFilter));
        
        if(!angular.isNullOrUndefined(id))
        {
            formData.append("store_id", id);
        }
        formData.append("distance", $rootScope.getCartDistance());
        
        return $http.post(eappService.getSiteUrl().concat("/shop/get_store_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.getStoreProducts = function(query, resultsFilter, viewConfig)
    {
        var formData = new FormData();
        formData.append("page", query.page);
        formData.append("limit", query.limit);
        formData.append("filter", query.filter);
        formData.append("order", query.order);
        formData.append("viewConfig", JSON.stringify(viewConfig));
        formData.append("resultsFilter", JSON.stringify(resultsFilter));
        // User's longitude
        formData.append("longitude", $rootScope.longitude);
        // user's latitude
        formData.append("latitude", $rootScope.latitude);
        formData.append("distance", $rootScope.getCartDistance());
        
        return $http.post(eappService.getSiteUrl().concat("/shop/get_store_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.addProductToList = function(product, listID)
    {
        var formData = new FormData();
        formData.append("product_id", product.id);
        formData.append("list_id", listID);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/add_product_to_list"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.removeProductFromList = function(product, listID)
    {
        var formData = new FormData();
        formData.append("product_id", product.id);
        formData.append("list_id", listID);
        
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
    
    eappService.getSubCategories = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_subcategories"), null);  
    };
	
    eappService.getAdminSubCategories = function(query)
    {
        var formData = new FormData();
        formData.append("query", JSON.stringify(query));
        
        return $http.post(eappService.getSiteUrl().concat("eapp/get_admin_subcategories"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
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
    
    eappService.getProductsWithStoreProducts = function(filter)
    {
        var formData = new FormData();
        formData.append("filter", filter);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/get_products_with_store_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.createNewList = function(name)
    {
        var formData = new FormData();
        formData.append("name", name);
        return $http.post(eappService.getSiteUrl().concat("/account/create_new_list"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.deleteGroceryList = function(id)
    {
        var formData = new FormData();
        formData.append("id", id);
        
        return $http.post(eappService.getSiteUrl().concat("/account/delete_grocery_list"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.addDepartmentStore = function(departmentStore)
    {
        var formData = new FormData();
        formData.append("department_store", JSON.stringify(departmentStore));
        
        return $http.post(eappService.getSiteUrl().concat("/account/add_department_store"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.removeDepartmentStore = function(id)
    {
        var formData = new FormData();
        formData.append("id", JSON.stringify(id));
        
        return $http.post(eappService.getSiteUrl().concat("/account/remove_department_store"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.toggleIsNew = function()
    {
        return $http.post(eappService.getSiteUrl().concat("account/toggle_new"), null);
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




