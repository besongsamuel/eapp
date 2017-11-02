angular.module('eappApp').controller('SelectAccountStoreController', ["$scope", "$mdDialog", "eapp", "$rootScope", function ($scope, $mdDialog, eapp, $rootScope) 
{
    $rootScope.isAccountMenu = true;
    
    $scope.max_stores = 4;
    
    $scope.Init = function()
    {
        var retailersPromise = eapp.getCloseRetailers($scope.getDistance());
        
        retailersPromise.then(function(response)
        {
            $scope.retailers = response.data;
        });
    };
    
    $scope.goto_retailer = function(id)
    {
        $scope.clearSessionItems();  
	var store_id = parseInt(id);
	window.sessionStorage.setItem("store_id", store_id);    
	window.location =  $scope.site_url.concat("/shop");
    };
    
    $scope.select_retailer = function($event)
    {
        var element = $event.target;
        
        if($(element).hasClass( "check" ))
        {
            // Get the retailer ID
            var index = $scope.selected_retailers.indexOf(parseInt(element.id));
            
            if (index > -1) 
            {
                $scope.selected_retailers.splice(index, 1);
            }
            
            $(element).toggleClass("check");
        }
        else
        {
            if($scope.selected_retailers.length < $scope.max_stores)
            {
                $scope.selected_retailers.push(parseInt(element.id));
                $(element).toggleClass("check");
            }
            else
            {
                $scope.showSimpleToast("Vous ne pouvez pas sélectionner plus de "+$scope.max_stores+" magasins.", "select-store-box");
            }
        }
    };
    
    
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
  
}]);
