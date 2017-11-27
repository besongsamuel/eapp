/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("ProductController", ["$scope", "eapp", function($scope, eapp) 
{
    
    var url = window.location.href.toString().split("/");
    var product_id = url[url.length - 1];
    
    // Get the latest products
    var promise = eapp.getProduct(product_id);
    
    promise.then(function(response)
    {
        $scope.storeProduct = response.data;
    },
    function(errorResponse)
    {
        $scope.storeProduct = null;
    });

	
}]);

