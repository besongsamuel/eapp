    
function optimization_avg(list)
{
    var average = 0;
    var count = 0;
    var display = "-";

    for(var i in list)
    {
        average += parseFloat(list[i].price_optimization);
        count++;
    }

    if(parseFloat(average) !== 0)
    {
        display = parseInt(average / count);
    }

    return display;
}

function items_count(list)
{
    var average = 0;
    var display = "-";
    var count = 0;

    for(var i in list)
    {
        var items = JSON.parse(list[i].items);
        average += items.length;
        count++;
    }

    if(parseFloat(average) !== 0)
    {
        display = parseInt(average / count);
    }

    return display;
}
    
angular.module('eappApp').controller('AccountOptimizationController', ["$scope", "$rootScope", function ($scope, $rootScope) 
{
    $rootScope.isAccountMenu = true;
    
    $scope.Init = function()
    {
        $scope.optimizations = [];
        
        if($scope.isUserLogged)
        {
            var data = 
            {
                label : "Économies général",
                value : optimization_avg($scope.loggedUser.optimizations.overall),
                count : items_count($scope.loggedUser.optimizations.overall)
            };
            
            $scope.optimizations.push(data);
            
            var data = 
            {
                label : "Économies cette semaine",
                value : optimization_avg($scope.loggedUser.optimizations.currentWeek),
                count : items_count($scope.loggedUser.optimizations.currentWeek)
            };
            
            $scope.optimizations.push(data);
            
            
            var data = 
            {
                label : "Économies ce mois",
                value : optimization_avg($scope.loggedUser.optimizations.currentMonth),
                count : items_count($scope.loggedUser.optimizations.currentMonth)
            };
            
            $scope.optimizations.push(data);
            
            var data = 
            {
                label : "Économies cette année",
                value : optimization_avg($scope.loggedUser.optimizations.currentYear),
                count : items_count($scope.loggedUser.optimizations.currentYear)
            };
            
            $scope.optimizations.push(data);
            
        }
    };
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
  
}]);
