/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("MenuController", function($scope, appService, cart, $rootScope, $mdDialog, eapp) 
{
    var ctrl = this;
    
    appService.ready.then(function()
    {
        
        $rootScope.isRegularUser = appService.isRegularUser;
        
        $rootScope.loggedUser = appService.loggedUser;
        
        $rootScope.isUserLogged = appService.isUserLogged;
        
        $rootScope.site_url = appService.siteUrl;
        
        $rootScope.base_url = appService.baseUrl;
        
        $scope.selectedMenu = 0;
        
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
            controller: function(appService, $scope)
            {    
                $scope.postal = "";
                
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
                    $mdDialog.hide(response);
                };
            },
            templateUrl:  appService.baseUrl + 'assets/templates/changeAddress.html',
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
        }).then(function(address) 
        {
            if(address)
            {
                if(appService.isUserLogged)
                {
                    var profile_address = 
                    {
                        postcode : address.address_components.length > 0 ? address.address_components[0].long_name : "",
                        address : address.formatted_address,
                        city : address.address_components.length > 2 ?address.address_components[2].long_name : "",
                        state : address.address_components.length > 4 ?address.address_components[4].long_name : "",
                        country : address.address_components.length > 5 ?address.address_components[5].long_name : "",
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
                    $scope.postcode = address.address_components.length > 0 ? address.address_components[0].long_name : appService.postcode;
                }
                
                
            }
            
        }, function() 
        {

        });
    };
    
    
    
});

