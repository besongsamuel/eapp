/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("ChangeLocationController", function(appService, $scope) 
{
    var ctrl = this;
    
    $scope.postcode = "";
    
    $scope.message = false;
    
    ctrl.getUserCoordinates = function()
    {
        appService.getUserCoordinates(function()
        {
            $scope.message = true;
            $scope.$apply();
        });
    };
        
    ctrl.getUserCoordinatesFromPostcode = function()
    {
        appService.getUserCoordinatesFromPostcode($scope.postcode, function()
        {
            $scope.message = true;
            $scope.$apply();
        });
    };
    
});
