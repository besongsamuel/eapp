angular.module('eappApp').controller('CategoryController', ["$scope", "$rootScope", "eapp", function ($scope, $rootScope, eapp) 
{
    $rootScope.isMainMenu = true;
    
    $scope.loading = false;
    
    $scope.root = $rootScope;
    
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
    
    $rootScope.select_category = function($event, category)
    {
        $scope.clearSessionItems();
        var category_id = parseInt(category.id);
        eapp.recordHit("eapp_product_category ",category_id);
        window.sessionStorage.setItem("category_id", category_id);    
        window.sessionStorage.setItem("category_name", category.name);
        window.location =  $scope.site_url.concat("/shop");
    };
    
    $scope.select_json_category = function($event, category)
    {
        $rootScope.select_category($event, JSON.parse(category));
    };

    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
  
}]);
