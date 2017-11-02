angular.module('eappApp').controller('CategoryController', ["$scope", "$rootScope", "eapp", function ($scope, $rootScope, eapp) 
{
    $rootScope.isMainMenu = true;
    
    $scope.Init = function()
    {
        var categoriesPromise = eapp.getCategories();
        
        categoriesPromise.then(function(response)
        {
            $scope.categories = response.data;
        });
    };
    
    $scope.select_category = function($event, id)
    {
        $scope.clearSessionItems();
        var category_id = parseInt(id);
        eapp.recordHit("eapp_product_category ",category_id);
        window.sessionStorage.setItem("category_id", category_id);    
        window.location =  $scope.site_url.concat("/shop");
    };
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
  
}]);
