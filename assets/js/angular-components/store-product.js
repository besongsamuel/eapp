angular.module('eappApp').component("storeProduct", 
{
    templateUrl : "templates/components/storeProduct.html",
    controller : Controller,
    bindings : 
    {
        storeProduct : '@'
    }
});

function Controller($scope, $rootScope, eapp)
{
    var ctrl = this;
    
    $scope.root = $rootScope;
    
    ctrl.$onInit = function()
    {
        $scope.storeProduct = JSON.parse(ctrl.storeProduct);
    };
    
    ctrl.viewProduct = function(product_id, ev)
    {
        eapp.viewProduct($scope, product_id, ev);
    };
    
}