/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('eappApp').component('cartListItem', {
    
    templateUrl: 'cartListItem.html',
    controller : CartListItemController,
    bindings: 
    {
        item: '<',
        iscartview : '<',
        onDelete: '&',
        onUpdate: '&',
        onUpdateQuantity: '&',
        viewRetailerImage: '@'
    }
});

function CartListItemController($scope, $rootScope, eapp, $mdDialog)
{
    var ctrl = this;
    
    ctrl.$onInit = function()
    {
        ctrl.isUserLogged = $rootScope.isUserLogged;
        
        ctrl.errorMargin = 10;
        
        ctrl.viewImage = angular.isNullOrUndefined(ctrl.viewRetailerImage) ? false : ctrl.viewRetailerImage == "true";
    };
    
    ctrl.delete = function()
    {
        ctrl.onDelete({id: ctrl.item.store_product.product.id});
    };
    
    ctrl.updateQuantity = function(quantity)
    {
        ctrl.onUpdateQuantity({ quantity : quantity, id : ctrl.item.store_product.product.id });
    };
    
    ctrl.update = function()
    {
        ctrl.onUpdate({item: ctrl.item});
    };
    
    ctrl.favoriteChanged = function(product)
    {
        if(product.in_user_grocery_list)
        {
            eapp.addProductToList(product);
        }
        else
        {
            eapp.removeProductFromList();
        }
    };
    
    ctrl.viewProduct = function(product_id, ev)
    {
        eapp.viewProduct($scope, product_id, ev);
    };
    
    ctrl.getCartItemRebate = function(cart_item)
    {
        if(typeof cart_item.store_product.worst_product === "undefined" || cart_item.store_product.worst_product === null)
        {
            return 0;
        }
        
        var rebate =  (parseFloat(cart_item.store_product.worst_product.unit_price) - parseFloat(cart_item.store_product.unit_price));
        
        if(rebate > ctrl.errorMargin)
        {
            rebate = 0;
        }
        
        return rebate;
    };
    
    ctrl.getRelatedProducts = function(store_product)
    {
        var results = [];
        // split related products to store related and format related
        var different_format_products = [];
        var different_store_products = [];

        for(var i in store_product.related_products)
        {
            if(parseInt(store_product.retailer.id) !== parseInt(store_product.related_products[i].retailer.id))
            {
                different_store_products.push(store_product.related_products[i]);
            }
            
            if(store_product.format.toString().trim() !== store_product.related_products[i].format.toString().trim()
                    && parseInt(store_product.retailer.id) === parseInt(store_product.related_products[i].retailer.id))
            {
                different_format_products.push(store_product.related_products[i]);
            }
        }
        
        // Sort them in ascending order
        different_store_products.sort(function(a, b)
        {
            if(parseFloat(a.compare_unit_price) < parseFloat(b.compare_unit_price))
            {
                return -1;
            }
            
            if(parseFloat(a.compare_unit_price) > parseFloat(b.compare_unit_price))
            {
                return 1;
            }
            
            return 0;
            
        });
        
        // Sort them in ascending order
        different_format_products.sort(function(a, b)
        {
            if(parseFloat(a.compare_unit_price) < parseFloat(b.compare_unit_price))
            {
                return -1;
            }
            
            if(parseFloat(a.compare_unit_price) > parseFloat(b.compare_unit_price))
            {
                return 1;
            }
            
            return 0;
            
        });

        results.push(different_store_products);
        results.push(different_format_products);

        return results;

    };
    
    /**
     * Callback when the user wants to change the store
     * of a given store product. 
     * @param {type} ev
     * @param {type} currentStoreProduct
     * @returns {undefined}
     */    
    ctrl.changeProductStore = function(ev, cartItem)
    {
        // The currentlu selected store product. 
        $scope.selectedStoreProduct = cartItem.store_product;
        $scope.different_store_products = cartItem.different_store_products;
        $scope.related_products = cartItem.store_product.related_products;
        $scope.scrollTop = $(document).scrollTop();
        
        // Show dialog for user to change the store of the product. 
        $mdDialog.show({
            controller: ChangeStoreController,
            templateUrl:  $rootScope.base_url + 'assets/templates/change-store-product.html',
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
    
    function ChangeStoreController($scope, $mdDialog) 
    {
        $scope.hide = function() 
        {
            $mdDialog.hide();
        };

        $scope.cancel = function() 
        {
            $mdDialog.cancel();
        };
        
        $scope.change = function(sp)
        {
            // Get reference to in_user_grocery_list
            var in_user_grocery_list = $scope.selectedStoreProduct.product.in_user_grocery_list;
            // Assign references to the newly selected product
            $scope.selectedStoreProduct = sp;
            // Assign to the selected product the related products. 
            $scope.selectedStoreProduct.related_products = $scope.related_products;
            // Recompute format and store products
            var relatedProducts = ctrl.getRelatedProducts($scope.selectedStoreProduct);
            $scope.selectedStoreProduct.different_store_products = relatedProducts[0];
            $scope.selectedStoreProduct.different_format_products = relatedProducts[1];
            $scope.selectedStoreProduct.product.in_user_grocery_list = in_user_grocery_list;
        };

        $scope.selectStoreProduct = function() 
        {
            var currentStoreProduct = $scope.selectedStoreProduct;
            ctrl.onUpdate({sp: currentStoreProduct});
            $mdDialog.hide();
        };
    };
    
    /**
     * This is the callback when the user desires to
     * change the products format
     * @param {type} ev
     * @param {type} currentStoreProduct
     * @returns {undefined}
     */
    ctrl.changeProductFormat = function(ev, cartItem)
    {
        $scope.selectedStoreProduct = cartItem.store_product;
	$scope.different_format_products = cartItem.different_format_products;
        $scope.related_products = cartItem.store_product.related_products;
        $scope.scrollTop = $(document).scrollTop();
        
        $mdDialog.show({
            controller: ChangeFormatController,
            templateUrl:  $rootScope.base_url + 'assets/templates/change-format-product.html',
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
          .then(function(answer) {
                
          }, function() {
                
          });
    };
    
    function ChangeFormatController($scope, $mdDialog) 
    {
        $scope.hide = function() 
        {
            $mdDialog.hide();
        };

        $scope.cancel = function() 
        {
            $mdDialog.cancel();
        };
        
        /**
         * This is called when a user selects a different product
         * @param {type} sp
         * @returns {undefined}
         */
        $scope.change = function(sp)
        {
            // Get the value indicating if the product is in the user's grocery list
            var in_user_grocery_list = $scope.selectedStoreProduct.product.in_user_grocery_list;
            // Set this store product as the selected product
            $scope.selectedStoreProduct = sp;
            // Assign the related products 
            $scope.selectedStoreProduct.related_products = $scope.related_products;
            var relatedProducts = ctrl.getRelatedProducts($scope.selectedStoreProduct);
            $scope.selectedStoreProduct.different_store_products = relatedProducts[0];
            $scope.selectedStoreProduct.different_format_products = relatedProducts[1];
            
            $scope.selectedStoreProduct.product.in_user_grocery_list = in_user_grocery_list;
        };

        $scope.selectStoreProduct = function() 
        {
            var currentStoreProduct = $scope.selectedStoreProduct;
            ctrl.onUpdate({sp: currentStoreProduct});
            $mdDialog.hide();
        };
    };
}

angular.module("eappApp").controller("CartController", ["$scope","$rootScope", "$http", "$mdDialog","eapp", function($scope, $rootScope, $http, $mdDialog, eapp) 
{
    var ctrl = this;
    
    $scope.root = $rootScope;
    
    $scope.errorMargin = 10;
    
    /**
     * List of selected cart items. 
     * With this list we can batch remove cart items. 
     */
    $scope.selected = [];
    
    /**
     * The query object
     */
    $scope.query = 
    {
      order: 'nameToLower',
      limit: 5,
      page: 1
    };
        
    $rootScope.totalPriceAvailableProducts = 0;
    $rootScope.totalPriceUnavailableProducts = 0;
    
    /**
    * List of optimized cart store product items
    */
    $scope.optimized_cart = [];
    
    $scope.price_optimization = 0;
            
    $scope.min_price_optimization = 0;
    
    $scope.productCategories = [];
    
    $scope.$watch('min_price_optimization', function()
    {
        $scope.show_min_price_optimization = $scope.min_price_optimization > 0 && $scope.price_optimization > 0 && ($scope.price_optimization - $scope.min_price_optimization) > 0.1;
    });
       
    $scope.listChanged = function()
    {        
        $scope.optimization_preference_changed();
    };   
       
    /**
     * This method initializes the cart
     * @returns {undefined}
     */
    $scope.Init = function()
    {       
        $scope.default_distance = $scope.getDistance();
        
        if(window.sessionStorage.getItem('cartSettings'))
        {
            $rootScope.cartSettings = JSON.parse(window.sessionStorage.getItem('cartSettings').toString());
            
            if(!$scope.isUserLogged)
            {
                $rootScope.cartSettings.searchMyList = false;
            }
        }
        else
        {
            $rootScope.cartSettings = { cartView : true, optimizedCart : false, searchMyList : false };
        }
        
        window.sessionStorage.removeItem("cartFilterSettings");
        
        $scope.update_cart_list();
        
        if(!$scope.initialized)
        {
            $scope.initialized = true;
        }
        
    };
        
    $scope.getFormat = function(storeProduct)
    {
        var formatVal = 1;
        
        if(storeProduct.format === 'undefined' || storeProduct.format === null)
        {
            return 1;
        }
        
        var format = storeProduct.format.toLowerCase().split("x");
        
        formatVal = 1;
        
        if(format.length === 1)
        {
            formatVal = parseFloat(format[0]);
        }
        
        if(format.length === 2)
        {
            formatVal = parseFloat(format[0]) * parseFloat(format[1]);
        }
        
        return formatVal;
    };
    
    /**
     * Callback method when the user changes his optimization preference
     * @returns void
     */
    $scope.optimization_preference_changed = function()
    {
        $scope.update_cart_list();
    };
        
    $scope.true_value = true;
    $scope.false_value = false;
    
    ctrl.lastBatchIndex = 0;
    ctrl.BATCH_SIZE = 8;
    
    $scope.getDistance = function()
    {
        if($scope.isUserLogged)
        {
            return parseInt($scope.loggedUser.profile.cart_distance);
        }
        else if(window.localStorage.getItem('cart_distance'))
        {
            return parseInt(window.localStorage.getItem('cart_distance'));
        }
        else
        {
            // return a default distance
            return 4;
        }
    };
    
    /**
     * Updates the cart list by finding cheap products 
     * close to you
     * @returns {undefined}
     */
    $scope.update_cart_list = function()
    {
        // Clear items
        $scope.optimized_cart = [];        
        
        $scope.ready = false;
        
        if(window.sessionStorage.getItem("cartFilterSettings"))
        {
            var settings = window.sessionStorage.getItem("cartFilterSettings");
            
            if(!angular.isNullOrUndefined(settings))
            {
                $scope.cartFilterSettings = JSON.parse(settings.toString());
                
                $scope.createResultsFilter();
            
                $scope.ready = true;
            }
            else
            {
                window.sessionStorage.removeItem("cartFilterSettings");                
            }
        }
        	
        var formData = new FormData();
        formData.append("distance", $scope.getDistance());
        // User's longitude
        formData.append("longitude", $rootScope.longitude);
        // user's latitude
        formData.append("latitude", $rootScope.latitude);
        
        formData.append("searchAll", !$rootScope.cartSettings.searchMyList);
        formData.append("resultsFilter", JSON.stringify($scope.resultFilter));
        formData.append("viewOptimizedList", $rootScope.cartSettings.optimizedCart);
        
        // Send request to server to get optimized list 	
        $scope.promise = 
            $http.post( $scope.site_url.concat("/cart/update_cart_list"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                $scope.cartFilterSettings = response.data.cartFilterSettings;
                
                window.sessionStorage.setItem("cartFilterSettings", JSON.stringify($scope.cartFilterSettings));
                
                $rootScope.cart = [];
                
                // Create ordered array list
                for(var x in response.data.items)
                {
                    var cartItem = response.data.items[x];

                    if(angular.isNullOrUndefined(cartItem.store_product.related_products))
                    {
                        cartItem.store_product.related_products = [];
                    }
                    
                    var relatedProducts = $scope.getRelatedProducts(cartItem.store_product);
                    cartItem.different_store_products = relatedProducts[0];
                    cartItem.different_format_products = relatedProducts[1];
					
                    $rootScope.cart.push(cartItem);
                }
                
                $scope.results_available = !angular.isNullOrUndefined($scope.cart) && $scope.cart.length > 0;
                
                
                if($rootScope.cartSettings.cartView)
                {
                    $rootScope.sortCart();
                
                    // orders the stores and assigns the distance and times to each of the
                    // department stores
                    groupByStore();

                    $scope.update_price_optimization();
                }
                else
                {
                    // Get the stores that will be displayed
                    $scope.stores = $scope.getListByStore();

                    // Select the first store
                    if($scope.stores.length > 0)
                    {
                        $scope.storeTabSelected($scope.stores[0]);
                    }
                    
                    ctrl.lastBatchIndex = 0;
                    // Get the driving distances of each of the stores
                    $scope.getStoreDrivingDistances();
                }
                
                $scope.ready = true;
                
            });
    };
    
    $scope.createResultsFilter = function()
    {
        if(angular.isNullOrUndefined($scope.cartFilterSettings))
        {
            return;
        }
        
        $scope.resultFilter = {};
        
        for(var x in $scope.cartFilterSettings)
        {
            var values = $scope.cartFilterSettings[x].values;
            var setting = $scope.cartFilterSettings[x].setting;
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
    	
    $scope.getRelatedProducts = function(store_product)
    {
        var results = [];
        // split related products to store related and format related
        var different_format_products = [];
        var different_store_products = [];

        for(var i in store_product.related_products)
        {
            if(parseInt(store_product.retailer.id) !== parseInt(store_product.related_products[i].retailer.id))
            {
                different_store_products.push(store_product.related_products[i]);
            }
            
            if(store_product.format.toString().trim() !== store_product.related_products[i].format.toString().trim()
                    && parseInt(store_product.retailer.id) === parseInt(store_product.related_products[i].retailer.id))
            {
                different_format_products.push(store_product.related_products[i]);
            }
        }
        
        // Sort them in ascending order
        different_store_products.sort(function(a, b)
        {
            if(parseFloat(a.compare_unit_price) < parseFloat(b.compare_unit_price))
            {
                return -1;
            }
            
            if(parseFloat(a.compare_unit_price) > parseFloat(b.compare_unit_price))
            {
                return 1;
            }
            
            return 0;
            
        });
        
        // Sort them in ascending order
        different_format_products.sort(function(a, b)
        {
            if(parseFloat(a.compare_unit_price) < parseFloat(b.compare_unit_price))
            {
                return -1;
            }
            
            if(parseFloat(a.compare_unit_price) > parseFloat(b.compare_unit_price))
            {
                return 1;
            }
            
            return 0;
            
        });

        results.push(different_store_products);
        results.push(different_format_products);

        return results;

    };
    
    /**
     * group by store
     * @returns {undefined}
     */
    groupByStore = function()
    {
        var currentDepartmentStoreID = 0;
        
        // List of all department stores the products belong to
        $scope.departmenStores = [];
        
        for(var x in $rootScope.cart)
        {
            var storeProduct = $rootScope.cart[x].store_product;
            
            // Filter

            if(currentDepartmentStoreID !== parseInt(storeProduct.retailer.id))
            {
                $scope.departmenStores.push(storeProduct.department_store);
                
                $scope.departmenStores[$scope.departmenStores.length - 1].storeName = storeProduct.retailer.name;
                $scope.departmenStores[$scope.departmenStores.length - 1].image = storeProduct.retailer.image;
                
                if(parseFloat($scope.departmenStores[$scope.departmenStores.length - 1].distance) === 0)
                {
                    $scope.departmenStores[$scope.departmenStores.length - 1].range = 0;
                }
                
                $scope.departmenStores[$scope.departmenStores.length - 1].products = [];

                currentDepartmentStoreID = parseInt(storeProduct.retailer.id);
            }
            
            $scope.departmenStores[$scope.departmenStores.length - 1].products.push($rootScope.cart[x]);
        }
        
        $scope.departmenStores.sort(function(a, b)
        {
            if(parseFloat(a.range) > parseFloat(b.range))
            {
                return -1;
            }
            
            if(parseFloat(a.range) < parseFloat(b.range))
            {
                return 1;
            }
            
            return 0;
            
        });
        
        $scope.groupCartByCategory();
        
        $scope.getDepartmentStoreInfo();
                
        // Get department store address, time to and distance
    };
    
    
    /**
     * This method gets a list of all products grouped by stores. 
     * Each store group also has a list of missing_products. 
     * i.e. products in cart that are not in the store
     * @returns {Array}
     */
    $scope.getListByStore = function()
    {
        var stores = [];
        
        for(var i in $rootScope.cart)
        {
            var cart_item = Object.assign({}, $rootScope.cart[i]);
            
            // Get the store product of the cart item
            var item = $rootScope.cart[i].store_product;
            
            // Check if it has related products
            if(item.related_products.length === 0)
            {
                var store_product = item;
                
                // check if the store for this store product has already been added to the array
                var index = stores.map(function(e) { return e.id; }).indexOf(store_product.retailer.id); 
                
                if(index >= 0)
                {
                    var product_index = stores[index].store_products.map(function(e){ return e.product.id; }).indexOf(store_product.product.id);
                    if(product_index === -1)
                    {
                        stores[index].store_products.push(cart_item);
                    }
                }
                else
                {
                    var retailer = Object.assign({}, store_product.retailer);
                    retailer.department_store = store_product.department_store;
   
                    stores.push(retailer);
                    stores[stores.length - 1].store_products = [];
                    stores[stores.length - 1].store_products.push(cart_item);
                }
            }
            
            // each related product represents a store
            for(var x in item.related_products)
            {
                // Pass by value
                var newItem = Object.assign({}, cart_item);
                
                // get a product store product
                var store_product = item.related_products[x];
                
                store_product.related_products = item.related_products;
                
                newItem.store_product = store_product;
                // check if the store for this related product has already been added to the array
                var index = stores.map(function(e) { return e.id; }).indexOf(store_product.retailer.id); 
                
                if(index >= 0)
                {
                    var product_index = stores[index].store_products.map(function(e){ return e.product.id; }).indexOf(store_product.product.id);
                    if(product_index === -1)
                    {
                        stores[index].store_products.push(newItem);
                    }
                }
                else
                {
                    var retailer = Object.assign({}, store_product.retailer);;
                    retailer.department_store = store_product.department_store;
   
                    stores.push(retailer);
                    stores[stores.length - 1].store_products = [];
                    stores[stores.length - 1].store_products.push(newItem);
                }
            }
        }
        
        // Get the list of missing products for each store
        for(var i in $rootScope.cart)
        {
            for(var x in stores)
            {
                // Get a given store product
                var item = Object.assign({}, $rootScope.cart[i]);
                
                // check if that store product exists in the given store
                index = stores[x].store_products.map(function(e) { return e.product.id; }).indexOf(item.store_product.product.id); 
                
                // The store product does not exist in that store
                if(index === -1)
                {
                    if(angular.isNullOrUndefined(stores[x].missing_products))
                    {
                        stores[x].missing_products = [];
                    }
                    
                    // At it to the list of missing products
                    stores[x].missing_products.push(item);
                }
            }
            
        }
        
        stores.sort(function(a, b)
        {
           
           if(a.store_products.length === b.store_products.length)
           {
               return 0;
           }
           
           if(a.store_products.length > b.store_products.length)
           {
               return -1;
           }
           else
           {
               return 1;
           }
            
        });
        
        return stores;
    };
    
    $scope.storeTabSelected = function(store)
    {
        
        // Get the selected store
        $rootScope.selectedStore = store;
                
        $scope.groupCartByCategory();
        
        
        // For each store product in the cart item, 
        // we select the least popular(most expensive)
        for(var i in $rootScope.cart)
        {
            var related_products = $rootScope.cart[i].store_product.related_products;
            
            if(!angular.isNullOrUndefined(related_products))
            {
                // There are no related items. Skip this product
                if(related_products.length === 0)
                {
                    continue;
                }
                
                // The last related product is the most expensive. 
                $rootScope.cart[i].store_product = related_products[related_products.length - 1];
                $rootScope.cart[i].store_product.related_products = related_products;
            }
            
            // Set the cart item store products to the 
            // selected store products
            for(var x in $rootScope.selectedStore.store_products)
            {
                if(parseInt($rootScope.cart[i].store_product.product.id) === parseInt($rootScope.selectedStore.store_products[x].store_product.product.id))
                {
                    $rootScope.selectedStore.store_products[x].quantity = $rootScope.cart[i].quantity;
                }
            }
            
            // reset the product price
            for(var x in $rootScope.selectedStore.missing_products)
            {
                if(parseInt($rootScope.cart[i].store_product.product.id) === parseInt($rootScope.selectedStore.missing_products[x].store_product.product.id))
                {
                    $rootScope.selectedStore.missing_products[x].quantity = $rootScope.cart[i].quantity;
                }
            }
        }
        
        $rootScope.totalPriceAvailableProducts = $scope.getCartTotalPrice(true);
        $rootScope.totalPriceUnavailableProducts = $scope.getCartTotalPrice(false);
        
        $scope.update_price_optimization();
        
        
    };
	
    /**
     * For each department store, gets the time to store and distance to store
     * using the google API
     * @returns {undefined}
     */    
    $scope.getDepartmentStoreInfo = function()
    {
    	// construct ordered list of origins and destinations
        var origins = [];
        var destinations = [];
        var mode = "DRIVING";
        
        var longitude = $scope.isUserLogged ? $scope.loggedUser.profile.longitude : $scope.longitude;
        var latitude = $scope.isUserLogged ? $scope.loggedUser.profile.latitude : $scope.latitude;
        
        var count = 0;
        
        var nextBatchIndex = 0;
        
        for(var i in $scope.departmenStores)
        {
            if(i < ctrl.lastBatchIndex)
            {
                // Already got the distance and time information for this department store. 
                // We skip it
                continue;
            }
                        
            if(count < ctrl.BATCH_SIZE)
            {
                var department_store = $scope.departmenStores[i];
                origins.push(new google.maps.LatLng(parseFloat(latitude), parseFloat(longitude)));
                destinations.push(new google.maps.LatLng(parseFloat(department_store.latitude), parseFloat(department_store.longitude)));
                count++;
            }
            
            
            if(count === ctrl.BATCH_SIZE)
            {
                nextBatchIndex = parseInt(i) + parseInt(1);
                break;
            }
            
            if(count !== ctrl.BATCH_SIZE)
            {
                nextBatchIndex = $scope.departmenStores.length;
            }
        }
        
        // No more batches are available
        if(count === 0)
        {
            $rootScope.totalPriceAvailableProducts = $scope.getCartTotalPrice(true);
            $rootScope.totalPriceUnavailableProducts = $scope.getCartTotalPrice(false);
            ctrl.lastBatchIndex = 0;
            return;
        }
        
        var service = new google.maps.DistanceMatrixService();
        service.getDistanceMatrix(
        {
            origins: origins,
            destinations: destinations,
            travelMode: mode,
            avoidHighways: false,
            avoidTolls: false
        }, function(response, status)
        {
            if(response === null || typeof response === "undefined")
            {
                return;
            }

            $scope.$apply(function()
            {
                
                for(var x in response.rows)
                {
                    if(typeof response.rows[x].elements[0].status !== 'undefined' && response.rows[x].elements[0].status === "ZERO_RESULTS")
                    {
                        continue;
                    }
                    else
                    {
                        if(!angular.isNullOrUndefined(response.rows[0].elements[x].distance))
                        {
                            var index = parseInt(ctrl.lastBatchIndex) + parseInt(x);
                            
                            $scope.departmenStores[index].distance = response.rows[0].elements[x].distance.value;
                            $scope.departmenStores[index].distanceText = response.rows[0].elements[x].distance.text;
                            $scope.departmenStores[index].timeText = response.rows[0].elements[x].duration.text;
                            $scope.departmenStores[index].fullName = $scope.departmenStores[index].address + ', ' + 
                                    $scope.departmenStores[index].state + ', ' + 
                                    $scope.departmenStores[index].city + ', ' + 
                                    $scope.departmenStores[index].postcode;
                        }
                        
                    }
                }
                
                ctrl.lastBatchIndex = nextBatchIndex;
                $scope.getDepartmentStoreInfo();
                
            });
        });
	  
    };
    
    /**
     * 
     * @returns {Number}
     */
    $scope.getCartTotalPrice = function(availableProducts)
    {
        var total = 0;

        if($rootScope.cartSettings.cartView)
        {
            for(var key in $scope.departmenStores)
            {
                
                for(var x in $scope.departmenStores[key].products)
                {
                    var item = $scope.departmenStores[key].products[x];
                    
                    if(availableProducts)
                    {
                        if(parseFloat($scope.departmenStores[key].distance) > 0)
                        {
                            total += parseFloat(item.quantity * item.store_product.price);
                        }
                    }
                    else
                    {
                        if(parseFloat($scope.departmenStores[key].distance) === 0)
                        {
                            total += parseFloat(item.quantity * item.store_product.price);
                        }
                    }
                }
            }
        }
        else
        {
            if(availableProducts)
            {
                for(var x in $rootScope.selectedStore.store_products)
                {
                    var item = $rootScope.selectedStore.store_products[x];
                    total += parseFloat(item.quantity * item.store_product.price);
                }
            }
            else
            {

                for(var x in $rootScope.selectedStore.missing_products)
                {
                    var item = $rootScope.selectedStore.missing_products[x];
                    total += parseFloat(item.quantity * item.store_product.price);
                }

            }
        }

        return total;
    };
    
    $scope.getStoreDrivingDistances = function()
    {
    	// construct ordered list of origins and destinations
        var origins = [];
        var destinations = [];
        var mode = "DRIVING";
        
        var longitude = $scope.isUserLogged ? $scope.loggedUser.profile.longitude : $scope.longitude;
        var latitude = $scope.isUserLogged ? $scope.loggedUser.profile.latitude : $scope.latitude;

        for(var i in $scope.stores.slice(0, 5))
        {
            var department_store = $scope.stores[i].department_store;
            origins.push(new google.maps.LatLng(parseFloat(latitude), parseFloat(longitude)));
            destinations.push(new google.maps.LatLng(parseFloat(department_store.latitude), parseFloat(department_store.longitude)));
        }
        
        var service = new google.maps.DistanceMatrixService();
        service.getDistanceMatrix(
        {
                origins: origins,
                destinations: destinations,
                travelMode: mode,
                avoidHighways: false,
                avoidTolls: false
        }, function(response, status)
        {
            if(response === null || typeof response === "undefined")
            {
                return;
            }

            $scope.$apply(function()
            {
                for(var x in response.rows)
                {
                    if(typeof response.rows[x].elements[0].status !== 'undefined' 
                            && (response.rows[x].elements[0].status === "ZERO_RESULTS" || response.rows[0].elements[x].status === "ZERO_RESULTS"))
                    {
                        continue;
                    }
                    else
                    {
                        $scope.stores[x].department_store.distance = response.rows[0].elements[x].distance.value;
                        $scope.stores[x].department_store.distanceText = response.rows[0].elements[x].distance.text;
                        $scope.stores[x].department_store.time = response.rows[0].elements[x].duration.value;
                        $scope.stores[x].department_store.timeText = response.rows[0].elements[x].duration.text;
                        $scope.stores[x].department_store.fullName = response.destinationAddresses[x];
                    }
                    $rootScope.totalPriceAvailableProducts = $scope.getCartTotalPrice(true);
                    $rootScope.totalPriceUnavailableProducts = $scope.getCartTotalPrice(false);
                }
            });

        });
	  
    };
	
    
    
    $scope.InitMap = function(ev, departmentStore)
    {
        
        var longitude = $scope.isUserLogged ? $scope.loggedUser.profile.longitude : $scope.longitude;
        var latitude = $scope.isUserLogged ? $scope.loggedUser.profile.latitude : $scope.latitude;
        
        $mdDialog.show({
            controller: GoogleMapsController,
            templateUrl:  $scope.base_url + 'assets/templates/google-map.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose:true,
            disableParentScroll : true,
            preserveScope:true,
            scope : $scope,
            fullscreen: true,
            onComplete : function()
            {
                var origin = {lat: parseFloat(latitude), lng: parseFloat(longitude)};
                var destination = {lat: parseFloat(departmentStore.latitude), lng: parseFloat(departmentStore.longitude)};

                var map = new google.maps.Map(document.getElementById('map'), {
                  center: destination,
                  zoom: 7
                });

                var directionsDisplay = new google.maps.DirectionsRenderer({
                  map: map
                });

                // Set destination, origin and travel mode.
                var request = {
                  destination: destination,
                  origin: origin,
                  travelMode: 'DRIVING'
                };

                // Pass the directions request to the directions service.
                var directionsService = new google.maps.DirectionsService();
                directionsService.route(request, function(response, status) {
                  if (status == 'OK') {
                    // Display the route on the map.
                    directionsDisplay.setDirections(response);
                  }
                });
            }
        })
        .then(function(answer) {

        }, function() {
                
        });
    };
    
    function GoogleMapsController()
    {
        $scope.cancel = function() 
        {
            $mdDialog.cancel();
        };
    };
    
    $scope.productChanged = function(currentStoreProduct)
    {
        var item = null;
        
        if($rootScope.cartSettings.cartView)
        {
            for(var i in $rootScope.cart)
            {
                if(parseInt($rootScope.cart[i].product.id) === parseInt(currentStoreProduct.product.id))
                {
                    item = $rootScope.cart[i];
                    
                    $rootScope.cart[i].store_product = currentStoreProduct;
                    var relatedProducts = $scope.getRelatedProducts($rootScope.cart[i].store_product);
                    $rootScope.cart[i].different_store_products = relatedProducts[0];
                    $rootScope.cart[i].different_format_products = relatedProducts[1]; 
                    $rootScope.cart[i].store_product_id = currentStoreProduct.id;
                    break;
                }
            }
        }
        else
        {
            for(var i in $rootScope.selectedStore.missing_products)
            {
                if(parseInt($rootScope.selectedStore.missing_products[i].product.id) === parseInt(currentStoreProduct.product.id))
                {
                    item = $rootScope.selectedStore.missing_products[i];
                    
                    $rootScope.selectedStore.missing_products[i].store_product = currentStoreProduct;
                    var relatedProducts = $scope.getRelatedProducts($rootScope.selectedStore.missing_products[i].store_product);
                    $rootScope.selectedStore.missing_products[i].different_store_products = relatedProducts[0];
                    $rootScope.selectedStore.missing_products[i].different_format_products = relatedProducts[1]; 
                    $rootScope.selectedStore.missing_products[i].store_product_id = currentStoreProduct.id;
                    break;
                }
            }
            
            for(var i in $rootScope.selectedStore.store_products)
            {
                if(parseInt($rootScope.selectedStore.store_products[i].product.id) === parseInt(currentStoreProduct.product.id))
                {
                    item = $rootScope.selectedStore.store_products[i];
                    
                    $rootScope.selectedStore.store_products[i].store_product = currentStoreProduct;
                    var relatedProducts = $scope.getRelatedProducts($rootScope.selectedStore.store_products[i].store_product);
                    $rootScope.selectedStore.store_products[i].different_store_products = relatedProducts[0];
                    $rootScope.selectedStore.store_products[i].different_format_products = relatedProducts[1]; 
                    $rootScope.selectedStore.store_products[i].store_product_id = currentStoreProduct.id;
                    break;
                }
            }
        }
        
        if(item === null)
        {
            return;
        }
        
        // Update cart item
        var update_data =
        {
            id      : item.product.id,
            rowid   : item.rowid,
            qty     : item.quantity,
            price   : currentStoreProduct.price,
            name    : 'name_'.concat(item.product.id),
            options : {store_product_id : currentStoreProduct.id, quantity : item.quantity}
        };

        var updateCartPromise = eapp.updateCart(update_data);

        updateCartPromise.then(function()
        {
            // Finished updating the cart item
            $rootScope.totalPriceAvailableProducts = $scope.getCartTotalPrice(true);
            $rootScope.totalPriceUnavailableProducts = $scope.getCartTotalPrice(false);
            $rootScope.sortCart();
            groupByStore();

            $scope.update_price_optimization();
            // If the user changed a product, he is no longer viewing an optimized store. 
            $rootScope.cartSettings.optimizedCart = false;
        });
        
        
    };
    
    $rootScope.$watch('cart', function(newValue, oldValue)
    {
        $scope.update_price_optimization();
    });
    
    $scope.removeFromCart = function(product_id)
    {
        var removePromise = eapp.removeFromCart($rootScope.getRowID(product_id));
        
        removePromise.then(function(response)
        {
            if(Boolean(response.data.success))
            {
                $rootScope.removeItemFromCart(product_id);
                groupByStore();
                $scope.update_price_optimization();
            }
        });

    };

    
    $scope.updateCartQuantity = function(newQuantity, productID)
    {
        for(var x in $rootScope.cart)
        {
            var item = $rootScope.cart[x];
            
            if(parseInt($rootScope.cart[x].store_product.product.id) === parseInt(productID))
            {
                if(parseInt(newQuantity) !== parseInt($rootScope.cart[x].quantity))
                {
                    $rootScope.cart[x].quantity = parseInt(newQuantity);
                }
                
                // Update cart item
                var update_data =
                {
                    id      : $rootScope.cart[x].product.id,
                    rowid   : $rootScope.cart[x].rowid,
                    qty     : $rootScope.cart[x].quantity,
                    price   : parseInt($rootScope.cart[x].store_product.price),
                    name    : 'name_'.concat($rootScope.cart[x].product.id),
                    options : {store_product_id : $rootScope.cart[x].store_product_id, quantity : $rootScope.cart[x].quantity}
                };
                
                eapp.updateCart(update_data);
            }
        }
        
        $rootScope.totalPriceAvailableProducts = $scope.getCartTotalPrice(true);
        $rootScope.totalPriceUnavailableProducts = $scope.getCartTotalPrice(false);
        $scope.update_price_optimization();
    };

    $scope.update_price_optimization = function()
    {
        $scope.price_optimization = 0;
        
        $scope.min_price_optimization = 0;
        
        /**
         * An optimization value greater than this number implies
         * that the product might have an error. 
         * @type Number
         */
        

        for(var key in $scope.cart)
        {
            var cart_item = $scope.cart[key];

            if(angular.isNullOrUndefined(cart_item.store_product.worst_product) 
                    || parseFloat(cart_item.store_product.price) === 0)
            {
                continue;
            }
            
            var value = 
                    (parseFloat(cart_item.store_product.worst_product.compare_unit_price) - parseFloat(cart_item.store_product.compare_unit_price)) 
                    * parseFloat(cart_item.quantity) * parseFloat($scope.getFormat(cart_item.store_product)) * parseFloat(cart_item.store_product.equivalent);
            
            // A value greater than 20 might be an error. 
            if(parseFloat(parseInt(value) / parseInt(cart_item.quantity)) > $scope.errorMargin)
            {
                console.log(cart_item);
            }
            else
            {
                $scope.price_optimization += value;
            }
        }
        
        for(var key in $scope.cart)
        {
            var cart_item = $scope.cart[key];

            if(angular.isNullOrUndefined(cart_item.store_product.worst_product) 
                    || parseFloat(cart_item.store_product.price) === 0 || angular.isNullOrUndefined(cart_item.store_product.related_products))
            {
                continue;
            }
            
            var min_optimization = 0;
            
            for(var i in cart_item.store_product.related_products)
            {
                // This is the currently selected item. 
                if(parseInt(cart_item.store_product.related_products[i].id) === parseInt(cart_item.store_product.id))
                {
                    continue;
                }
                
                if(cart_item.store_product.related_products[i].compare_unit_price <  min_optimization || min_optimization === 0)
                {
                    min_optimization = cart_item.store_product.related_products[i].compare_unit_price;
                }
            }
            
            var value = 
                    (parseFloat(min_optimization) - parseFloat(cart_item.store_product.compare_unit_price)) 
                    * parseFloat(cart_item.quantity) * parseFloat($scope.getFormat(cart_item.store_product)) * parseFloat(cart_item.store_product.equivalent);
            
            $scope.min_price_optimization += value;
        }
    };
    
    
   
    $scope.get_price_label = function(store_product, product)
    {
        return parseFloat(store_product.price) === 0 ? "Item pas disponible" : "CAD " + store_product.price * product.quantity;
    };
    
    /**
     * This method applies the selected store
     * @param {type} index
     * @returns {void}
     */
    $scope.store_selected = function(index)
    {
        for(var store_index in $rootScope.close_stores)
        {
            if(parseInt(store_index) !== parseInt(index))
            {
                $rootScope.close_stores[store_index].selected = false;
            }
            else
            {
                $rootScope.travel_distance = $rootScope.close_stores[store_index].distance;
            }
        }
        
        for(var product_index in $rootScope.cart)
        {
            $rootScope.cart[product_index].store_product = $rootScope.cart[product_index].store_products[index];
        }
    }; 
    
    $rootScope.clearCart = function($event)
    {
        var confirmDialog = $rootScope.createConfirmDIalog($event, "Cela effacera tous les contenus de votre panier.");
        
        $mdDialog.show(confirmDialog).then(function() 
        {
            var cartClearedPromise = eapp.clearCart();
            
            cartClearedPromise.then(function(response)
            {
                $rootScope.cart = [];
                $scope.stores = [];
                $scope.departmenStores = [];
		$rootScope.totalPriceAvailableProducts = 0;
                
            });

        });
        
        
    };
     
    $rootScope.relatedProductsAvailable = function()
    {
        if(typeof $scope.storeProduct !== 'undefined')
        {
            if(typeof $scope.storeProduct.related_products !== 'undefined' && $scope.storeProduct.related_products.length > 0)
            {
                return true;
            }
        }
        
        return false;
    };
    
    $rootScope.getListAsText = function()
    {
        var currentDepartmentStoreID = -1;
        var smsText = "Votre liste d'épicerie fourni par OtiPrix \n";
        for(var x in $rootScope.cart)
        {
            var storeProduct = $rootScope.cart[x].store_product;

            if(parseFloat(storeProduct.price) === 0)
            {
                continue;
            }

            if(currentDepartmentStoreID !== parseInt(storeProduct.department_store.id))
            {
                    currentDepartmentStoreID = parseInt(storeProduct.department_store.id);

                    smsText += storeProduct.retailer.name;
                    if(typeof storeProduct.department_store !== "undefined" && parseInt(storeProduct.department_store.distance) !== 0)
                    {
                         smsText += " - " +  storeProduct.department_store.address + ", " + storeProduct.department_store.state + ", " + storeProduct.department_store.city + "," + storeProduct.department_store.postcode;
                    }
                    smsText += "\n";
            }

            smsText += storeProduct.product.name + ": " + storeProduct.price + " $ CAD";

            if(storeProduct.unit != null && typeof storeProduct.unit !== "undefined")
            {
                smsText += "/" + storeProduct.unit.name + "\n";
            }
            else
            {
                smsText += "\n";
            }
        }

                    smsText += "TOTAL : $ CAD " + $scope.get_cart_total_price();

                    if(parseFloat($scope.price_optimization) > 0)
                    {
                            smsText += "Vous économiserez environs : $ CAD " +  $scope.price_optimization;
                    }

        return smsText;
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

    $rootScope.sortCart = function()
    {
        $rootScope.cart.sort(function(a, b)
        {
            var keyA = a.store_product.retailer.name.toString(),
            keyB = b.store_product.retailer.name.toString();
            return keyA.localeCompare(keyB);
        });
    };
        
    $rootScope.sendListAsEmail = function($event)
    {
        if(!$rootScope.isUserLogged)
        {
            $scope.showAlert($event, "Se connecter", "Vous devez vous connecter au site pour utiliser cette fonctionnalité..");
            return;
        }

        if($rootScope.cart.length === 0)
        {
            $scope.showAlert($event, "Panier vide", "Votre panier est actuellement vide. Ajoutez des éléments au panier avant d'utiliser cette fonctionnalité.");
            return;
        }
        
        $scope.saveUserOptimisation(3);

        $rootScope.sortCart();

        var formData = new FormData();
        var content = $scope.getCartHtmlContent();
        formData.append("content", content);
        // Send request to server to get optimized list 	
        $http.post($scope.site_url.concat("/cart/mail_user_cart"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            if(response.data)
            {
                $scope.showAlert($event, "Email envoyé", "Votre liste d'épicerie a été envoyée à votre email.");
            }
            else
            {
                $scope.showAlert($event, "Erreur du serveur", "Une erreur inattendue s'est produite. Veuillez réessayer plus tard..");
            }
        });
    };

    $rootScope.sendListAsSMS = function($event)
    {
        if(!$rootScope.isUserLogged)
        {
            return;
        }

        if(parseInt($rootScope.loggedUser.phone_verified) === 0)
        {
            $scope.showAlert($event, "Votre numéro de téléphone n'est pas vérifié", "Votre numéro de téléphone n'est pas vérifié. Veuillez consulter l'onglet de sécurité de votre compte pour vérifier votre numéro de téléphone.");
            return;
        }

        if($rootScope.cart.length === 0)
        {
            $scope.showAlert($event, "Panier vide", "Votre panier est actuellement vide. Ajoutez des éléments au panier avant d'utiliser cette fonctionnalité.");
            return;
        }
        
        $scope.saveUserOptimisation(1);

        $rootScope.sortCart();

        var formData = new FormData();
        formData.append("sms", $rootScope.getListAsText());
        // Send request to server to get optimized list 	
        $scope.promise = $http.post($scope.site_url.concat("/cart/send_sms"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            if(response.data)
            {
                $scope.showAlert($event, "Message envoyé", "Votre liste d'épicerie a été envoyée à votre téléphone.");
            }
        });
    };

    $rootScope.printCart = function($event) 
    {
        if($rootScope.cart.length === 0)
        {
            $scope.showAlert($event, "Panier vide", "Votre panier est actuellement vide. Ajoutez des éléments au panier avant d'utiliser cette fonctionnalité.");
            return;
        }
        
        if($rootScope.isUserLogged)
        {
            $scope.saveUserOptimisation(0);
        }

        var mywindow = window.open('', 'PRINT');

        mywindow.document.write($scope.getCartHtmlContent());

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();
        
        
        return true;

    };
    
    /**
     * This method will group the cart based on categories
     * @returns {undefined}
     */
    $scope.groupCartByCategory = function()
    {
        $scope.productCategories = [];
        
        if(!$rootScope.cartSettings.cartView)
        {
            for(var x in $rootScope.selectedStore.store_products)
            {
                var store_product = $rootScope.selectedStore.store_products[x].store_product;

                if(!angular.isNullOrUndefined(store_product) && $rootScope.selectedStore.store_products[x].quantity > 0)
                {
                    // get product category id
                    var category = store_product.product.category;

                    if(angular.isNullOrUndefined(category))
                    {
                        category = 
                        {
                            id : 0,
                            name : 'Aucune catégorie'
                        };

                        store_product.product.category = category;
                    }

                    // Check if category exists
                    var index = $scope.productCategories.map(function(e) { return e.id; }).indexOf(category.id);

                    if(index !== -1)
                    {

                        if(angular.isNullOrUndefined($scope.productCategories[index].products))
                        {
                            $scope.productCategories[index].products = [];
                        }

                        $scope.productCategories[index].products.push($rootScope.selectedStore.store_products[x]);

                    }
                    else
                    {
                        // create category
                        category.products = [];
                        category.products.push($rootScope.selectedStore.store_products[x]);
                        $scope.productCategories.push(category);
                    }
                }
            }
        }
        else
        {
            for(var i in $scope.departmenStores)
            {
                $scope.departmenStores[i].categories = [];
                
                for(var x in $scope.departmenStores[i].products)
                {
                    var store_product = $scope.departmenStores[i].products[x].store_product;
                    var cartItem = $scope.departmenStores[i].products[x];

                    if(!angular.isNullOrUndefined(store_product) && $scope.departmenStores[i].products[x].quantity > 0)
                    {
                        // get product category id
                        var category = store_product.product.category;

                        if(angular.isNullOrUndefined(category))
                        {
                            category = 
                            {
                                id : 0,
                                name : 'Aucune catégorie'
                            };

                            store_product.product.category = category;
                        }

                        // Check if category exists
                        var index = $scope.departmenStores[i].categories.map(function(e) { return e.id; }).indexOf(category.id);

                        if(index !== -1)
                        {

                            if(angular.isNullOrUndefined($scope.departmenStores[i].categories[index].products))
                            {
                                $scope.departmenStores[i].categories[index].products = [];
                            }

                            $scope.departmenStores[i].categories[index].products.push(cartItem);

                        }
                        else
                        {
                            // create category
                            category.products = [];
                            category.products.push(cartItem);
                            $scope.departmenStores[i].categories.push(category);
                        }
                    }
                }
            }
        }
        
    };
    
    $scope.getCartHtmlContent = function()
    {

        var content = "";

        $rootScope.sortCart();
        
        var siteLogo = $scope.base_url.concat("assets/img/logo.png");
        
        content += '<style> tr:nth-child(even){ background-color : #f2f2f2;} @media print{ body{ -webkit-print-color-adjust: exact;} } </style>';
        
        content += '<html><head><title style="font-style: italic; color : #444; ">OtiPrix - All RIghts Reserved</title>';
        content += '</head><body >';
        content += '<div style="text-align : center; width : 100%; padding : 10px; background-color : #1abc9c !important;"><img style="display = block; margin : auto; width : 60px;" src="' + siteLogo + '" /></div>';
        content += "<h4 style='text-align : center; color : #444; color : #1abc9c;'>OtiPrix - Liste d'épicerie optimisé</h4>";

        var currentDepartmentStoreID = -1;
        
       
        
        for(var i in $scope.departmenStores)
        {
            var departmentStore = $scope.departmenStores[i];
            
            content += '<h4>' + departmentStore.name + ': ' + departmentStore.address + ', ' + departmentStore.state + ', ' + departmentStore.city + ', ' + departmentStore.postcode + '</h4>';
            
            for(var j in departmentStore.categories)
            {
                var category = departmentStore.categories[j];
                
                content += '<b><p style="text-align: center; color : #666;"> - ' + category.name + ' - </p</b>';
                
                content += '<table style="width : 100%; border-collapse : collapse;">';
                
                for(var k in category.products)
                {
                    var storeProduct = category.products[k].store_product;
                    
                    content += '<tr width="100%">';
                    
                    // Details Here
                    content += '<td>';
                    
                    var unit = angular.isNullOrUndefined(storeProduct.unit) ? '-' : storeProduct.unit.name;
                    
                    content += '<p style="padding : 10px;"><span><input type="checkbox" style="margin-right : 5px;"></span><span>' + category.products[k].quantity + ' x </span><b style="color : #1abc9c;">' + storeProduct.product.name + ' </b> (' + storeProduct.format + ' ' + unit + ' à ' + storeProduct.price + ' C$) <span style="float : right"><b style="font-size : 16px;">' + parseFloat(storeProduct.price) * parseFloat(category.products[k].quantity) + ' C$</b></span></p>';
                    
                    content += '</td></tr>';
                    
                }
                
                content += '</table>';
            }
        }
        
        
        
        var total_price = Math.round(parseFloat($rootScope.get_cart_total_price()) * 100) / 100;

        content += "<br>";
        content += "<br>";
        content += "<p style='float : right;'><b><span>Totale : <span><span style=' color : red;'>$ CAD " + total_price + "<span> + taxes. </b></p>";

        if($rootScope.price_optimization > 0)
        {
            content += "<p style='float : right; color : red;'><b>Vous économiserez environs : $ CAD  " + $rootScope.price_optimization + "</b></p>";
        }
        content += '</body></html>';

        return content;
    };
    
    /**
     * The mode represents the method used by the user
     * to get the optimization details. 
     * 0 = Print
     * 1 = sent via sms
     * 2 = sent via email
     */
    $scope.saveUserOptimisation = function(mode)
    {
        var optimization_data = 
        {
            items : getUserCartDetails(),
            price_optimization : $scope.price_optimization,
            mode : mode
        };
        
        var formData = new FormData();
        formData.append("optimization_data", JSON.stringify(optimization_data));
        // Send request to server to get optimized list 	
        $scope.promise = $http.post($scope.site_url.concat("/cart/save_user_optimisation"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            
        });
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
    
    $scope.changeCartDistance = function(newDistance)
    {
        if($scope.isUserLogged)
        {
            var changePromise = eapp.changeDistance('cart_distance', newDistance);

            changePromise.then(function(response)
            {
                if(response.data)
                {
                    // Update Logged User
                    $scope.loggedUser = response.data;
                    $scope.optimization_preference_changed();
                }
            });
        }
        else
        {
            // Change in the session
            window.localStorage.setItem('cart_distance', newDistance);
            $scope.optimization_preference_changed();
        }

        $mdDialog.cancel();
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
                var changePromise = eapp.changeDistance('cart_distance', $scope.default_distance);
            
                changePromise.then(function(response)
                {
                    if(response.data)
                    {
                        // Update Logged User
                        $scope.loggedUser = response.data;
                        $scope.optimization_preference_changed();
                    }
                });
            }
            else
            {
                // Change in the session
                window.localStorage.setItem('cart_distance', $scope.default_distance);
                $scope.optimization_preference_changed();
            }
            
            $mdDialog.cancel();
        };
    };
           
    /**
     * This method gets the cart details
     * and saves them to the database for the user
     * The mode represents the method used by the user
     * to get the optimization details. 
     * 0 = Print
     * 1 = sent via sms
     * 2 = sent via email
     */
    function getUserCartDetails()
    {
        var items = [];
        
        
        for(var i in $scope.cart)
        {
            var data = 
            {
                store_product_id : $scope.cart[i].store_product.id,
                product : $scope.cart[i].store_product.product.id,
                quantity : $scope.cart[i].quantity
            };
            
            items.push(data);
        }
        
        return items;
    }
    
    $rootScope.viewProduct = function(product_id, ev)
    {
        eapp.viewProduct($scope, product_id, ev);
    };
    
    
    $rootScope.selectProduct = function(store_product)
    {
        // Get the latest products
        var promise = eapp.getProduct(store_product.id);
    
        promise.then(function(response)
        {
            $scope.storeProduct = response.data;
        },
        function(errorResponse)
        {
            $scope.storeProduct = null;
        });
    };
    
    $scope.settingsChanged = function(item)
    {
        $scope.updateItemChanged(item);
        
        window.sessionStorage.setItem("cartFilterSettings", JSON.stringify($scope.cartFilterSettings));
        
        // Get store filter
        $scope.createResultsFilter();
        
        $scope.update_cart_list();
        
    };
    
    $scope.refresh = function(cartSettings)
    {
        $scope.cartSettings = cartSettings;
        	
	// Save the new configuration for the current session    
	window.sessionStorage.setItem("cartSettings", JSON.stringify($scope.cartSettings));
	    
        $scope.update_cart_list();
    };
    
    $scope.updateItemChanged = function(item)
    {
        var index = $scope.cartFilterSettings[item.type].values.map(function(e){ return e.name; }).indexOf(item.name);
        
        if(index > -1)
        {
            $scope.cartFilterSettings[item.type].values[index] = item;
        }
    };
    
    $rootScope.$watch('cartReady', function(newValue, oldValue)
    {
        if(newValue)
        {
            
            if($scope.controller === "cart")
            {
                $rootScope.isCart = true;
                
                $scope.Init();
            }
        }
        
        $scope.update_price_optimization();
    });
	
}]);


