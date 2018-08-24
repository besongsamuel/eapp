/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("TabsController", ["$rootScope", "$scope", "sessionData", function($rootScope, $scope, sessionData) 
{
    $scope.Init = function()
    {
        $scope.sessionData = sessionData.get();
        
        if(angular.isNullOrUndefined($scope.sessionData.accountMenuIndex))
        {
            $scope.sessionData.accountMenuIndex = 1;
        }
    };
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
    $scope.$watch("sessionData.accountMenuIndex", function(newValue)
    {
        if(!angular.isNullOrUndefined(newValue))
        {
            sessionData.set("accountMenuIndex", newValue);
        }
         
    });
    
}]);
