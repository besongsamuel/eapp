angular.module('eappApp').controller('SelectStoreController', function ($scope, $mdDialog, eapp, appService, profileData) 
{
    $scope.loading = false;
    
    var ctrl = this;
        
    $scope.storeName = "";
    
    $scope.storesAvailable = false;
    
    $scope.Init = function()
    {
        $scope.storeName = "";
        
        var retailersPromise = eapp.getCloseRetailers($scope.getDistance());
        
        $scope.loading = true;
        
        retailersPromise.then(function(response)
        {
            $scope.retailers = Object.values(response.data);
            
            $scope.storesAvailable = $scope.retailers.length > 0;
            
            ctrl.retailers = $scope.retailers;
            
            $scope.loading = false;
        });
    };
    
    $scope.getDistance = function()
    {
        if($scope.isUserLogged)
        {
            return parseInt(appService.loggedUser.profile.optimization_distance);
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
            templateUrl:  appService.baseUrl + 'assets/templates/change-distance.html',
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
            if(appService.isUserLogged)
            {
                var changePromise = eapp.changeDistance('optimization_distance', $scope.default_distance);
            
                changePromise.then(function(response)
                {
                    if(response.data)
                    {
                        // Update Logged User
                        appService.loggedUser = response.data;
                        
                        $scope.Init();
                    }
                });
            }
            else
            {
                // Change in the session
                window.localStorage.setItem('optimization_distance', $scope.default_distance);
                
                $scope.Init();
            }
            
            $mdDialog.cancel();
        };
    };
    
    $scope.select_retailer = function($event, store)
    {
        appService.clearSessionItems();  
	var store_id = parseInt(store.id);
        appService.recordRetailerHit(store.id, profileData.instance.cartDistance);
	window.sessionStorage.setItem("store_id", store_id); 
        window.sessionStorage.setItem("store_name", store.name); 
	window.location =  $scope.site_url.concat("/shop");
    };
    
    $scope.search = function()
    {
        $scope.retailers = ctrl.retailers.filter(x => x.name.toLowerCase().indexOf($scope.storeName.toLowerCase()) !== -1);
    };
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
  
});
