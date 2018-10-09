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

function Controller($scope, $rootScope, eapp)
{
    var ctrl = this;
    
    $scope.root = $rootScope;
    
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
    
}