angular.module('eappApp').controller('UnsubscribeController', ["$scope", "appService", "eapp", function ($scope, appService, eapp) 
{
    
    $scope.unsubscribeErrorMessage = null;
    $scope.unsubscribeSuccessMessage = null;
    $scope.subscribed = false;
    
    $scope.unsubscribeToken = eapp.getUrlParameter('token');
    
    var unsubscribeEmailPromise = eapp.getUnsubscribeEmailFromToken($scope.unsubscribeToken);
    
    unsubscribeEmailPromise.then(function(response)
    {
        if(response.data.success)
        {
            $scope.userEmail = response.data.email;
            
        }
        else
        {
            $scope.userEmail = "";
            //redirecto to invalid page
            window.location.href = appService.siteUrl.concat("/account/invalid");
        }
    });
    
    $scope.unsubscribe = function()
    {
        var unsubscribePromise = eapp.unsubscribe($scope.unsubscribeToken);
    
        unsubscribePromise.then(function(response)
        {
            if(response.data.success)
            {
                $scope.subscribed = true;
                $scope.unsubscribeSuccessMessage = "Vous avez été desabonné avec succès à Otiprix.";
            }
            else
            {
                $scope.unsubscribeErrorMessage = "Une erreur inattendue s'est produite.";
            }
        });
    };
    
    $scope.gotoHome = function()
    {
        window.location.href = appService.siteUrl.concat("/home");
    };
    
}]);
