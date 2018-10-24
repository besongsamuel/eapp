angular.module('eappApp').controller('ShopController', ["$scope", "$q", "$mdDialog", "$rootScope", "eapp", "profileData", function ($scope, $q, $mdDialog, $rootScope, eapp, profileData) 
{
    $rootScope.query = 
    {
        filter: '',
        limit: '100',
        order: 'name',
        page: 1
    };
 
    $rootScope.searchText = "";
    
    $scope.ready = false;
    
    $scope.isLoading = false;
    
    $scope.isInitialized = false;
    
    $scope.root = $rootScope;
        
    var ctrl = this;
    
    /**
     * This variable is true when a store is selected. 
     */
    $scope.isStoreSelected = false;
       
    $scope.productsReady = false;
    
    angular.element(document).ready(function()
    {
        if(!$scope.ready)
        {
            $scope.Init();
            $scope.ready = true;
        }
    });
    var bookmark;
    
    $scope.Init = function()
    {
        $scope.assets_dir = $scope.base_url.concat("/eapp/assets/");
        
        if($(window).width() < 500)
        {
            profileData.get().gridView = true;
        }
        
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
            $scope.isInitialized = true;
        }
        
        $scope.distance =  profileData.get().optimizationDistance;
                
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
                    quantity : product_quantity,
                    store_product_id : store_product_id
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
        $scope.isLoading = true;
        
        if(!$scope.ready || !$scope.isInitialized)
        {
            return;
        }
                
        if(profileData.get().filterSettings)
        {
            $scope.filterSettings = profileData.get().filterSettings;
            // Get the filter from the current settings and checks if a store is selected
            $scope.createResultsFilter();
            $scope.productsReady = true;
        }
            
        $scope.isStoreSelected = profileData.get().viewAll;
            
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
            
            $scope.maxPageItem = Math.min(parseInt(($scope.query.page * $scope.query.limit)), parseInt($scope.count));
            
            $scope.products = array;
            
            $scope.filterSettings = response.data.settings;
            
            profileData.set("filterSettings", response.data.settings);
            
            $scope.hasResults = array.length > 0;
            
            q.resolve( array );
            
            $scope.productsReady = true;
            
            $scope.isLoading = false;

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
            for(var x in $scope.filterSettings.stores.values)
            {
                var store = $scope.filterSettings.stores.values[x];

                if(store.selected)
                {
                    return true;
                }
            }
        }
        
        if(profileData.get().viewAll)
        {
            return true;
        }
        
        return false;
    };
  
  
    ctrl.oldValue = null;
    
    $scope.search = function()
    {
        if(!ctrl.oldValue) 
        {
            bookmark = $scope.query.page;
        }

        if($scope.query.filter !== ctrl.oldValue) 
        {
            $scope.query.page = 1;
        }

        if(!ctrl.oldValue) 
        {
            $scope.query.page = bookmark;
        }
        
        
        $scope.getProducts();
    };
    
    
    $scope.refresh = function(viewConfig)
    {
        profileData.set('viewAll', viewConfig.viewAll);
        
        if(profileData.get().viewAll)
        {
            $scope.isStoreSelected = true;
        }
        else
        {
            $scope.isStoreSelected = $scope.IsStoreSelected();
        }
	    
        $scope.getProducts();
    };
  	
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
        
        profileData.set("filterSettings", $scope.filterSettings);
        
        // Get store filter
        $scope.createResultsFilter();
        
        $scope.getProducts();
        
    };
    
    $scope.updateItemChanged = function(item)
    {
        var index = $scope.filterSettings[item.type].values.map(function(e){ return e.name; }).indexOf(item.name);
        
        if(index > -1)
        {
            $scope.filterSettings[item.type].values[index] = item;
        }
    };
    
    $scope.createResultsFilter = function()
    {
        if(angular.isNullOrUndefined($scope.filterSettings))
        {
            return;
        }
        
        $scope.resultFilter = {};
        
        for(var x in $scope.filterSettings)
        {
            var values = $scope.filterSettings[x].values;
            var setting = $scope.filterSettings[x].setting;
            var filter = "";
            for(var y in values)
            {
                if(values[y].selected)
                {
                    var value = values[y].id.toString();
                    
                    if(value === "Autre")
                    {
                        value = "";
                    }
                    
                    if(filter === "")
                    {
                        if(value === "")
                        {
                            filter = filter.concat(",", value);
                        }
                        else
                        {
                            filter = filter.concat("", value);
                        }
                    }
                    else
                    {
                        filter = filter.concat(",", value);
                    }
                    
                    
                }
            }
            
            $scope.resultFilter[setting.name] = filter;
        }
        
    };
    
    $scope.changeDistance = function(newDistance)
    {
        profileData.set("optimizationDistance", newDistance);
        $scope.getProducts();
    };
    
    ctrl.select_category = function($event, category)
    {
        $scope.clearSessionItems();
        var category_id = parseInt(category.id);
        eapp.recordHit("eapp_product_category ",category_id);
        window.sessionStorage.setItem("category_id", category_id);    
        window.sessionStorage.setItem("category_name", category.name);
        window.location =  $scope.site_url.concat("/shop");
    };
    
    ctrl.select_json_category = function($event, category)
    {
        $rootScope.select_category($event, JSON.parse(category));
    };
    
    $scope.viewChanged = function(gridView)
    {
        profileData.set('gridView', gridView);
        $scope.profileData = profileData;
    };
    
    $scope.$watch("query.page", function(newValue, oldValue)
    {
        window.scrollTo(0, 0);
    });
}]);
