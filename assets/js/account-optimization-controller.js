    
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
    
angular.module('eappApp').controller('AccountOptimizationController', ["$scope", "$rootScope", "eapp", "$mdDialog", function ($scope, $rootScope, eapp, $mdDialog) 
{
    $rootScope.isAccountMenu = true;
    
    $scope.viewOptimization = function(index, ev)
    {
        var data = [];
        
        var settings = {};
        
        switch(parseInt(index))
        {
            case 0:
                // Set week data
                settings.header = "Économies cette semaine";
                
                for(var x in $scope.userOptimization.currentWeek)
                {
                    data.push({ Total : $scope.userOptimization.currentWeek[x].price_optimization, Day : $scope.userOptimization.currentWeek[x].day});
                }
                
                break;
            case 1:
                // Set month data
                settings.header = "Économies ce mois";
                
                for(var x in $scope.userOptimization.currentMonth)
                {
                    data.push({ Total : $scope.userOptimization.currentMonth[x].price_optimization, Week : $scope.userOptimization.currentMonth[x].week});
                }
                break;
            case 2:
                // Set Year data
                settings.header = "Économies cette année";
                
                for(var x in $scope.userOptimization.currentYear)
                {
                    data.push({ Total : $scope.userOptimization.currentYear[x].price_optimization, Month : $scope.userOptimization.currentYear[x].month});
                }
                break;
            default:
                settings.header = "Économies général";
                
                for(var x in $scope.userOptimization.overall)
                {
                    data.push({ Total : $scope.userOptimization.overall[x].price_optimization, Day : $scope.userOptimization.overall[x].date_created });
                }
                break;
        }
        
        $scope.showOptimization(ev, data, settings);
        
        
    };
    
    $scope.showOptimization = function(ev, data, settings)
    {
        // Initialize data here
        
        
        $scope.header = settings.header;
        $scope.data = data;
        $scope.scrollTop = $(document).scrollTop();
        // Show dialog for user to change the store of the product. 
        $mdDialog.show({
            controller: showOptimizationController,
            templateUrl:  $scope.base_url + 'assets/templates/show-optimization.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose:true,
            disableParentScroll : true,
            preserveScope:true,
            scope : $scope,
            fullscreen: false,
            onRemoving : function()
            {
                // Restore scroll
                $(document).scrollTop($scope.scrollTop);
            }
          })
          .then(function(answer) 
            {
                
          }, function() {
                
          });
    };
    
    function showOptimizationController($scope, $mdDialog) 
    {
        $scope.hide = function() 
        {
            $mdDialog.hide();
        };

        $scope.cancel = function() 
        {
            $mdDialog.cancel();
        };
        
    };
    
    $scope.Init = function()
    {
        
        var getUserOptimizationPromise = eapp.getUserOptimizations();
        
        getUserOptimizationPromise.then(function(response)
        {
            $scope.userOptimization = response.data;
        });
        
        
        $scope.optimizations = [];
        
        if($scope.isUserLogged)
        {
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
            
            var data = 
            {
                label : "Économies général",
                value : optimization_avg($scope.loggedUser.optimizations.overall),
                count : items_count($scope.loggedUser.optimizations.overall)
            };
            
            $scope.optimizations.push(data);
            
        }
    };
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
  
}]);
