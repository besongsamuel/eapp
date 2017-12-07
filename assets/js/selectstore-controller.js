angular.module('eappApp').controller('SelectStoreController', ["$scope", "$mdDialog", "eapp", "$rootScope", function ($scope, $mdDialog, eapp, $rootScope) 
{
    $rootScope.isMainMenu = true;
    
    $scope.loading = false;
    
    $scope.Init = function()
    {
        var retailersPromise = eapp.getCloseRetailers($scope.getDistance());
        
        $scope.loading = true;
        
        retailersPromise.then(function(response)
        {
            $scope.retailers = response.data;
            
            $scope.loading = false;
        });
    };
    
    $scope.getDistance = function()
    {
        if($scope.isUserLogged)
        {
            return parseInt($scope.loggedUser.profile.optimization_distance);
        }
        else if(window.localStorage.getItem('optimization_distance'))
        {
            return parseInt(window.localStorage.getItem('optimization_distance'));
        }
        else
        {
            // return a default distance
            return 4;
        }
    };
    
    $scope.changeDistance = function(ev)
    {
        $scope.default_distance = $scope.getDistance();
        $scope.scrollTop = $(document).scrollTop();
        $mdDialog.show({
            controller: ChangeDistanceController,
            templateUrl:  $scope.base_url + 'assets/templates/change-distance.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose:true,
            preserveScope:true,
            scope : $scope,
            fullscreen: true,
            onRemoving : function()
            {
                // Restore scroll
                $(document).scrollTop($scope.scrollTop);
            }
          })
          .then(function(answer) {
                
          }, function() {
                
          });
    };
    
    function ChangeDistanceController($scope, $mdDialog) 
    {
        $scope.hide = function() 
        {
            $mdDialog.hide();
        };

        $scope.cancel = function() 
        {
            $mdDialog.cancel();
        };
        
        $scope.change = function()
        {
            if($scope.isUserLogged)
            {
                var changePromise = eapp.changeDistance('optimization_distance', $scope.default_distance);
            
                changePromise.then(function(response)
                {
                    if(response.data)
                    {
                        // Update Logged User
                        $scope.loggedUser = response.data;
                        
                        $scope.Init();
                    }
                });
            }
            else
            {
                // Change in the session
                window.localStorage.setItem('optimization_distance', $scope.default_distance);
            }
            
            $mdDialog.cancel();
        };
    };
    
    $scope.select_retailer = function($event, store)
    {
        $scope.clearSessionItems();  
	var store_id = parseInt(store.id);
	window.sessionStorage.setItem("store_id", store_id); 
        window.sessionStorage.setItem("store_name", store.name); 
	window.location =  $scope.site_url.concat("/shop");
    };
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
  
}]);
