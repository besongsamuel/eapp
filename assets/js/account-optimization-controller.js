    
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
				
				for(var x in $scope.userOptimization.checkDay)
                {
                    data.push({ Total : $scope.userOptimization.checkDay[x].total, Day : $scope.userOptimization.checkDay[x].date});
                }
               
                break;
            case 1:
                // Set month data
                settings.header = "Économies ce mois";
                
                for(var x in $scope.userOptimization.checkWeek)
                {
                    data.push({ Total : $scope.userOptimization.checkWeek[x].total, Week : $scope.userOptimization.checkWeek[x].date});
                }
                
                break;
            case 2:
                // Set Year data
                settings.header = "Économies cette année";
                
                for(var x in $scope.userOptimization.checkMonth)
                {
                    data.push({ Total : $scope.userOptimization.checkMonth[x].total, Month : $scope.userOptimization.checkMonth[x].date});
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
         switch(settings.header)
        {
            case "Économies cette semaine":
				var Week = [];
				var Total = [];

				for(var i in data) {
					Week.push("Le " + data[i].Day);
					Total.push(data[i].Total);
				}

				var chartdata = {
					labels: Week,
					datasets : [
						{
							label: 'Economie cette semaine',
							backgroundColor: 'rgba(200, 200, 200, 0.75)',
							borderColor: 'rgba(200, 200, 200, 0.75)',
							hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
							hoverBorderColor: 'rgba(200, 200, 200, 1)',
							data: Total
						}
					]
					};
				break;
				
				
			case "Économies ce mois":
				var Month = [];
				var Total = [];

				for(var i in data) {
					Month.push("Semaine du " + data[i].Week);
					Total.push(data[i].Total);
				}

				var chartdata = {
					labels: Month,
					datasets : [
						{
							label: 'Economie ce mois',
							backgroundColor: 'rgba(200, 200, 200, 0.75)',
							borderColor: 'rgba(200, 200, 200, 0.75)',
							hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
							hoverBorderColor: 'rgba(200, 200, 200, 1)',
							data: Total
						}
					]
					};
				break;
				
			case "Économies cette année":
				var Year = [];
				var Total = [];

				for(var i in data) {
					Year.push("Mois: " + data[i].Month);
					Total.push(data[i].Total);
				}

				var chartdata = {
					labels: Year,
					datasets : [
						{
							label: 'Economies cette année',
							backgroundColor: 'rgba(200, 200, 200, 0.75)',
							borderColor: 'rgba(200, 200, 200, 0.75)',
							hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
							hoverBorderColor: 'rgba(200, 200, 200, 1)',
							data: Total
						}
					]
					};
				break;
			default:
					//do nothing
				break;
				
		}
			
		
        
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
			onComplete : function()
			{
				// Quand le pop up s'affiche
				var ctx = $("#mycanvas");

				var barGraph = new Chart(ctx, {
				type: 'bar',
				data: chartdata,
				
				options: {
                        title: {
                          display: false
                        },
                        scales: {
                          yAxes: [{
                          	ticks: {
                            	beginAtZero: true
                            }
                          }]
                        }
                      }
				
				
			});	
			},
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
