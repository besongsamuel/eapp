/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


angular.module('eappApp').component("address", 
{
    templateUrl : "templates/components/address.html",
    controller : function($scope)
    {
        var ctrl = this;
        
        $scope.vsOptions = 
        {
            types : ['address'],
            componentRestrictions : { country : 'CA' }
        };
        
        $scope.$watch("place.address", function(newValue)
        {
            if(newValue)
            {
                $scope.address.address = newValue;
                ctrl.onAddressChanged({ address : $scope.address });
            }
        });
        $scope.$watch("place.country", function(newValue)
        {
            if(newValue)
            {
                $scope.address.country = newValue;
                ctrl.onAddressChanged({ address : $scope.address });
            }
        });
        $scope.$watch("place.city", function(newValue)
        {
            if(newValue)
            {
                $scope.address.city = newValue;
                ctrl.onAddressChanged({ address : $scope.address });
            }
        });
        $scope.$watch("place.state", function(newValue)
        {
            if(newValue)
            {
                $scope.address.state = newValue;
                ctrl.onAddressChanged({ address : $scope.address });
            }
        });
        $scope.$watch("place.postcode", function(newValue)
        {
            if(newValue)
            {
                $scope.address.postcode = newValue;
                ctrl.onAddressChanged({ address : $scope.address });
            }
        });
        $scope.$watch("place.longitude", function(newValue)
        {
            if(newValue)
            {
                $scope.address.longitude = newValue;
                ctrl.onAddressChanged({ address : $scope.address });
            }
        });
        $scope.$watch("place.latitude", function(newValue)
        {
            if(newValue)
            {
                $scope.address.latitude = newValue;
                ctrl.onAddressChanged({ address : $scope.address });
            }
        });
        
        
                
        ctrl.$onInit = function()
        {
            $scope.formReference = ctrl.formReference;
            $scope.address = ctrl.address;
            
            if(angular.isNullOrUndefined(ctrl.readOnlyFields))
            {
                ctrl.readOnlyFields = false;
            }
        };
        
    },
    bindings : 
    {
        onAddressChanged : '&',
        formReference : '=',
        address : '<',
        readOnlyFields : '<',
        caption : '@'
    }
});
