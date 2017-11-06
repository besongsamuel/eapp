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
        var retailersPromise = eapp.getRetailers();
        
        retailersPromise.then(function(response)
        {
            $scope.retailers = response.data;
            
            $scope.$watch("my_retailers", function(newValue, oldValue)
            {		
                $scope.favoriteStores = [];

                for(var x in $scope.my_retailers)
                {
                    var retailer_id = parseInt($scope.my_retailers[x]);

                    $scope.favoriteStores.push($scope.retailers[retailer_id]);

                }

            });
            
            var favoriteStoresPromise = eapp.getFavoriteStores();
            
            favoriteStoresPromise.then(function(response)
            {
                $scope.my_retailers = response.data;
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
	
    $scope.select_retailer = function($event)
    {
        var element = $event.target;
        
        $scope.clickedElement = element;
        
        $scope.listChangedSuccess = false;
        
        if($(element).hasClass( "check" ))
        {
            // Get the retailer ID
            var index = $scope.selected_retailers.indexOf(parseInt(element.id));
            
            if (index > -1) 
            {
                $scope.selected_retailers.splice(index, 1);
            }
            
            $(element).toggleClass("check");
        }
        else
        {
            if($scope.selected_retailers.length < $scope.max_stores)
            {
                $scope.selected_retailers.push(parseInt(element.id));
                $(element).toggleClass("check");
            }
            else
            {
                $scope.showAlert($event, "Message", "Vous ne pouvez pas sélectionner plus de "+$scope.max_stores+" magasins. Veillez désélectionner certains magasins ou cliquer sur recommencer. ");
            }
        }
    };
    
    $scope.saveFavoriteStores = function()
    {
        $scope.listChangedSuccess = false;
        if($scope.selected_retailers.length < $scope.max_stores)
        {
            $scope.showSimpleToast("Vous devez sélectionner au moins "+$scope.max_stores+" magasins.", "select-store-box");
        }
        else
        {

            var saveFavoriteStoresPromise = eapp.saveFavoriteStores(JSON.stringify($scope.selected_retailers));
            // Send request to server to get optimized list 	
            saveFavoriteStoresPromise.then(
            function(response)
            {
                if(response.data.success)
                {
                    $scope.my_retailers = $scope.selected_retailers;
                    $scope.selected_retailers = [];
                    $scope.listChangedSuccess = true;
                    $scope.listChangedSuccessMessage = "Votre liste de magasins a été modifiée.";
                    $(".check").toggleClass("check");
                }
                else
                {
                    $scope.showSimpleToast("une erreur inattendue est apparue. Veuillez réessayer plus tard.", "select-store-box");
                }
            });
        }
    };
    
    $scope.showAlert = function(ev, title, message) 
    {
        // Appending dialog to document.body to cover sidenav in docs app
        // Modal dialogs should fully cover application
        // to prevent interaction outside of dialog
        $mdDialog.show(
          $mdDialog.alert()
                .parent(angular.element(document.querySelector('#popupContainer')))
                .clickOutsideToClose(true)
                .title(title)
                .textContent(message)
                .ariaLabel('Alert')
                .ok('Ok')
                .targetEvent(ev)
        );
    };
    
    $scope.reset = function()
    {
        $scope.listChangedSuccess = false;
        $scope.selected_retailers = [];
        $(".check").toggleClass("check");
    };
    
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    
  
}]);
