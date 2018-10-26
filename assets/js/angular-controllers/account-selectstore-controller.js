angular.module('eappApp').controller('SelectAccountStoreController', ["$scope", "$mdDialog", "eapp", "$rootScope", function ($scope, $mdDialog, eapp, $rootScope) 
{
    $rootScope.isAccountMenu = true;
    
    $scope.max_stores = 4;
	
    $scope.selected_retailers = [];
    
    $scope.my_retailers = [];
	
    $scope.favoriteStores = [];
    
    $scope.clickedElement = null;
    
    $scope.Init = function()
    {
        var favoriteStoresPromise = eapp.getFavoriteStores();
            
        favoriteStoresPromise.then(function(response)
        {
            $scope.favoriteStores = $.map(response.data, function(value, index) {
                return [value];
            });
        });
        
        var retailersPromise = eapp.getRetailers();
        
        retailersPromise.then(function(response)
        {
            $scope.retailers = $.map(response.data, function(value, index) {
                return [value];
            });
        });
        
    };
    
    $scope.goto_retailer = function(id)
    {
        $scope.clearSessionItems();  
	var store_id = parseInt(id);
	window.sessionStorage.setItem("store_id", store_id);    
	window.location =  $scope.site_url.concat("/shop");
    };
    
    $scope.removeRetailer = function(ev, index)
    {
        var confirmDialog = eapp.createConfirmDialog(ev, "Êtes-vous sûr de vouloir supprimer le magasin?");
        
        $mdDialog.show(confirmDialog).then(function() 
        {
            $scope.favoriteStores[index] = { id : -1};
                        
            
            var arrayToSave = [];
            
            for(var x in $scope.favoriteStores)
            {
                arrayToSave.push($scope.favoriteStores[x].id);
            }
            
            eapp.saveFavoriteStores(JSON.stringify(arrayToSave)).then(function()
            {
                var favoriteStoresPromise = eapp.getFavoriteStores();
                favoriteStoresPromise.then(function(response)
                {
                    $scope.favoriteStores = $.map(response.data, function(value, index) {
                        return [value];
                    });
                });
            });
        });
            
    };
	
    $scope.setRetailer = function(ev, index)
    {
        var scrollTop = $(document).scrollTop();
            
        $mdDialog.show({
            controller: DialogController,
            templateUrl: 'templates/components/selectUserFavoriteStore.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            locals : 
            {
                retailers : $scope.retailers,
                favoriteStores : $scope.favoriteStores,
                storeIndex : index
            },
            clickOutsideToClose:true,
            fullscreen: true
        })
        .then(function(answer) 
        {
            var favoriteStoresPromise = eapp.getFavoriteStores();
            
            favoriteStoresPromise.then(function(response)
            {
                $scope.favoriteStores = response.data;
                $(document).scrollTop(scrollTop);
            });
            
            
        }, function() 
        {
            $(document).scrollTop(scrollTop);
        });
    };
        
    function DialogController($scope, $mdDialog, retailers, favoriteStores, eapp, storeIndex) 
    {
        
        var ctrl = this;
        
        $scope.storeName = "";
        
        ctrl.retailers = retailers;
        
        $scope.retailers = retailers;
        
        for(var x in favoriteStores)
        {
            if(parseInt(favoriteStores[x].id) > -1)
            {
                var index = $scope.retailers.map(function(e){ return e.id; }).indexOf(favoriteStores[x].id);
                
                if(index > -1)
                {
                    $scope.retailers.splice(index, 1);
                }
            }
        }
        
        ctrl.isFavoriteStore = function(store)
        {
            for(var x in favoriteStores)
            {
                if(parseInt(favoriteStores[x].id) > -1)
                {

                    if(parseInt(store.id) === parseInt(favoriteStores[x].id))
                    {
                        return true;
                    }
                }
            }
            
            return false;
        };

        $scope.hide = function() {
          $mdDialog.hide();
        };

        $scope.cancel = function() {
          $mdDialog.cancel();
        };
        
        $scope.selectStore = function(item)
        {
            var myStores = favoriteStores;
            
            myStores[storeIndex] = item;
            
            var arrayToSave = [];
            
            for(var x in myStores)
            {
                arrayToSave.push(myStores[x].id);
            }
            
            eapp.saveFavoriteStores(JSON.stringify(arrayToSave)).then(function()
            {
                $mdDialog.hide(item);
            });
            
            
        };
        
        $scope.search = function()
        {
            $scope.retailers = ctrl.retailers.filter(x => x.name.toLowerCase().indexOf($scope.storeName.toLowerCase()) !== -1 && !ctrl.isFavoriteStore(x));
        };

    }
    
    
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
  
}]);
