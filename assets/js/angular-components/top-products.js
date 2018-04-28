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
            
            if(angular.isNullOrUndefined(ctrl.countCaption))
            {
                ctrl.countCaption = "Vues: ";
            }
            
            $scope.countCaption = ctrl.countCaption;
        };
        
        
    },
    bindings : 
    {
        data : '=',
        caption : '@',
        countCaption : '@'
    }
});