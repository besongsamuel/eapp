angular.module('eappApp').component("storeProduct", 
{
    templateUrl : "templates/components/storeProduct.html",
    controller : Controller,
    bindings : 
    {
        storeProduct : '@'
    }
});

function Controller($scope, $rootScope)
{
    var ctrl = this;
    
    $scope.root = $rootScope;
    
    ctrl.$onInit = function()
    {
        $scope.storeProduct = JSON.parse(ctrl.storeProduct);
    };
    
}