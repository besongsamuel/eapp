/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("UploadController", ["$rootScope", "$scope", "$company","$mdDialog", function($rootScope, $scope, $company, $mdDialog) 
{
    $rootScope.isAboutUs = true;
    
    $scope.Init = function()
    {
        $scope.success = false;
        $scope.maxItems = 0;
        $scope.incomplete = false;
        
        if(window.sessionStorage.getItem('success'))
        {
            $scope.success = JSON.parse(window.sessionStorage.getItem('success'));
            window.sessionStorage.removeItem('success');
            
            $scope.incomplete = !$scope.success;
        }
        
        if(window.sessionStorage.getItem('maxItems'))
        {
            $scope.maxItems = JSON.parse(window.sessionStorage.getItem('maxItems'));
            window.sessionStorage.removeItem('maxItems');
        }
    };
    
    function fileUploaded(response)
    {
        window.sessionStorage.setItem('success', JSON.stringify(response.data.success));
        
        window.sessionStorage.setItem('maxItems', JSON.stringify(response.data.max_items));
        
        // Reload Page
        window.location.href = $rootScope.site_url.concat("/account/index/1");
    };
    
    $("#fileUploadInput").change(function()
    {
        if($scope.uploading)
        {
            var input = $('#fileUploadInput');
        
            var replace = false;

            var confirm = $mdDialog.confirm()
              .title('Options de téléversement')
              .textContent('Voulez-vous ajouter à votre liste de produits ou remplacer vos de produits?')
              .ariaLabel('Options de téléversement')
              .targetEvent(null)
              .ok('Ajouter')
              .cancel('Remplacer');

            $mdDialog.show(confirm).then(function() 
            {
                replace = false;
                $company.uploadProducts(input[0].files[0], fileUploaded, replace);

            }, function() 
            {
                replace = true;
                $company.uploadProducts(input[0].files[0], fileUploaded, replace);
            });
            
            $scope.uploading = false;
        }
        
        
        
        
        
    });
    
    $scope.selectFile = function()
    {
        $('#fileUploadInput').trigger('click');
        $scope.uploading = true;
    };
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
}]);
