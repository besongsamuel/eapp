angular.module('eappApp').controller('CategoryController', ["$scope", "$rootScope", "eapp", function ($scope, $rootScope, eapp) 
{
    $rootScope.isMainMenu = true;
    
    $scope.loading = false;
    
    $scope.Init = function()
    {
        var categoriesPromise = eapp.getCategories();
        
        $scope.loading = true;
        
        categoriesPromise.then(function(response)
        {
            $scope.categories = response.data;
            
            $scope.loading = false;
        });
    };

    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
  
}]);
