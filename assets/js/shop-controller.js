angular.module('eappApp').controller('ShopController', ["$scope", "$q", "$http", "$mdDialog", "$rootScope", "eapp", function ($scope, $q, $http, $mdDialog, $rootScope, eapp) 
{
 
 
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
    var bookmark;
    
    $scope.assets_dir = "http://" + window.location.hostname + "/eapp/assets/";
    
    $scope.Init = function()
    {
        $rootScope.searchText = "";
        
        if(window.sessionStorage.getItem("searchText"))
        {
            $rootScope.searchText = window.sessionStorage.getItem("searchText");
            window.sessionStorage.removeItem("searchText");
            $scope.query.filter = $scope.searchText;
        }
        
        // Get the products for the store
        if($scope.controller === 'shop')
        {
            // We selected a specific store flyer
            if(window.sessionStorage.getItem("store_id"))
            {
                $scope.store_id = parseInt(window.sessionStorage.getItem("store_id"));
            }

            // We selected a specific category
            if(window.sessionStorage.getItem("category_id"))
            {
                $scope.category_id = parseInt(window.sessionStorage.getItem("category_id"));
            }
            
            $rootScope.isSearch = true;
            $scope.getProducts();
        }
    };

    $scope.add_to_cart = function(product_id) 
    {
        $scope.addProductToCart(product_id);
    };
    
    $scope.remove_from_cart = function(product_id)
    {
        $scope.removeItemFromCart(product_id);
    };

    $scope.selected = [];
  
    $scope.filter = 
    {
        options: 
        {
            debounce: 500
        }
    };

    $scope.query = 
    {
        filter: '',
        limit: '50',
        order: 'name',
        page: 1
    };
  
    $scope.getProducts = function () 
    {
        
        var q = $q.defer();

        if(!angular.isNullOrUndefined($scope.store_id))
        {
            $scope.promise = eapp.getFlyerProducts($scope.store_id, $scope.query);
        }
        else if(!angular.isNullOrUndefined($scope.category_id))
        {
            $scope.promise = eapp.getCategoryProducts($scope.category_id, $scope.query);
        }
        else
        {
            $scope.promise = eapp.getStoreProducts($scope.query);
        }
      
        $scope.promise.then(function(response)
        {
            var array = $.map(response.data.products, function(value, index) {
                return [value];
            });

            $scope.count = response.data.count;
            $scope.products = array;
            q.resolve( array );

        });
	
        return q.promise;
  };
  
    $scope.removeFilter = function () 
    {
        $scope.filter.show = false;
        $scope.query.filter = '';

        if($scope.filter.form.$dirty) 
        {
          $scope.filter.form.$setPristine();
        }
    };
  
    $scope.$watch('query.filter', function (newValue, oldValue) 
    {
        if(!oldValue) 
        {
            bookmark = $scope.query.page;
        }

        if(newValue !== oldValue) 
        {
            $scope.query.page = 1;
        }

        if(!newValue) 
        {
            $scope.query.page = bookmark;
        }

        $scope.getProducts();
    });
  	
    $scope.searchProducts = function(searchText)
    {
        $scope.clearSessionItems();
        window.sessionStorage.setItem("searchText", searchText);
        window.location.href =  $scope.site_url.concat("/shop");
    };
  
}]);
