/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("MenuController", function($scope, appService, cart, $rootScope) 
{
    appService.ready.then(function()
    {
        
        $rootScope.isRegularUser = appService.isRegularUser;
        
        $rootScope.loggedUser = appService.loggedUser;
        
        $rootScope.isUserLogged = appService.isUserLogged;
        
        $scope.selectedMenu = 0;
        
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
    }
    
});

