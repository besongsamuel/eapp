/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("HomeController", ["$rootScope", "$scope", "eapp", function($rootScope, $scope, eapp) 
{
    $rootScope.isHome = true;
    
    var getLatestProductsPromise = eapp.getLatestProducts();
    
    getLatestProductsPromise.then(function(response)
    {
        var array = $.map(response.data, function(value, index) {
            return [value];
        });
        
        $scope.latestProducts = array;
    });
    
    $scope.gotoShop = function()
    {
      window.location.href = $scope.site_url.concat("/shop");  
    };
    
}]);

