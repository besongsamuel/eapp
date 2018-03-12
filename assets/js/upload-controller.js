/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("UploadController", ["$rootScope", "$scope", "$company","eapp", function($rootScope, $scope, $company, eapp) 
{
    $rootScope.isAboutUs = true;
    
    $scope.Init = function()
    {
        
    };
    
    function fileUploaded()
    {
        // Reload Page
        window.location.href = $rootScope.site_url.concat("/account");
    };
    
    $("#fileUploadInput").change(function()
    {
        var input = $('#fileUploadInput');
        
        $company.uploadProducts(input[0].files[0], fileUploaded);
        
    });
    
    $scope.selectFile = function()
    {
        $('#fileUploadInput').trigger('click');
    };
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
}]);
