/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("CartController", ["$scope","$rootScope", "$http", "$mdDialog","eapp", function($scope, $rootScope, $http, $mdDialog, eapp) 
{
    
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
    
    /**
    * When this is true, the user is viewing optimizations
    * based on the cart. When false, he is viewing optimization 
    * based on the closest stores. 
    */
    $scope.viewing_cart_optimization = { value: true};

    $scope.searchInMyList = { value: false};
    
    $rootScope.totalPriceAvailableProducts = 0;
    $rootScope.totalPriceUnavailableProducts = 0;
    
    /**
    * List of optimized cart store product items
    */
    $scope.optimized_cart = [];
       
    /**
     * This method initializes the cart
     * @returns {undefined}
     */
    $scope.Init = function()
    {
        $scope.viewing_cart_optimization.value = true;
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
        if($scope.viewing_cart_optimization.value)
        {
            $scope.update_cart_list();
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
            
            // Get the driving distances of each of the stores
            $scope.getStoreDrivingDistances();
        }
    };
        
    $scope.true_value = true;
    $scope.false_value = false;
    
    
    
    $scope.favoriteChanged = function(product)
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
        // Create array with selected store_product id's
        var store_products = [];
        // Get optimized list here
        for(var index in $rootScope.cart)
        {
            var cartItem = $rootScope.cart[index];
            var data = 
            {
                id : cartItem.product.id,
                rowid : cartItem.rowid,
                quantity : cartItem.quantity,
                store_product_id : cartItem.store_product_id
            };
            store_products.push(data);
        }
	
        // Clear the cart. 
        // It shall be repopulated with optimized products
        $rootScope.cart = [];
		
        var formData = new FormData();
        formData.append("products", JSON.stringify(store_products));
        formData.append("distance", $scope.getDistance());
        // User's longitude
        formData.append("longitude", $scope.longitude);
        // user's latitude
        formData.append("latitude", $scope.latitude);
        formData.append("searchAll", !$scope.searchInMyList.value);
        // Send request to server to get optimized list 	
        $scope.promise = 
            $http.post( $scope.site_url.concat("/cart/update_cart_list"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                // Create ordered array list
                for(var x in response.data)
                {
                    var cartItem = response.data[x];

                    if(angular.isNullOrUndefined(cartItem.store_product.related_products))
                    {
                        cartItem.store_product.related_products = [];
                    }
                    
                    var relatedProducts = $scope.getRelatedProducts(cartItem);
                    cartItem.different_store_products = relatedProducts[0];
                    cartItem.different_format_products = relatedProducts[1];
					
                    $rootScope.cart.push(cartItem);
                }
                
                $scope.results_available = !angular.isNullOrUndefined($scope.cart) && $scope.cart.length > 0;

                
                $rootScope.sortCart();
                
                // orders the stores and assigns the distance and times to each of the
                // department stores
                groupByStore();
                
                $scope.update_price_optimization();
                
            });
    };
	
    $scope.getRelatedProducts = function(cartItem)
    {
        var results = [];
        // split related products to store related and format related
        var different_format_products = [];
        var different_store_products = [];

        for(var i in cartItem.store_product.related_products)
        {
            if(parseInt(cartItem.store_product.retailer.id) !== parseInt(cartItem.store_product.related_products[i].retailer.id))
            {
                different_store_products.push(cartItem.store_product.related_products[i]);
            }
            
            if(cartItem.store_product.format.toString().trim() !== cartItem.store_product.related_products[i].format.toString().trim()
                    && parseInt(cartItem.store_product.retailer.id) === parseInt(cartItem.store_product.related_products[i].retailer.id))
            {
                different_format_products.push(cartItem.store_product.related_products[i]);
            }
        }

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

            if(parseFloat(storeProduct.price) === 0)
            {
                continue;
            }

            if(currentDepartmentStoreID !== parseInt(storeProduct.department_store.id))
            {
                $scope.departmenStores.push(storeProduct.department_store);
                
                $scope.departmenStores[$scope.departmenStores.length - 1].storeName = storeProduct.retailer.name;
                $scope.departmenStores[$scope.departmenStores.length - 1].image = storeProduct.retailer.image;
                
                if(parseFloat($scope.departmenStores[$scope.departmenStores.length - 1].distance) === 0)
                {
                    $scope.departmenStores[$scope.departmenStores.length - 1].range = 0;
                }
                
                $scope.departmenStores[$scope.departmenStores.length - 1].products = [];

                currentDepartmentStoreID = parseInt(storeProduct.department_store.id);
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
            var cart_item = $rootScope.cart[i];
            
            // Get the store product of the cart item
            var item = $rootScope.cart[i].store_product;
            
            // Check if it has related products
            if(item.related_products.length === 0)
            {
                var store_product = item;
                
                // check if the store for this related product has already been added to the array
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
                    var retailer = store_product.retailer;
                    retailer.department_store = store_product.department_store;
   
                    stores.push(retailer);
                    stores[stores.length - 1].store_products = [];
                    stores[stores.length - 1].store_products.push(cart_item);
                }
            }
            
            // each related product represents a store
            for(var x in item.related_products)
            {
                // get a product store product
                var store_product = item.related_products[x];
                
                store_product.related_products = item.related_products;
                
                cart_item.store_product = store_product;
                // check if the store for this related product has already been added to the array
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
                    var retailer = store_product.retailer;
                    retailer.department_store = store_product.department_store;
   
                    stores.push(retailer);
                    stores[stores.length - 1].store_products = [];
                    stores[stores.length - 1].store_products.push(cart_item);
                }
            }
        }
        
        // Get the list of missing products for each store
        for(var i in $rootScope.cart)
        {
            // Get a given store product
            var item = $rootScope.cart[i];
            
            for(var x in stores)
            {
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
        $scope.selectedStore = store;
        
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
            for(var x in store.store_products)
            {
                if(parseInt($rootScope.cart[i].store_product.product.id) === parseInt(store.store_products[x].product.id))
                {
                    $rootScope.cart[i].store_product = store.store_products[x].store_product;
                }
            }
            
            // reset the product price
            for(var x in store.missing_products)
            {
                if(parseInt($rootScope.cart[i].store_product.product.id) === parseInt(store.missing_products[x].product.id))
                {
                    $rootScope.cart[i].store_product = store.missing_products[x].store_product;
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

        for(var i in $scope.departmenStores )
        {
            var department_store = $scope.departmenStores[i];
            origins.push(new google.maps.LatLng(parseFloat($scope.loggedUser.profile.latitude), parseFloat($scope.loggedUser.profile.longitude)));
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
                    if(typeof response.rows[x].elements[0].status !== 'undefined' && response.rows[x].elements[0].status === "ZERO_RESULTS")
                    {
                        continue;
                    }
                    else
                    {
                        if(!angular.isNullOrUndefined(response.rows[0].elements[x].distance))
                        {
                            $scope.departmenStores[x].distance = response.rows[0].elements[x].distance.value;
                            $scope.departmenStores[x].distanceText = response.rows[0].elements[x].distance.text;
                            $scope.departmenStores[x].timeText = response.rows[0].elements[x].duration.text;
                            //$scope.departmenStores[x].fullName = response.destinationAddresses[x];
                            $scope.departmenStores[x].fullName = $scope.departmenStores[x].address + ', ' + $scope.departmenStores[x].state + ', ' + $scope.departmenStores[x].city + ', ' + $scope.departmenStores[x].postcode;
                        }
                        
                    }
                }
                $rootScope.totalPriceAvailableProducts = $scope.getCartTotalPrice(true);
                $rootScope.totalPriceUnavailableProducts = $scope.getCartTotalPrice(false);
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

        if($scope.viewing_cart_optimization.value)
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
    
    $scope.getStoreDrivingDistances = function()
    {
    	// construct ordered list of origins and destinations
        var origins = [];
        var destinations = [];
        var mode = "DRIVING";

        for(var i in $scope.stores.slice(0, 5))
        {
            var department_store = $scope.stores[i].department_store;
            origins.push(new google.maps.LatLng(parseFloat($scope.loggedUser.profile.latitude), parseFloat($scope.loggedUser.profile.longitude)));
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
                    if(typeof response.rows[x].elements[0].status !== 'undefined' && response.rows[x].elements[0].status === "ZERO_RESULTS")
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
	
    /**
     * Callback when the user wants to change the store
     * of a given store product. 
     * @param {type} ev
     * @param {type} currentStoreProduct
     * @returns {undefined}
     */    
    $scope.changeProductStore = function(ev, cartItem)
    {
        // The currentlu selected store product. 
        $scope.selectedStoreProduct = cartItem.store_product;
        $scope.different_store_products = cartItem.different_store_products;
        $scope.related_products = cartItem.store_product.related_products;
        // Show dialog for user to change the store of the product. 
        $mdDialog.show({
            controller: ChangeStoreController,
            templateUrl:  $scope.base_url + 'assets/templates/change-store-product.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose:true,
            disableParentScroll : true,
            preserveScope:true,
            scope : $scope,
            fullscreen: false //
          })
          .then(function(answer) {
                
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
            $scope.selectedStoreProduct.different_store_products = $scope.different_store_products;
            $scope.selectedStoreProduct.related_products = $scope.related_products;
            $scope.selectedStoreProduct.product.in_user_grocery_list = in_user_grocery_list;
        };

        $scope.selectStoreProduct = function() 
        {
            var currentStoreProduct = $scope.selectedStoreProduct;
            $scope.productChanged(currentStoreProduct);
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
    $scope.changeProductFormat = function(ev, cartItem)
    {
        $scope.selectedStoreProduct = cartItem.store_product;
	$scope.different_format_products = cartItem.different_format_products;
        $scope.related_products = cartItem.store_product.related_products;
        
        $mdDialog.show({
            controller: ChangeFormatController,
            templateUrl:  $scope.base_url + 'assets/templates/change-format-product.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose:true,
            disableParentScroll : true,
            preserveScope:true,
            scope : $scope,
            fullscreen: false //
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
        
        $scope.change = function(sp)
        {
            var in_user_grocery_list = $scope.selectedStoreProduct.product.in_user_grocery_list;
            $scope.selectedStoreProduct = sp;
            $scope.selectedStoreProduct.different_store_products = $scope.different_store_products;
            $scope.selectedStoreProduct.related_products = $scope.related_products;
            $scope.selectedStoreProduct.product.in_user_grocery_list = in_user_grocery_list;
        };

        $scope.selectStoreProduct = function() 
        {
            var currentStoreProduct = $scope.selectedStoreProduct;
            $scope.productChanged(currentStoreProduct);
            $mdDialog.hide();
        };
    };
    
    $scope.InitMap = function(ev, departmentStore)
    {
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
                var origin = {lat: parseFloat($scope.loggedUser.profile.latitude), lng: parseFloat($scope.loggedUser.profile.longitude)};
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
        for(var i in $rootScope.cart)
        {
            var item = $rootScope.cart[i];
            var mode = "DRIVING";
            
            if(parseInt(item.product.id) === parseInt(currentStoreProduct.product.id))
            {
                currentStoreProduct.related_products = $rootScope.cart[i].store_product.related_products;
                $rootScope.cart[i].store_product = currentStoreProduct;
                var relatedProducts = $scope.getRelatedProducts($rootScope.cart[i]);
                $rootScope.cart[i].different_store_products = relatedProducts[0];
                $rootScope.cart[i].different_format_products = relatedProducts[1];  
                
                $rootScope.sortCart();
                groupByStore();
                $rootScope.totalPriceAvailableProducts = $scope.getCartTotalPrice(true);
                $rootScope.totalPriceUnavailableProducts = $scope.getCartTotalPrice(false);
                
                break;
            }
        }
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
	
    $scope.update_price_optimization = function()
    {
        $scope.price_optimization = 0;

        for(var key in $scope.cart)
        {
            var cart_item = $scope.cart[key];

            if(typeof cart_item.store_product.worst_product === "undefined" || cart_item.store_product.worst_product === null)
            {
                continue;
            }
            $scope.price_optimization += (parseFloat(cart_item.store_product.worst_product.compare_price) - parseFloat(cart_item.store_product.compare_price)) * parseFloat(cart_item.quantity);// * parseFloat($scope.getFormat(cart_item.store_product));
        }
    };
    
    $scope.getCartItemRebate = function(cart_item)
    {
        if(typeof cart_item.store_product.worst_product === "undefined" || cart_item.store_product.worst_product === null)
        {
            return 0;
        }
        
        return (parseFloat(cart_item.store_product.worst_product.unit_price) - parseFloat(cart_item.store_product.unit_price));
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
            return;
        }

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

        if(!$rootScope.loggedUser.phone_verified)
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
        if(!$rootScope.isUserLogged)
        {
            return;
        }

        if($rootScope.cart.length === 0)
        {
            $scope.showAlert($event, "Panier vide", "Votre panier est actuellement vide. Ajoutez des éléments au panier avant d'utiliser cette fonctionnalité.");
            return;
        }
        
        $scope.saveUserOptimisation(0);

        var mywindow = window.open('', 'PRINT');

        mywindow.document.write($scope.getCartHtmlContent());

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();
        
        
        return true;

    };
    
    $scope.getCartHtmlContent = function()
    {

        var content = "";

        $rootScope.sortCart();

        content += '<html><head><title style="font-style: italic; color : #444; ">OtiPrix - All RIghts Reserved</title>';
        content += '</head><body >';
        content += "<h3 style='text-align : center; color : #444;'>OtiPrix - Liste d'épicerie optimisé</h3>";

        var currentDepartmentStoreID = -1;

        for(var x in $rootScope.cart)
        {
            var storeProduct = $rootScope.cart[x].store_product;

            if(parseFloat(storeProduct.price) === 0)
            {
                continue;
            }

            if(currentDepartmentStoreID !== parseInt(storeProduct.department_store.id))
            {
                if(currentDepartmentStoreID !== -1)
                {
                    content += "<br></div></ul>";
                }

                if(typeof storeProduct.department_store !== "undefined" && parseInt(storeProduct.department_store.distance) !== 0)
                {
                    var text = storeProduct.retailer.name + " - " + storeProduct.department_store.address + ", " + storeProduct.department_store.state + ", " + storeProduct.department_store.city + "," + storeProduct.department_store.postcode;
                    content += "<h4 style='color: #1abc9c; border-bottom-style: solid; border-width : 1px; border-color: gray; padding-bottom : 10px;'>" + text + "</h4>";
                }
                else
                {
                    content += "<h4 style='color: red; border-bottom-style: solid; border-width : 1px; border-color: gray; padding-bottom : 10px;'> " + storeProduct.retailer.name + " - Le magasin n'est pas proche de chez vous.</h4>";
                }

                currentDepartmentStoreID = parseInt(storeProduct.department_store.id);

                // Open new table
                content += "<div>";
                content += "<ul style='list-style-type: none;'>";
            }

            var description = "<p style='color : #444; font-style: italic;'>   - ";
            if(storeProduct.size)
            {
                description += " Taile : " + storeProduct.size;
            }
            if(storeProduct.brand)
            {
                description += " Marque : " + storeProduct.brand.name;
            }
            if(storeProduct.format)
            {
                description += " Format : " + storeProduct.format;
            }
            if(storeProduct.state)
            {
                description += " Origine : " + storeProduct.state;
            }

            var unit = "";

            if(storeProduct.unit)
            {
                unit += " / " + storeProduct.unit.name;
            }

            var price = Math.round(parseFloat(storeProduct.price) * 100) / 100;

            description += " Prix : <b> $ CAD " + price + unit + "</b></p>";

            var product_text = "<p><b>" + storeProduct.product.name +  "</b></p>" + description;

            content += "<li class='list-group-item'>" + product_text + "</li>";
        }

        if(currentDepartmentStoreID !== -1)
        {
            // Close last opened tag
            content += "</div>";
            content += "</ul>";
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


