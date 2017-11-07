angular.module('eappApp').controller('AccountCreatedController', ["$scope", function ($scope) 
{
    $scope.message = 'Votre compte OtiPrix a été créé avec succès.';
    $scope.visible = false;
    $scope.gotoHome = function()
    {
        window.location.href = $scope.site_url.concat("/home");
    };
    
    $scope.gotoAccount = function()
    {
        window.location.href = $scope.site_url.concat("/account");
    };
    
    $scope.Init = function()
    {
        
    };
    
    angular.element(document).ready(function()
    {
        if(window.sessionStorage.getItem('newAccount'))
        {
            window.sessionStorage.removeItem('newAccount');
            $scope.visible = true;
        }
        else
        {
            window.location.href = $scope.site_url.concat("/account/invalid");
        }
    });
  
}]);
