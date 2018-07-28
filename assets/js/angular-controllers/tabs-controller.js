/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("TabsController", ["$rootScope", "$scope", function($rootScope, $scope) 
{
    
    
    
    $scope.Init = function()
    {
        if(window.sessionStorage.getItem("selectedTab"))
        {
            $scope.selectedTab = parseInt(window.sessionStorage.getItem("selectedTab"));
        }   
        else
        {
            $scope.selectedTab = 0;
        }
        
        $scope.$watch("selectedTab", function(newVal, oldVal, scope)
        {
            if(!angular.isNullOrUndefined(newVal))
            {
                window.sessionStorage.setItem("selectedTab", newVal);
            }

        });
    };
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
}]);
