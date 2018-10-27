angular.module('eappApp').controller('CategoryController', function ($scope, $rootScope, eapp, appService) 
{
    $rootScope.isMainMenu = true;
    
    $scope.loading = false;
    
    $scope.root = $rootScope;
    
    var ctrl = this;
    
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
    
    ctrl.getHomeCategories = function()
    {
        var categoriesPromise = eapp.getCategories(5, 8);
        
        $scope.loading = true;
        
        categoriesPromise.then(function(response)
        {
            $scope.homePageCategories = response.data;
            
            $scope.loading = false;
        });
    };
    
    $rootScope.select_category = function($event, category)
    {
        appService.clearSessionItems();
        var category_id = parseInt(category.id);
        appService.recordHit("eapp_product_category ",category_id);
        window.sessionStorage.setItem("category_id", category_id);    
        window.sessionStorage.setItem("category_name", category.name);
        window.location = appService.siteUrl.concat("/shop");
    };
    
    $scope.select_json_category = function($event, category)
    {
        $rootScope.select_category($event, JSON.parse(category));
    };

    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
  
});
