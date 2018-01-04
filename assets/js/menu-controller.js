/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("MenuController", ["$rootScope", "$http", "$mdDialog", "eapp", function($rootScope, $http, $mdDialog, eapp) 
{
    
    $rootScope.isHome = false;
    
    // This variable prevents us from getting the cart contents twice.
    $rootScope.cartReady = false;
	
    $rootScope.gotoShop = function()
    {
        $rootScope.clearSessionItems(); 
        window.location =  $rootScope.site_url.concat("/shop");
    };
    
    $rootScope.remove_product_from_cart = function(product_id)
    {
        var removePromise = eapp.removeFromCart($rootScope.getRowID(product_id));

        removePromise.then(function(response)
        {
            if(Boolean(response.data.success))
            {
                $rootScope.removeItemFromCart(product_id);
            }
        });

    };
    
    angular.element(document).ready(function()
    {
        var cartPromise = eapp.getCart();
    
        cartPromise.then(function(cartResponse)
        {
            // Get the cart data when the menu is loaded
            $rootScope.cart = cartResponse.data;
            
            $rootScope.cartReady = true;
            
        });
    });

	
}]);

