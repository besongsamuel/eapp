angular.module('eappApp').component("quantityInput", 
{
    templateUrl : "templates/components/quantityInput.html",
    controller : QuantityInputController,
    bindings : 
    {
        quantity : '=',
        onChange : '&'
    }
});

function QuantityInputController($scope)
{
    var ctrl = this;
    
    ctrl.add = function(ev)
    {
        $scope.quantity = $scope.quantity + 1; 
        
        $("#" + ctrl.id).attr('disabled', false);
        
        ctrl.quantity = $scope.quantity;
        
        ctrl.onChange({quantity : ctrl.quantity});
        
    };
    
    $scope.$watch("quantity", function()
    {
        ctrl.quantity = $scope.quantity;
        
        ctrl.onChange({quantity : ctrl.quantity});
    });
    
    ctrl.subtract = function(ev)
    {
        if($scope.quantity > 0)
        {
           $scope.quantity = $scope.quantity - 1; 
        }
        
        if(parseInt($scope.quantity) === 0)
        {
            $("#" + ctrl.id).attr('disabled', true);
        }
        
        ctrl.quantity = $scope.quantity;
        
        ctrl.onChange({quantity : ctrl.quantity});
    };
        
    ctrl.$onInit = function()
    {
        ctrl.id = "btn_" + Math.random().toString(36).substr(2, 9);;
        
        $("#btn-minus").attr("id", ctrl.id);
        
        $scope.quantity = ctrl.quantity;
    };
}