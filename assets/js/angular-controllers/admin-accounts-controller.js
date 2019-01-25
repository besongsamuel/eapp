/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("UserAccountsController", function($scope, eapp) 
{    
    $scope.type = "company";

    
    $scope.query = 
    {
        filter: '',
        limit: '100',
        order: 'name',
        page: 1
    };
    
    $scope.$watch("query.page", function(newValue, oldValue)
    {
        window.scrollTo(0, 0);
    });
    
    $scope.getAccounts = function()
    {
        
        $scope.promise = eapp.getCompanyAccounts($scope.query);
        
        $scope.promise.then(function(response)
        {
            var data =    response.data.accounts;
            
            $scope.count = response.data.count;
            
            $scope.accounts = Object.keys(data).map(i => data[i]);
            
            if($scope.accounts.length > 0)
            {
                $scope.headers = Object.keys($scope.accounts[0]);
            }
            
             
        });
    };
    
    angular.element(document).ready(function()
    {
        $scope.getAccounts();
    });
    
});
