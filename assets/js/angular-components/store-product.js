angular.module('eappApp').component("storeProduct", 
{
    templateUrl : "templates/components/storeProduct.html",
    controller : Controller,
    bindings : 
    {
        jsonStoreProduct : '@',
        storeProduct : '<',
        fullDisplay : '<'
    }
});

function Controller($scope, appService, eapp, cart)
{
    var ctrl = this;
    
    appService.ready.then(function()
    {
        $scope.isUserLogged = appService.isUserLogged;
        
        $scope.isUserActive =  appService.isUserLogged ? parseInt(appService.loggedUser.is_active) === 1 : false;
        
        $scope.loggedUser = appService.loggedUser;
    });
        
    ctrl.$onInit = function()
    {
        if(!angular.isNullOrUndefined(ctrl.jsonStoreProduct))
        {
            $scope.storeProduct = JSON.parse(ctrl.jsonStoreProduct);
        }
        
        if(!angular.isNullOrUndefined(ctrl.storeProduct))
        {
            $scope.storeProduct = ctrl.storeProduct;
        }
        
    };
    
    ctrl.viewProduct = function(product_id, ev)
    {
        eapp.viewProduct($scope, product_id, ev);
    };
    
    $scope.productInCart = function(product_id)
    {
        return cart.productInCart(product_id);
    };
    
    $scope.addProductToCart = function(product_id, store_product_id = -1, product_quantity = 1)
    {
        cart.addProductToCart(product_id, store_product_id, product_quantity);
    };
    
    $scope.removeProductFromCart = function(product_id)
    {
        cart.removeProductFromCart(product_id);

    };
    
    $scope.viewStoreDetails = function(ev, retailer)
    {
        eapp.viewStoreDetails(ev, retailer);
    };
    
}