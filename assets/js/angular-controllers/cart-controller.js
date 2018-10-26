/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('eappApp').component('cartListItem', {
    
    templateUrl: 'templates/components/cartListItem.html',
    controller : CartListItemController,
    bindings: 
    {
        item: '<',
        onDelete: '&',
        onUpdate: '&',
        onUpdateQuantity: '&',
        viewRetailerImage: '@'
    }
});

function CartListItemController($scope, appService, eapp, $mdDialog, profileData, cart)
{
    var ctrl = this;
        
    ctrl.$onInit = function()
    {
        ctrl.iscartview = profileData.get().cartView;
        
        ctrl.isUserLogged = appService.isUserLogged;
        
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
            templateUrl:  appService.baseUrl + 'assets/templates/change-store-product.html',
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
            var relatedProducts = cart.getRelatedProducts($scope.selectedStoreProduct);
            $scope.selectedStoreProduct.different_store_products = relatedProducts.differentStore;
            $scope.selectedStoreProduct.different_format_products = relatedProducts.differentFormat;
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
            templateUrl:  appService.baseUrl + 'assets/templates/change-format-product.html',
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
            var relatedProducts = cart.getRelatedProducts($scope.selectedStoreProduct);
            $scope.selectedStoreProduct.different_store_products = relatedProducts.differentStore;
            $scope.selectedStoreProduct.different_format_products = relatedProducts.differentFormat;
            
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

angular.module("eappApp").controller("CartController", function(appService, $scope, $http, $mdDialog, eapp, profileData, cart) 
{
    var ctrl = this;
    
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
        
    $scope.totalPriceAvailableProducts = 0;
    $scope.totalPriceUnavailableProducts = 0;
    
    /**
    * List of optimized cart store product items
    */
    $scope.optimized_cart = [];
    
    $scope.price_optimization = 0;
            
    $scope.min_price_optimization = 0;
    
    $scope.productCategories = [];
    
    $scope.profileData = profileData;
    
    $scope.$watch('min_price_optimization', function()
    {
        $scope.show_min_price_optimization = $scope.min_price_optimization > 0 && $scope.price_optimization > 0 && ($scope.price_optimization - $scope.min_price_optimization) > 0.1;
    });
        
    
    appService.ready.then(function()
    {
        $scope.Init();
    });
    
    /**
     * This method initializes the cart
     * @returns {undefined}
     */
    $scope.Init = function()
    {       

        $scope.default_distance = profileData.instance.cartDistance;
        
        profileData.set("cartFilterSettings", null);
        
        $scope.update_cart_list();
        
    };
            
    $scope.true_value = true;
    $scope.false_value = false;
    
    ctrl.BATCH_SIZE = 8;
    
    $scope.getDistance = function()
    {
        return profileData.instance.cartDistance;
    };
    
    /**
     * Updates the cart list by finding cheap products 
     * close to you
     * @returns {undefined}
     */
    $scope.update_cart_list = function()
    {
        $scope.optimized_cart = [];        
        
        $scope.ready = false;
        
        if(profileData.instance.cartFilterSettings)
        {
            profileData.instance.cartFilterSettings = profileData.instance.cartFilterSettings;
            $scope.createResultsFilter();
            $scope.ready = true;
        }
        	
        var formData = new FormData();
        formData.append("distance", profileData.instance.cartDistance);
        // User's longitude
        formData.append("longitude", appService.longitude);
        // user's latitude
        formData.append("latitude", appService.latitude);
        
        formData.append("searchAll", JSON.stringify(!profileData.instance.searchMyList));
        formData.append("resultsFilter", JSON.stringify($scope.resultFilter));
        formData.append("viewOptimizedList", profileData.instance.optimizedCart);
        
        // Send request to server to get optimized list 	
        $scope.promise = 
            $http.post( appService.siteUrl.concat("/cart/update_cart_list"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                profileData.instance.cartFilterSettings = response.data.cartFilterSettings;
                
                profileData.set("cartFilterSettings", response.data.cartFilterSettings);
                
                // Clear the application cart
                // we are going to repopulate it with whatever the server serves us
                appService.cart = [];
                
                // Repopulate the cart
                for(var x in response.data.items)
                {
                    var cartItem = response.data.items[x];

                    if(angular.isNullOrUndefined(cartItem.store_product.related_products))
                    {
                        cartItem.store_product.related_products = [];
                    }
                    
                    var relatedProducts = cart.getRelatedProducts(cartItem.store_product);
                    cartItem.different_store_products = relatedProducts.differentStore;
                    cartItem.different_format_products = relatedProducts.differentFormat;
					
                    appService.cart.push(cartItem);
                }
                
                $scope.results_available = !angular.isNullOrUndefined(appService.cart) && appService.cart.length > 0;
                
                if(profileData.get().cartView)
                {
                    cart.sortCartByStore();
                
                    // orders the stores and assigns the distance and times to each of the
                    // department stores
                    groupByStore();

                    ctrl.update_price_optimization();
                }
                else
                {
                    // Get the stores that will be displayed
                    $scope.stores = ctrl.getListByStore();

                    // Select the first store
                    if($scope.stores.length > 0)
                    {
                        $scope.storeTabSelected($scope.stores[0]);
                    }
                    
                    // Get the driving distances of each of the stores
                    ctrl.getStoreDrivingDistances();
                }
                
                $scope.ready = true;
                
            });
    };
    
    $scope.createResultsFilter = function()
    {
        if(angular.isNullOrUndefined(profileData.instance.cartFilterSettings))
        {
            return;
        }
        
        $scope.resultFilter = {};
        
        for(var x in profileData.instance.cartFilterSettings)
        {
            var values = profileData.instance.cartFilterSettings[x].values;
            var setting = profileData.instance.cartFilterSettings[x].setting;
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
    
    /**
     * group by store
     * @returns {undefined}
     */
    groupByStore = function()
    {
        var currentDepartmentStoreID = 0;
        
        // List of all department stores the products belong to
        $scope.departmenStores = [];
        
        for(var x in appService.cart)
        {
            var storeProduct = appService.cart[x].store_product;
            
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
            
            $scope.departmenStores[$scope.departmenStores.length - 1].products.push(appService.cart[x]);
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
        
        ctrl.getDepartmentStoreInfo();
                
        // Get department store address, time to and distance
    };
    
    
    /**
     * This method gets a list of all products grouped by stores. 
     * Each store group also has a list of missing_products. 
     * i.e. products in cart that are not in the store
     * @returns {Array}
     */
    ctrl.getListByStore = function()
    {
        var stores = [];
        
        for(var i in appService.cart)
        {
            var cart_item = Object.assign({}, appService.cart[i]);
            
            // Get the store product of the cart item
            var item = appService.cart[i].store_product;
            
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
        for(var i in appService.cart)
        {
            for(var x in stores)
            {
                // Get a given store product
                var item = Object.assign({}, appService.cart[i]);
                
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
        $scope.selectedStore = cart.selectCartStore(store);
        
        $scope.groupCartByCategory();
        
        $scope.totalPriceAvailableProducts = ctrl.getCartTotalPrice(true);
        $scope.totalPriceUnavailableProducts = ctrl.getCartTotalPrice(false);
        
        ctrl.update_price_optimization();
        
    };
	
    /**
     * For each department store, gets the time to store and distance to store
     * using the google API
     * @returns {undefined}
     */    
    ctrl.getDepartmentStoreInfo = function()
    {
    	// construct ordered list of origins and destinations
        var origins = [];
        var destinations = [];
        var mode = "DRIVING";
        
        var coordinates = appService.getActiveUserCoordinates();
        var longitude = coordinates.longitude;
        var latitude = coordinates.latitude;
        
        var tmpOrigins = [];
        var tmpDestinations = [];
        var storeIndex = 0;
        
        var userLocation = new google.maps.LatLng(parseFloat(latitude), parseFloat(longitude));
        
        // Group the destinations and origins into groups of 5 each
        for(var i in $scope.departmenStores)
        {
            var department_store = $scope.departmenStores[i];
            tmpOrigins.push(userLocation);
            tmpDestinations.push(new google.maps.LatLng(parseFloat(department_store.latitude), parseFloat(department_store.longitude)));
            storeIndex++;
            
            // 5 stores where added
            if(storeIndex > 0 && parseInt(storeIndex % ctrl.BATCH_SIZE) === 0)
            {
                storeIndex = 0;
                origins.push(tmpOrigins);
                destinations.push(tmpDestinations);
                tmpOrigins = [];
                tmpDestinations = [];
            }
        }
        
        if(tmpOrigins.length > 0)
        {
            origins.push(tmpOrigins);
            destinations.push(tmpDestinations);
        }
        
        for(var i in origins)
        {
            let arrayIndex = i * ctrl.BATCH_SIZE;
            
            var service = new google.maps.DistanceMatrixService();
            
            service.getDistanceMatrix(
            {
                origins: origins[i],
                destinations: destinations[i],
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
                                var index = arrayIndex + parseInt(x);

                                $scope.departmenStores[index].distance = response.rows[0].elements[x].distance.value;
                                $scope.departmenStores[index].distanceText = response.rows[0].elements[x].distance.text;
                                $scope.departmenStores[index].timeText = response.rows[0].elements[x].duration.text;
                                $scope.departmenStores[index].fullName = $scope.departmenStores[index].address + ', ' + 
                                $scope.departmenStores[index].city + ', ' + 
                                $scope.departmenStores[index].state + ', ' + 
                                $scope.departmenStores[index].postcode;
                            }

                        }
                    }
                    
                    $scope.totalPriceAvailableProducts = ctrl.getCartTotalPrice(true);
                    $scope.totalPriceUnavailableProducts = ctrl.getCartTotalPrice(false);

                });
            });
        }
        
	  
    };
    
    /**
     * This method will get the total cost of the cart items. 
     * if @availableProducts is true, it gets the price for only available products
     * if @availableProducts is false, it gets the price for all products
     * @param {type} availableProducts
     * @returns {Number}
     */
    ctrl.getCartTotalPrice = function(availableProducts)
    {
        var total = 0;

        if(profileData.instance.cartView)
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
                for(var x in $scope.selectedStore.store_products)
                {
                    var item = $scope.selectedStore.store_products[x];
                    total += parseFloat(item.quantity * item.store_product.price);
                }
            }
            else
            {

                for(var x in $scope.selectedStore.missing_products)
                {
                    var item = $scope.selectedStore.missing_products[x];
                    total += parseFloat(item.quantity * item.store_product.price);
                }

            }
        }

        return total;
    };
    
    ctrl.getStoreDrivingDistances = function()
    {
    	// construct ordered list of origins and destinations
        var origins = [];
        var destinations = [];
        var mode = "DRIVING";
        
        var coordinates = appService.getActiveUserCoordinates();
        var longitude = coordinates.longitude;
        var latitude = coordinates.latitude;
        var storeIndex = 0;
        var tmpOrigins = [];
        var tmpDestinations = [];
        // The origin is always the same
        var userLocation = new google.maps.LatLng(parseFloat(latitude), parseFloat(longitude));
        
        // Group the destinations and origins into groups of 5 each
        for(var i in $scope.stores)
        {
            var department_store = $scope.stores[i].department_store;
            tmpOrigins.push(userLocation);
            tmpDestinations.push(new google.maps.LatLng(parseFloat(department_store.latitude), parseFloat(department_store.longitude)));
            storeIndex++;
            
            // 5 stores where added
            if(storeIndex > 0 && parseInt(storeIndex % 5) === 0)
            {
                storeIndex = 0;
                origins.push(tmpOrigins);
                destinations.push(tmpDestinations);
                tmpOrigins = [];
                tmpDestinations = [];
            }
        }
        
        if(tmpOrigins.length > 0)
        {
            origins.push(tmpOrigins);
            destinations.push(tmpDestinations);
        }
        
        
        for(var i in origins)
        {
            let arrayIndex = i * 5;
            
            var service = new google.maps.DistanceMatrixService();
            
            service.getDistanceMatrix(
            {
                    origins: origins[i],
                    destinations: destinations[i],
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
                            $scope.stores[arrayIndex + parseInt(x)].department_store.distance = response.rows[0].elements[x].distance.value;
                            $scope.stores[arrayIndex + parseInt(x)].department_store.distanceText = response.rows[0].elements[x].distance.text;
                            $scope.stores[arrayIndex + parseInt(x)].department_store.time = response.rows[0].elements[x].duration.value;
                            $scope.stores[arrayIndex + parseInt(x)].department_store.timeText = response.rows[0].elements[x].duration.text;
                            $scope.stores[arrayIndex + parseInt(x)].department_store.fullName = response.destinationAddresses[x];
                        }
                        $scope.totalPriceAvailableProducts = ctrl.getCartTotalPrice(true);
                        $scope.totalPriceUnavailableProducts = ctrl.getCartTotalPrice(false);
                    }
                });

            });
        }
    };
	
    $scope.InitMap = function(ev, departmentStore)
    {
        var coordinates = appService.getActiveUserCoordinates();
        var longitude = coordinates.longitude;
        var latitude = coordinates.latitude;
        
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
        
        if(profileData.instance.cartView)
        {
            for(var i in appService.cart)
            {
                if(parseInt(appService.cart[i].product.id) === parseInt(currentStoreProduct.product.id))
                {
                    item = appService.cart[i];
                    
                    appService.cart[i].store_product = currentStoreProduct;
                    var relatedProducts = cart.getRelatedProducts(appService.cart[i].store_product);
                    appService.cart[i].different_store_products = relatedProducts.differentStore;
                    appService.cart[i].different_format_products = relatedProducts.differentFormat; 
                    appService.cart[i].store_product_id = currentStoreProduct.id;
                    break;
                }
            }
        }
        else
        {
            for(var i in $scope.selectedStore.missing_products)
            {
                if(parseInt($scope.selectedStore.missing_products[i].product.id) === parseInt(currentStoreProduct.product.id))
                {
                    item = $scope.selectedStore.missing_products[i];
                    
                    $scope.selectedStore.missing_products[i].store_product = currentStoreProduct;
                    var relatedProducts = cart.getRelatedProducts($scope.selectedStore.missing_products[i].store_product);
                    $scope.selectedStore.missing_products[i].different_store_products = relatedProducts.differentStore;
                    $scope.selectedStore.missing_products[i].different_format_products = relatedProducts.differentFormat; 
                    $scope.selectedStore.missing_products[i].store_product_id = currentStoreProduct.id;
                    break;
                }
            }
            
            for(var i in $scope.selectedStore.store_products)
            {
                if(parseInt($scope.selectedStore.store_products[i].product.id) === parseInt(currentStoreProduct.product.id))
                {
                    item = $scope.selectedStore.store_products[i];
                    
                    $scope.selectedStore.store_products[i].store_product = currentStoreProduct;
                    var relatedProducts = cart.getRelatedProducts($scope.selectedStore.store_products[i].store_product);
                    $scope.selectedStore.store_products[i].different_store_products = relatedProducts.differentStore;
                    $scope.selectedStore.store_products[i].different_format_products = relatedProducts.differentFormat; 
                    $scope.selectedStore.store_products[i].store_product_id = currentStoreProduct.id;
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
            $scope.totalPriceAvailableProducts = ctrl.getCartTotalPrice(true);
            $scope.totalPriceUnavailableProducts = ctrl.getCartTotalPrice(false);
            cart.sortCartByStore();
            groupByStore();

            ctrl.update_price_optimization();
            // If the user changed a product, he is no longer viewing an optimized store. 
            profileData.set("optimizedCart", false);
        });
        
        
    };
    
    $scope.removeFromCart = function(product_id)
    {
        cart.removeProductFromCart(product_id, function()
        {
            groupByStore();
            ctrl.update_price_optimization();
        });
    };
    
    $scope.updateCartQuantity = function(newQuantity, productID)
    {
        for(var x in appService.cart)
        {
            let item = appService.cart[x];
            
            if(parseInt(item.store_product.product.id) === parseInt(productID))
            {
                if(parseInt(newQuantity) !== parseInt(item.quantity))
                {
                    item.quantity = parseInt(newQuantity);
                }
                
                // Update cart item
                var update_data =
                {
                    id      : item.product.id,
                    rowid   : item.rowid,
                    qty     : item.quantity,
                    price   : parseInt(item.store_product.price),
                    name    : 'name_'.concat(item.product.id),
                    options : {store_product_id : item.store_product_id, quantity : item.quantity}
                };
                
                eapp.updateCart(update_data);
            }
        }
        
        $scope.totalPriceAvailableProducts = ctrl.getCartTotalPrice(true);
        $scope.totalPriceUnavailableProducts = ctrl.getCartTotalPrice(false);
        ctrl.update_price_optimization();
    };

    ctrl.update_price_optimization = function()
    {
        $scope.price_optimization = 0;
        
        $scope.min_price_optimization = 0;
        
        /**
         * An optimization value greater than this number implies
         * that the product might have an error. 
         * @type Number
         */
        

        for(var key in appService.cart)
        {
            var cart_item = appService.cart[key];

            if(angular.isNullOrUndefined(cart_item.store_product.worst_product) 
                    || parseFloat(cart_item.store_product.price) === 0)
            {
                continue;
            }
            
            var value = 
                    (parseFloat(cart_item.store_product.worst_product.compare_unit_price) - parseFloat(cart_item.store_product.compare_unit_price)) 
                    * parseFloat(cart_item.quantity) * parseFloat(cart.getStoreProductFormat(cart_item.store_product)) * parseFloat(cart_item.store_product.equivalent);
            
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
        
        for(var key in appService.cart)
        {
            var cart_item = appService.cart[key];

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
                    * parseFloat(cart_item.quantity) * parseFloat(cart.getStoreProductFormat(cart_item.store_product)) * parseFloat(cart_item.store_product.equivalent);
            
            $scope.min_price_optimization += value;
        }
    };
   
    $scope.get_price_label = function(store_product, product)
    {
        return parseFloat(store_product.price) === 0 ? "Item pas disponible" : "CAD " + store_product.price * product.quantity;
    };
        
    $scope.clearCart = function($event)
    {
        var confirmDialog = eapp.createConfirmDialog($event, "Cela effacera tous les contenus de votre liste.");
        
        $mdDialog.show(confirmDialog).then(function() 
        {
            var cartClearedPromise = eapp.clearCart();
            
            cartClearedPromise.then(function(response)
            {
                appService.cart = [];
                $scope.stores = [];
                $scope.departmenStores = [];
		$scope.totalPriceAvailableProducts = 0;
                
            });

        });
    };
     
    $scope.relatedProductsAvailable = function()
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
    
    ctrl.getListAsText = function()
    {
        var smsText = "Votre liste d'épicerie fourni par OtiPrix \n\n";
        
        for(var i in $scope.departmenStores)
        {
            var departmentStore = $scope.departmenStores[i];
            
            smsText +=  departmentStore.name + ': ' + departmentStore.address + ', ' + departmentStore.state + ', ' + departmentStore.city + ', ' + departmentStore.postcode + '\n\n\n';
            
            for(var j in departmentStore.categories)
            {
                var category = departmentStore.categories[j];
                
                smsText += category.name + '\n';
                                
                for(var k in category.products)
                {
                    var storeProduct = category.products[k].store_product;
                    
                    
                    var unit = angular.isNullOrUndefined(storeProduct.unit) ? '-' : storeProduct.unit.name;
                    
                    smsText += category.products[k].quantity + ' x ' + storeProduct.product.name + ' (' + storeProduct.format + ' ' + unit + ' à ' + storeProduct.price + ' C $), ' + parseFloat(storeProduct.price) * parseFloat(category.products[k].quantity) + ' C $ \n\n';
                    
                }
                
            }
        }
        
        smsText += '\n\n';
        
        var total_price = Math.round(parseFloat(cart.getCartPrice()) * 100) / 100;

        smsText += "TOTAL : " + total_price.toString() + " C $";

        if(parseFloat($scope.price_optimization) > 0)
        {
            var economy = Math.round(parseFloat($scope.price_optimization) * 100) / 100;
            
            smsText += "\nVous économiserez environs :" +  economy + "  C $";
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
        
    $scope.sendListAsEmail = function($event)
    {
        if(!appService.isUserLogged)
        {
            $scope.showAlert($event, "Se connecter", "Vous devez vous connecter au site pour utiliser cette fonctionnalité..");
            return;
        }

        if(appService.cart.length === 0)
        {
            $scope.showAlert($event, "Panier vide", "Votre panier est actuellement vide. Ajoutez des éléments au panier avant d'utiliser cette fonctionnalité.");
            return;
        }
        
        $scope.saveUserOptimisation(3);

        cart.sortCartByStore();

        var formData = new FormData();
        var content = ctrl.getCartHtmlContent();
        formData.append("content", content);
        // Send request to server to get optimized list 	
        $http.post(appService.siteUrl.concat("/cart/mail_user_cart"), 
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

    $scope.sendListAsSMS = function($event)
    {
        if(!appService.isUserLogged)
        {
            return;
        }

        if(parseInt(appService.loggedUser.phone_verified) === 0)
        {
            $scope.showAlert($event, "Votre numéro de téléphone n'est pas vérifié", "Votre numéro de téléphone n'est pas vérifié. Veuillez consulter l'onglet de sécurité de votre compte pour vérifier votre numéro de téléphone.");
            return;
        }

        if(appService.cart.length === 0)
        {
            $scope.showAlert($event, "Panier vide", "Votre panier est actuellement vide. Ajoutez des éléments au panier avant d'utiliser cette fonctionnalité.");
            return;
        }
        
        $scope.saveUserOptimisation(1);

        cart.sortCartByStore();

        var formData = new FormData();
        formData.append("sms", ctrl.getListAsText());
        // Send request to server to get optimized list 	
        $scope.promise = $http.post(appService.siteUrl.concat("/cart/send_sms"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            if(response.data)
            {
                $scope.showAlert($event, "Message envoyé", "Votre liste d'épicerie a été envoyée à votre téléphone.");
            }
        });
    };

    $scope.printCart = function($event) 
    {
        if(appService.cart.length === 0)
        {
            $scope.showAlert($event, "Panier vide", "Votre panier est actuellement vide. Ajoutez des éléments au panier avant d'utiliser cette fonctionnalité.");
            return;
        }
        
        if(appService.isUserLogged)
        {
            $scope.saveUserOptimisation(0);
        }

        var mywindow = window.open('', 'PRINT');

        mywindow.document.write(ctrl.getCartHtmlContent());

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
        
        if(!profileData.instance.cartView)
        {
            for(var x in $scope.selectedStore.store_products)
            {
                var store_product = $scope.selectedStore.store_products[x].store_product;

                if(!angular.isNullOrUndefined(store_product) && $scope.selectedStore.store_products[x].quantity > 0)
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

                        $scope.productCategories[index].products.push($scope.selectedStore.store_products[x]);

                    }
                    else
                    {
                        // create category
                        category.products = [];
                        category.products.push($scope.selectedStore.store_products[x]);
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
    
    ctrl.getCartHtmlContent = function()
    {

        var content = "";

        cart.sortCartByStore();
        
        var siteLogo = $scope.base_url.concat("assets/img/logo.png");
        
        content += '<style> tr:nth-child(even){ background-color : #f2f2f2;} @media print{ body{ -webkit-print-color-adjust: exact;} } </style>';
        
        content += '<html><head><title style="font-style: italic; color : #444; ">OtiPrix - All RIghts Reserved</title>';
        content += '</head><body >';
        content += '<div style="text-align : center; width : 100%; padding : 10px; background-color : #1abc9c !important;"><img style="display = block; margin : auto; width : 60px;" src="' + siteLogo + '" /></div>';
        content += "<h4 style='text-align : center; color : #444; color : #1abc9c;'>OtiPrix - Liste d'épicerie optimisé</h4>";

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
                    
                    content += '<p style="padding : 10px;"><span><input type="checkbox" style="margin-right : 5px;"></span><span>' + category.products[k].quantity + ' x </span><b style="color : #1abc9c;">' + storeProduct.product.name + ' </b> (' + storeProduct.format + ' ' + unit + ' à ' + storeProduct.price + ' C $) <span style="float : right"><b style="font-size : 16px;">' + parseFloat(storeProduct.price) * parseFloat(category.products[k].quantity) + ' C$</b></span></p>';
                    
                    content += '</td></tr>';
                    
                }
                
                content += '</table>';
            }
        }
        
        
        
        var total_price = Math.round(parseFloat(cart.getCartPrice()) * 100) / 100;

        content += "<br>";
        content += "<br>";
        content += "<p style='float : right;'><b><span>Totale : <span><span style=' color : red;'> " + total_price + " C $ <span> + taxes. </b></p>";

        if($scope.price_optimization > 0)
        {
            var economy = Math.round(parseFloat($scope.price_optimization) * 100) / 100;
            content += "<p style='float : right; color : red;'><b>Vous économiserez environs : " + economy + " C $ </b></p>";
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
        $scope.promise = $http.post(appService.siteUrl.concat("/cart/save_user_optimisation"), 
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
        profileData.instance.set("cartDistance", newDistance);
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
                        appService.loggedUser = response.data;
                        $scope.update_cart_list();
                    }
                });
            }
            else
            {
                // Change in the session
                window.localStorage.setItem('cart_distance', $scope.default_distance);
                $scope.update_cart_list();
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
        
        
        for(var i in appService.cart)
        {
            var data = 
            {
                store_product_id : appService.cart[i].store_product.id,
                product : appService.cart[i].store_product.product.id,
                quantity : appService.cart[i].quantity
            };
            
            items.push(data);
        }
        
        return items;
    }
    
    $scope.viewProduct = function(product_id, ev)
    {
        eapp.viewProduct($scope, product_id, ev);
    };
    
    
    $scope.selectProduct = function(store_product)
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
        
        profileData.set("cartFilterSettings", profileData.instance.cartFilterSettings);
        
        // Get store filter
        $scope.createResultsFilter();
        
        $scope.update_cart_list();
        
    };
    
    $scope.refresh = function(userProfileData)
    {
        profileData.reset(userProfileData);
        
        $scope.update_cart_list();
    };
    
    $scope.updateItemChanged = function(item)
    {
        var index = profileData.cartFilterSettings[item.type].values.map(function(e){ return e.name; }).indexOf(item.name);
        
        if(index > -1)
        {
            profileData.cartFilterSettings[item.type].values[index] = item;
        }
    };
});


