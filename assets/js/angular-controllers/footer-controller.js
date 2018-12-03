/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("FooterController", ["$scope", "eapp", function($scope, eapp) 
{
    
    $scope.currentYear = (new Date()).getFullYear();
    
    $scope.subscribe = function(ev)
    {
        var subscribePromise = eapp.subscribe($scope.subscribe_email);
        
        subscribePromise.then(function(response)
        {
            eapp.showAlert(ev, response.data.title, response.data.message);
        });
    };
    
    eapp.getMostViewedCategories().then(function(response)
    {
        $scope.categories = response.data;
    });
    
    
}]);

