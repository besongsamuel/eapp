angular.module('eappApp').controller('ShopController', ["$scope", "$q", "$http", "$mdDialog", "$rootScope", "eapp", function ($scope, $q, $http, $mdDialog, $rootScope, eapp) 
{
    $rootScope.query = 
    {
        filter: '',
        limit: '50',
        order: 'name',
        page: 1
    };
 
    $rootScope.searchText = "";
    
    $scope.ready = false;
    
    $scope.showStoreLogo = false;

    angular.element(document).ready(function()
    {
        $scope.Init();
        
        $scope.ready = true;
    });
    var bookmark;
    
    $scope.Init = function()
    {
        $scope.assets_dir = $scope.base_url.concat("/eapp/assets/");
        
        if(window.sessionStorage.getItem("searchText"))
        {
            $rootScope.searchText = window.sessionStorage.getItem("searchText");
            window.sessionStorage.removeItem("searchText");
            $rootScope.query.filter = $rootScope.searchText;
        }
                
        // Get the products for the store
        if($scope.controller === 'shop')
        {
            // We selected a specific store flyer
            if(window.sessionStorage.getItem("store_id"))
            {
                $scope.store_id = parseInt(window.sessionStorage.getItem("store_id"));
                $scope.store_name = window.sessionStorage.getItem("store_name");
                $scope.showStoreLogo = true;
            }

            // We selected a specific category
            if(window.sessionStorage.getItem("category_id"))
            {
                $scope.category_id = parseInt(window.sessionStorage.getItem("category_id"));
                $scope.category_name = window.sessionStorage.getItem("category_name");
                $scope.showStoreLogo = true;
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

    $scope.getProducts = function () 
    {
        if(!$scope.ready)
        {
            return;
        }
        
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
    
    $rootScope.select_category = function($event, category)
    {
        $scope.clearSessionItems();
        var category_id = parseInt(category.id);
        eapp.recordHit("eapp_product_category ",category_id);
        window.sessionStorage.setItem("category_id", category_id);    
        window.sessionStorage.setItem("category_name", category.name);
        window.location =  $scope.site_url.concat("/shop");
    };
    
    $scope.viewProduct = function(product_id, ev)
    {
        eapp.viewProduct($scope, product_id, ev);
    };
  
}]);
