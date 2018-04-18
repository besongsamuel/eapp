// Component to Select from user grocery list
angular.module('eappApp').component("topProducts", 
{
    templateUrl : "templates/components/top-products.html",
    controller : function($scope)
    {
        var ctrl = this;
        
        ctrl.$onInit = function()
        {
            $scope.products = ctrl.data;
            $scope.caption = ctrl.caption;
        };
        
        ctrl.$onChanges = function(changeObj)
        {
            
        };
        
    },
    bindings : 
    {
        data : '=',
        caption : '@'
    }
});