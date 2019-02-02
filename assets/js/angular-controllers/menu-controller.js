/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("MenuController", function($scope, appService, cart, $rootScope, $mdDialog, eapp, $sce, profileData) 
{
    var ctrl = this;
    
    $rootScope.isUserLogged = false;
    
    appService.ready.then(function()
    {
        
        $rootScope.isRegularUser = appService.isRegularUser;
        
        $rootScope.loggedUser = appService.loggedUser;
        
        $rootScope.isUserLogged = appService.isUserLogged;
        
        $rootScope.site_url = appService.siteUrl;
        
        $rootScope.base_url = appService.baseUrl;
        
        $scope.selectedMenu = 0;
        
        profileData.ready.then(function()
        {
            $scope.optimizationDistance = profileData.instance.optimizationDistance;
        });
        
        if(appService.isUserLogged)
        {
            $scope.postcode = appService.loggedUser.profile.postcode;
        }
        else
        {
            $scope.postcode = appService.postcode;
        }
        
        switch(appService.controller.toString())
        {
            case "home":
                switch(appService.method.toString())
                {
                    case "contact":
                        $scope.selectedMenu = 3;
                        break;
                    case "about":
                        $scope.selectedMenu = 4;
                        break;
                    default:
                        $scope.selectedMenu = 0;
                        break;
                    
                }
                break;
            case "account":
                
                switch(appService.method.toString())
                {
                    case "my_grocery_list":
                        $scope.selectedMenu = 1;
                        break;
                    case "login":
                        $scope.selectedMenu = 5;
                        break;
                    case "register":
                        $scope.selectedMenu = 6;
                        break;
                    default:
                        $scope.selectedMenu = 5;
                        break;
                    
                }
                
                break;
            case "shop":
                switch(appService.method.toString())
                {
                    case "select_flyer_store":
                        $scope.selectedMenu = 1;
                        break;
                    case "categories":
                        $scope.selectedMenu = 1;
                        break;
                    default:
                        $scope.selectedMenu = 2;
                    
                }
                break;
            case "admin":
                $scope.selectedMenu = 100;
                break;
            default:
                $scope.selectedMenu = 0;
                break;
        }
        
    });
	
    $scope.getTotalItemsInCart = function()
    {
        return cart.getTotalItemsInCart();
    };
    
    $scope.getCartPrice = function()
    {
        return cart.getCartPrice();
    };
    
    $scope.gotoShop = function()
    {
        appService.gotoShop();
    };
    
    ctrl.changeAddress = function($event)
    {
        $mdDialog.show(
        {
            controller: function(appService, $scope, profileData)
            {    
                $scope.postal = "";
                
                $scope.distance = profileData.instance.optimizationDistance;
                
                var response = null;

                $scope.getUserCoordinates = function()
                {
                    appService.getUserCoordinates(function(result)
                    {
                        $scope.newAddress = result.formatted_address;
                        response = result;
                        $scope.$apply();
                    });
                };

                $scope.getUserCoordinatesFromPostcode = function()
                {
                    appService.getUserCoordinatesFromPostcode($scope.postal, function(result)
                    {
                        $scope.newAddress = result.formatted_address;
                        response = result;
                        $scope.$apply();
                    });
                };
                
                $scope.cancel = function()
                {
                    $mdDialog.cancel();
                };
                
                $scope.hide = function()
                {
                    var res = 
                    {
                        address : response,
                        distance : $scope.distance
                    };
                    
                    $mdDialog.hide(res);
                };
            },
            templateUrl:  $sce.trustAsResourceUrl(appService.baseUrl + 'assets/templates/changeAddress.html'),
            parent: angular.element(document.body),
            targetEvent: $event,
            clickOutsideToClose:true,
            disableParentScroll : true,
            preserveScope:true,
            fullscreen: false,
            onRemoving : function()
            {
                // Restore scroll
                $(document).scrollTop($scope.scrollTop);
            }
        }).then(function(result) 
        {
            var address = result.address;
            
            
            if(address)
            {
                profileData.set("optimizationDistance", result.distance);
                
                if(appService.isUserLogged)
                {
                    var profile_address = 
                    {
                        postcode : ctrl.getAddressComponent(address.address_components, "postal_code"),
                        address : address.formatted_address,
                        city : ctrl.getAddressComponent(address.address_components, "administrative_area_level_2"),
                        state : ctrl.getAddressComponent(address.address_components, "administrative_area_level_1"),
                        country : ctrl.getAddressComponent(address.address_components, "country"),
                        longitude : appService.longitude,
                        latitude : appService.latitude
                    };
                    
                    $scope.postcode = profile_address.postcode;
                    
                    eapp.updateAddress(profile_address).then(function()
                    {
                        window.location.reload();
                    });
                }
                else
                {
                    window.localStorage.setItem("longitude", address.geometry.location.lng());
                    window.localStorage.setItem("latitude", address.geometry.location.lat());
                    window.localStorage.setItem("postcode", ctrl.getAddressComponent(address.address_components, "postal_code"));
                    window.localStorage.setItem("currentAddress", address.formatted_address);
                    
                    window.location.reload();
                }
            }
            else if(parseInt(result.distance) !== parseInt(profileData.instance.optimizationDistance))
            {
                profileData.set("optimizationDistance", result.distance);
                window.location.reload();
            }
            
        }, function() 
        {

        });
    };
    
    ctrl.getAddressComponent = function(address, type)
    {
        var res = "";
        
        address.forEach(function(component)
        {
            if(component.types.includes(type))
            {
                res = component.long_name;
            }
        });
        
        return res;
    };
    
    
    
});

