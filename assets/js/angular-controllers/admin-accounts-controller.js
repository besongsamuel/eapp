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
    
     $scope.$watch("type", function(newValue, oldValue)
    {
        $scope.getAccounts();
    });
    
    $scope.getAccounts = function()
    {
        
        if($scope.type == "company")
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
        }
        
        if($scope.type == "user")
        {
            $scope.promise = eapp.getUserAccounts($scope.query);
        
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
        }
        
        
    };
    
    $scope.activate = function(account, $event)
    {
        eapp.showConfirmDialog($event, "Activer le compte d'utilisateur?").then(function()
        {
            eapp.ToggleAccountState(account.id, 1).then(function(response)
            {
                if(response.data.success)
                {
                    var index = $scope.accounts.map(function(acc){ return acc.id; }).indexOf(account.id);
                    $scope.accounts[index].is_active = 1;
                }
            });
        });
    };
    
    $scope.deactivate = function(account, $event)
    {
        eapp.showConfirmDialog($event, "Désactiver le compte utilisateur?").then(function()
        {
            eapp.ToggleAccountState(account.id, 0).then(function(response)
            {
                if(response.data.success)
                {
                    var index = $scope.accounts.map(function(acc){ return acc.id; }).indexOf(account.id);
                    $scope.accounts[index].is_active = 0;
                }
            });
        });
    };
    
    angular.element(document).ready(function()
    {
        $scope.getAccounts();
    });
    
});
