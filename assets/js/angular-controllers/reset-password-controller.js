angular.module('eappApp').controller('ResetPasswordController', ["$scope", "appService", "eapp", function ($scope, appService, eapp) 
{
    
    var ctrl = this;
    
    var getUrlParameter = function getUrlParameter(sParam) 
    {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };
    
    $scope.resetPasswordErrorMessage = null;
    $scope.resetPasswordSuccessMessage = null;
    
    $scope.resetToken = getUrlParameter('reset_token');
    
    ctrl.countDown =  function()
    {
        if(ctrl.count > 0)
        {
            if(ctrl.count < 6)
            {
                $scope.$apply(function()
                {
                    $scope.resetPasswordSuccessMessage = "Votre mot de passe a été changé. Vous serez redirigé vers a page de connexion dans " + ctrl.count + " secondes..."; 
                });
            }
            else
            {
                $scope.resetPasswordSuccessMessage = "Votre mot de passe a été changé. Vous serez redirigé vers a page de connexion dans " + ctrl.count + " secondes..."; 
            }
            
            ctrl.count--;
            
            setTimeout(ctrl.countDown, 1000);
        }
        else
        {
            window.location.href = appService.siteUrl.concat("/account/login");
        }
    };
    
    $scope.resetPassword = function()
    {
        var resetPasswordPromise = eapp.resetPassword($scope.password, $scope.resetToken);
        
        resetPasswordPromise.then(function(response)
        {
            if(response.data.success)
            {
                ctrl.count = 6;
                                
                ctrl.countDown();
                
                $scope.confirm_password = '';
                $scope.password = '';
                $scope.resetPasswordForm.$setPristine();
                $scope.resetPasswordForm.$setValidity();
                $scope.resetPasswordForm.$setUntouched();
                $scope.resetPasswordSuccessMessage = response.data.message;
                $scope.resetPasswordErrorMessage = null;
            }
            else
            {
                $scope.resetPasswordErrorMessage = response.data.message;
                $scope.resetPasswordSuccessMessage = null;
            }
        });
    };
  
}]);
