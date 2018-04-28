
angular.module('eappApp').controller('CompanyStatsController', function($scope, $company)
{
    
    var ctrl = this;
    
    $scope.period = 1;
    
    $scope.sort = "desc";
    
    $scope.limit = 5;
    
    ctrl.periodChanged = function()
    {
        $scope.loading = true;
        $company.getStats('desc', $scope.limit, $scope.period, getStatsSuccess);
    };
        
    angular.element(document).ready(function()
    {
        
        (function()
        {
             $scope.company = 
            {
                id : $scope.loggedUser.company.id,
                name : $scope.loggedUser.company.name,
                neq : $scope.loggedUser.company.neq
            };

            $scope.loading = true;

            $company.getStats('desc', $scope.limit, $scope.period, getStatsSuccess);
            
        })();
        
    });
    
    function getStatsSuccess(response)
    {
        $scope.loading = false;

        $scope.stats = response.data;
    };
});