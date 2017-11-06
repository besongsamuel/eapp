angular.module('eappApp').controller('PasswordForgottenController', ["$scope", "$rootScope", "eapp", function ($scope, $rootScope, eapp) 
{
    
    $scope.passwordForgottenErrorMessage = null;
    $scope.passwordForgottenSuccessMessage = null;
    
    $scope.sendPasswordReset = function()
    {
        var sendPasswordResetPromise = eapp.sendPasswordReset($scope.email);
        
        sendPasswordResetPromise.then(function(response)
        {
            if(response.data.success)
            {
                 $scope.passwordForgottenSuccessMessage = response.data.message;
                 $scope.passwordForgottenErrorMessage = null;
            }
            else
            {
                $scope.passwordForgottenErrorMessage = response.data.message;
                $scope.passwordForgottenSuccessMessage = null;
            }
        });
    };
  
}]);
