angular.module('eappApp').controller('AccountCreatedController', function ($scope, appService) 
{
    
    $scope.visible = false;
    $scope.gotoHome = function()
    {
        window.location.href = appService.siteUrl.concat("/home");
    };
    
    $scope.gotoAccount = function()
    {
        window.location.href = appService.siteUrl.concat("/account");
    };
    
    $scope.Init = function()
    {
        if(window.sessionStorage.getItem('newAccount'))
        {
            window.sessionStorage.removeItem('newAccount');
            $scope.message = 'Votre compte OtiPrix a été créé avec succès.';
            $scope.visible = true;
        }
        else
        {
            $scope.message = 'La page demandée n\'est plus disponible.';
            $scope.invalid = true;
        }
    };
    
    $scope.Init();
  
});
