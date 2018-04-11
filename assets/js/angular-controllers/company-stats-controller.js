
angular.module('eappApp').controller('CompanyStatsController', function($scope, $company)
{
    
    var ctrl = this;
    
    $scope.Init = function()
    {
        $scope.company = 
        {
            id : $scope.loggedUser.company.id,
            name : $scope.loggedUser.company.name,
            neq : $scope.loggedUser.company.neq
        };
        
        $company.getStats('desc', 5, 1, getStatsSuccess);
        
    };
    
    function getStatsSuccess(response)
    {
        $scope.stats = response.data;
    };
        
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
});