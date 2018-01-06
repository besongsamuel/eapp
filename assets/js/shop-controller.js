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
    
    /**
     * This variable is true when a store is selected. 
     */
    $scope.isStoreSelected = false;
   
    $scope.productsReady = false;
    
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
                $scope.isStoreSelected = true;
            }

            // We selected a specific category
            if(window.sessionStorage.getItem("category_id"))
            {
                $scope.category_id = parseInt(window.sessionStorage.getItem("category_id"));
                $scope.category_name = window.sessionStorage.getItem("category_name");
            }
            
            $rootScope.isSearch = true;
            $scope.getProducts();
        }
    };

    $rootScope.add_product_to_cart = function(product_id, store_product_id = -1, product_quantity = 1)
    {
        if(typeof store_product_id === 'undefined')
        {
            store_product_id = -1;
        }
        
        var addToCartPromise = eapp.addToCart(
                product_id, 
                store_product_id, 
                $rootScope.isUserLogged ? $rootScope.loggedUser.profile.longitude : $rootScope.longitude, 
                $rootScope.isUserLogged ? $rootScope.loggedUser.profile.latitude : $rootScope.latitude, 
                product_quantity);
        
        addToCartPromise.then(function(response)
        {
            if(Boolean(response.data.success))
            {
                var cart_item = 
                {
                    rowid : response.data.rowid,
                    store_product : response.data.store_product,
                    top_five_store_products : [],
                    quantity : product_quantity
                };

                if($rootScope.cart === null || typeof $rootScope.cart === 'undefined')
                {
                    $rootScope.cart = [];
                }

                $rootScope.cart.push(cart_item);
            }
        });
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
        $scope.productsReady = false;
        $scope.isStoreSelected = false;
        
        if(!$scope.ready)
        {
            return;
        }
                
        if(window.sessionStorage.getItem("filterSettings"))
        {
            
            $scope.filterSettings = JSON.parse(window.sessionStorage.getItem("filterSettings").toString());;
            
            // Get the filter from the current settings and checks if a store is selected
            $scope.createResultsFilter();
            
            $scope.productsReady = true;
        }
        
        var q = $q.defer();
        
        $scope.isStoreSelected = $scope.IsStoreSelected();
        
        if(!angular.isNullOrUndefined($scope.store_id))
        {
            $scope.promise = eapp.getFlyerProducts($scope.store_id, $scope.query, $scope.resultFilter);
        }
        else if(!angular.isNullOrUndefined($scope.category_id))
        {
            $scope.promise = eapp.getCategoryProducts($scope.category_id, $scope.query, $scope.resultFilter);
        }
        else
        {
            $scope.promise = eapp.getStoreProducts($scope.query, $scope.resultFilter);
        }
      
        $scope.promise.then(function(response)
        {
            var array = $.map(response.data.products, function(value, index) {
                return [value];
            });

            $scope.count = response.data.count;
            $scope.products = array;
            
            var storeFilterSettings = $.map(response.data.settings.stores, function(value, index) {
                return [value];
            });
            var brandsFilterSettings = $.map(response.data.settings.brands, function(value, index) {
                return [value];
            });
            var categoriesFilterSettings = $.map(response.data.settings.categories, function(value, index) {
                return [value];
            });
            var originsFilterSettings = $.map(response.data.settings.origins, function(value, index) {
                return [value];
            });
            
            $scope.filterSettings = 
            {
                stores : storeFilterSettings,
                brands : brandsFilterSettings,
                categories : categoriesFilterSettings,
                origins : originsFilterSettings
            };

            window.sessionStorage.setItem("filterSettings", JSON.stringify($scope.filterSettings));
            
            
            $scope.hasResults = array.length > 0;
            
            q.resolve( array );
            
            $scope.productsReady = true;

        });
	
        return q.promise;
  };
  
    $scope.IsStoreSelected = function()
    {
        if(!angular.isNullOrUndefined($scope.store_id))
        {
            return true;
        }
        
        if(!angular.isNullOrUndefined($scope.filterSettings))
        {
            for(var x in $scope.filterSettings.stores)
            {
                var store = $scope.filterSettings.stores[x];

                if(store.selected)
                {
                    return true;
                }
            }
        }
        
        return false;
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
    
    $scope.settingsChanged = function(item)
    {
        $scope.updateItemChanged(item);
        
        window.sessionStorage.setItem("filterSettings", JSON.stringify($scope.filterSettings));
        
        // Get store filter
        $scope.createResultsFilter();
        
        $scope.getProducts();
        
    };
    
    $scope.updateItemChanged = function(item)
    {
        switch(item.type)
        {
            case "ORIGIN":
                var index = $scope.filterSettings.origins.map(function(e){ return e.name; }).indexOf(item.name);
                if(index > -1)
                {
                    $scope.filterSettings.origins[index] = item;
                }
                break;
            case "STORE":
                var index = $scope.filterSettings.stores.map(function(e){ return e.id; }).indexOf(item.id);
                if(index > -1)
                {
                    $scope.filterSettings.stores[index] = item;
                    $scope.isStoreSelected = true;
                }
                break;
            case "CATEGORY":
                var index = $scope.filterSettings.categories.map(function(e){ return e.id; }).indexOf(item.id);
                if(index > -1)
                {
                    $scope.filterSettings.categories[index] = item;
                }
                break;
            case "BRAND":
                var index = $scope.filterSettings.brands.map(function(e){ return e.id; }).indexOf(item.id);
                if(index > -1)
                {
                    $scope.filterSettings.brands[index] = item;
                }
                break;
        }
    };
    
    $scope.createResultsFilter = function()
    {
        if(angular.isNullOrUndefined($scope.filterSettings))
        {
            return;
        }
        
        $scope.resultFilter = { };
        
        var storeFilter = "";
        for(var x in $scope.filterSettings.stores)
        {
            var store = $scope.filterSettings.stores[x];
            
            if(store.selected)
            {
                if(storeFilter === "")
                {
                    storeFilter = storeFilter.concat(store.id.toString());
                }
                else
                {
                    storeFilter = storeFilter.concat(",", store.id.toString());
                }
            }
        }
        
        $scope.resultFilter.stores = storeFilter;
        
        // Get Category filter
        var categoryFilter = "";
        
        for(var x in $scope.filterSettings.categories)
        {
            var category = $scope.filterSettings.categories[x];
            
            if(category.selected)
            {
                if(categoryFilter === "")
                {
                    categoryFilter = categoryFilter.concat(category.id.toString());
                }
                else
                {
                    categoryFilter = categoryFilter.concat(",", category.id.toString());
                }
            }
        }
        
        $scope.resultFilter.categories = categoryFilter;
        
        // Get Brands filter
        var brandFilter = "";
        
        for(var x in $scope.filterSettings.brands)
        {
            var brand = $scope.filterSettings.brands[x];
            
            if(brand.selected)
            {
                if(brandFilter === "")
                {
                    brandFilter = brandFilter.concat(brand.id.toString());
                }
                else
                {
                    brandFilter = brandFilter.concat(",", brand.id.toString());
                }
            }
        }
        
        $scope.resultFilter.brands = brandFilter;
        
        // Get Brands filter
        var originsFilter = "";
        
        for(var x in $scope.filterSettings.origins)
        {
            var origin = $scope.filterSettings.origins[x];
            
            if(origin.selected)
            {
                var nameval = origin.name.toString();
                
                if(nameval == "Autre")
                {
                    nameval = "";
                }
                
                if(nameval == "Pas connu")
                {
                    nameval = "undefined";
                }
                
                if(originsFilter === "")
                {
                    originsFilter = originsFilter.concat(nameval);
                }
                else
                {
                    originsFilter = originsFilter.concat(",", nameval);
                }
            }
        }
        
        $scope.resultFilter.origins = originsFilter;
    };
    
    $scope.$watch("query.page", function(newValue, oldValue)
    {
        window.scrollTo(0, 0);
    });
    
  
}]);
