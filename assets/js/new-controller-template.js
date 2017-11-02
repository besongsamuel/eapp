angular.module('eappApp').controller('AccountOptimizationController', ["$scope", "$rootScope", function ($scope, $rootScope) 
{
    $rootScope.isMainMenu = true;
    
    $scope.Init = function()
    {
        
    };
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
  
}]);
