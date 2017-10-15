/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("CartController", ["$scope","$rootScope", "$http", "$mdDialog", "$sce", function($scope, $rootScope, $http, $mdDialog, $sce) 
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
     * Callback method when the user changes his optimization preference
     * @returns void
     */
    $scope.optimization_preference_changed = function()
    {
        if($rootScope.viewing_cart_optimization.value)
        {
            $scope.update_cart_list();
        }
        else
        {
            $rootScope.stores = $scope.getListByStore();
            
            // Select the first store
            if($rootScope.stores.length > 0)
            {
                $scope.storeTabSelected($rootScope.stores[0]);
            }
            
            $scope.getStoreDrivingDistances();
            //$scope.update_product_list_by_store();
        }
    };
    
    /**
     * Set distance
     */
    $scope.distance = 4;
    
    $scope.true_value = true;
    $scope.false_value = false;
    
    $rootScope.$watch('cart', function(newValue, oldValue) 
    {
        //$scope.orderByStore();
    });
    
    $rootScope.remove_product_from_cart = function(product_id)
    {
        var formData = new FormData();
        formData.append("rowid", $rootScope.getRowID(product_id));
        
        $http.post
        ($rootScope.site_url.concat("/cart/remove"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(function(response)
        {
            if(Boolean(response.data.success))
            {
                $rootScope.removeItemFromCart(product_id);
                $scope.orderByStore();
            }
        });
    };
    
    /**
     * Updates the cart list by finding cheap products 
     * close to you
     * @returns {undefined}
     */
    $scope.update_cart_list = function()
    {
        // Clear items
        $rootScope.optimized_cart = [];
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
		
		$rootScope.cart = [];
		
        var formData = new FormData();
        formData.append("products", JSON.stringify(store_products));
        formData.append("distance", $scope.distance);
        formData.append("longitude", $scope.longitude);
        formData.append("latitude", $scope.latitude);
        formData.append("searchAll", !$rootScope.searchInMyList.value);
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

                    if(typeof cartItem.store_product.related_products === "undefined" || cartItem.store_product.related_products === null)
                    {
                        cartItem.store_product.related_products = [];
                    }

                    var relatedProducts = $scope.getRelatedProducts(cartItem);
                    cartItem.different_store_products = relatedProducts[0];
                    cartItem.different_format_products = relatedProducts[1];
					
                    $rootScope.cart.push(cartItem);
                    $scope.storeChanged(response.data[x].store_product);
                }
                
                $rootScope.sortCart();
                
                $scope.orderByStore();
                
                //$scope.getDrivingDistances();
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
    
    $scope.orderByStore = function()
    {
        var currentDepartmentStoreID = 0;
        
        $rootScope.departmenStores = [];
        
        for(var x in $rootScope.cart)
        {
            var storeProduct = $rootScope.cart[x].store_product;

            if(parseFloat(storeProduct.price) === 0)
            {
                continue;
            }

            if(currentDepartmentStoreID !== parseInt(storeProduct.department_store.id))
            {
                $rootScope.departmenStores.push(storeProduct.department_store);
                $rootScope.departmenStores[$rootScope.departmenStores.length - 1].storeName = storeProduct.retailer.name;
                $rootScope.departmenStores[$rootScope.departmenStores.length - 1].image = storeProduct.retailer.image;
                
                if(parseFloat($rootScope.departmenStores[$rootScope.departmenStores.length - 1].distance) === 0)
                {
                    $rootScope.departmenStores[$rootScope.departmenStores.length - 1].range = 0;
                }
                
                $rootScope.departmenStores[$rootScope.departmenStores.length - 1].products = [];

                currentDepartmentStoreID = parseInt(storeProduct.department_store.id);
            }
            
            $rootScope.departmenStores[$rootScope.departmenStores.length - 1].products.push($rootScope.cart[x]);
        }
        
        $rootScope.departmenStores.sort(function(a, b)
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
    };
    
    $scope.getListByStore = function()
    {
        var stores = [];
        
        for(var i in $rootScope.cart)
        {
            var cart_item = $rootScope.cart[i];
            
            var item = $rootScope.cart[i].store_product;
            
            cart_item.store_product = item;
            
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
        
        for(var i in $rootScope.cart)
        {
            var item = $rootScope.cart[i];
            
            for(var x in stores)
            {
                index = stores[x].store_products.map(function(e) { return e.product.id; }).indexOf(item.store_product.product.id); 
                
                // The product does not exist in that store
                if(index === -1)
                {
                    if(typeof stores[x].missing_products === 'undefined')
                    {
                        stores[x].missing_products = [];
                    }
                    
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
        $rootScope.selectedStore = store;
        
        for(var i in $rootScope.cart)
        {
            var related_products = $rootScope.cart[i].store_product.related_products;
            
            if(typeof related_products !== 'undefined')
            {
                // There are no related items. Skip this product
                if(related_products.length === 0)
                {
                    continue;
                }
                
                $rootScope.cart[i].store_product = related_products[related_products.length - 1];
                $rootScope.cart[i].store_product.related_products = related_products;
            }
            
            // reset the product price
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
        
        $scope.update_price_optimization();
    };
	
	// This method computes the distance to each product stores
    $scope.getDrivingDistances = function()
    {
    	// construct ordered list of origins and destinations
        var origins = [];
        var destinations = [];
        var mode = "DRIVING";

        for(var i in $rootScope.cart)
        {
            var currentStoreProduct = $rootScope.cart[i].store_product;
            origins.push(new google.maps.LatLng(parseFloat($scope.loggedUser.profile.latitude), parseFloat($scope.loggedUser.profile.longitude)));
            destinations.push(new google.maps.LatLng(parseFloat(currentStoreProduct.department_store.latitude), parseFloat(currentStoreProduct.department_store.longitude)));
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

            $rootScope.$apply(function()
            {
                for(var x in response.rows)
                {
                    var distance = 0;
                    var time = 0;
                    if(typeof response.rows[x].elements[0].status !== 'undefined' && response.rows[x].elements[0].status === "ZERO_RESULTS")
                    {
                        $rootScope.cart[x].store_product.department_store.time = time;
                        $rootScope.cart[x].store_product.department_store.distance = distance;
                        continue;
                    }
                    else
                    {
                        distance = parseFloat(response.rows[x].elements[0].distance.value) / 1000;
                        time = parseFloat(response.rows[x].elements[0].duration.value) / 60;
                    }

                    $rootScope.cart[x].store_product.department_store.time = time;
                    $rootScope.cart[x].store_product.department_store.distance = distance;
                }

                $scope.update_travel_distance();
            });

        });
	  
    };
    
    $scope.getStoreDrivingDistances = function()
    {
    	// construct ordered list of origins and destinations
        var origins = [];
        var destinations = [];
        var mode = "DRIVING";

        for(var i in $rootScope.stores)
        {
            var department_store = $rootScope.stores[i].department_store;
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

            $rootScope.$apply(function()
            {
                for(var x in response.rows)
                {
                    var distance = 0;
                    var time = 0;
                    if(typeof response.rows[x].elements[0].status !== 'undefined' && response.rows[x].elements[0].status === "ZERO_RESULTS")
                    {
                        $rootScope.stores[x].department_store.distance = distance;
                        $rootScope.stores[x].department_store.time = time;
                        continue;
                    }
                    else
                    {
                        distance = parseFloat(response.rows[x].elements[0].distance.value) / 1000;
                        time = parseFloat(response.rows[x].elements[0].duration.value) / 60;
                    }
                    
                    $rootScope.stores[x].department_store.distance = distance;
                    $rootScope.stores[x].department_store.time = time;

                    
                }
                $scope.update_travel_distance();
            });

        });
	  
    };
	
    $scope.productStoreChanged = function(ev, currentStoreProduct)
    {
        $scope.selectedStoreProduct = currentStoreProduct.store_product;
	$scope.selectedStoreProduct.different_store_products = 	currentStoreProduct.different_store_products;
        $mdDialog.show({
            controller: ChangeStoreController,
            templateUrl:  $scope.base_url + 'assets/templates/change-store-product.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose:true,
            preserveScope:true,
            scope : $scope,
            fullscreen: false //
          })
          .then(function(answer) {
                
          }, function() {
                
          });
    };
    
    $scope.productFormatChanged = function(ev, currentStoreProduct)
    {
        $scope.selectedStoreProduct = currentStoreProduct.store_product;
	$scope.selectedStoreProduct.different_format_products = currentStoreProduct.different_format_products;
        $mdDialog.show({
            controller: ChangeFormatController,
            templateUrl:  $scope.base_url + 'assets/templates/change-format-product.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose:true,
            preserveScope:true,
            scope : $scope,
            fullscreen: false //
          })
          .then(function(answer) {
                
          }, function() {
                
          });
    };
    
    $scope.InitMap = function(ev, departmentStore)
    {
        $mdDialog.show({
            controller: GoogleMapsController,
            templateUrl:  $scope.base_url + 'assets/templates/google-map.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose:true,
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
            var related_store_products = $scope.selectedStoreProduct.different_store_products;
            var related_products = $scope.selectedStoreProduct.related_products;
            var in_user_grocery_list = $scope.selectedStoreProduct.product.in_user_grocery_list;
            $scope.selectedStoreProduct = sp;
            $scope.selectedStoreProduct.different_store_products = related_store_products;
            $scope.selectedStoreProduct.related_products = related_products;
            $scope.selectedStoreProduct.product.in_user_grocery_list = in_user_grocery_list;
        };

        $scope.selectStoreProduct = function() 
        {
            var currentStoreProduct = $scope.selectedStoreProduct;
            $scope.storeChanged(currentStoreProduct);
            $mdDialog.hide();
        };
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
            var related_store_products = $scope.selectedStoreProduct.different_format_products;
            var related_products = $scope.selectedStoreProduct.related_products;
            var in_user_grocery_list = $scope.selectedStoreProduct.product.in_user_grocery_list;
            $scope.selectedStoreProduct = sp;
            $scope.selectedStoreProduct.different_format_products = related_store_products;
            $scope.selectedStoreProduct.related_products = related_products;
            $scope.selectedStoreProduct.product.in_user_grocery_list = in_user_grocery_list;
        };

        $scope.selectStoreProduct = function() 
        {
            var currentStoreProduct = $scope.selectedStoreProduct;
            $scope.storeChanged(currentStoreProduct);
            $mdDialog.hide();
        };
    };
    
    $scope.storeChanged = function(currentStoreProduct)
    {
        for(var i in $rootScope.cart)
        {
            var item = $rootScope.cart[i];
			var mode = "DRIVING";
            
            if(parseInt(item.product.id) === parseInt(currentStoreProduct.product.id))
            {
                currentStoreProduct.related_products = $rootScope.cart[i].store_product.related_products;
                
                var origin = new google.maps.LatLng(parseFloat(currentStoreProduct.department_store.latitude), parseFloat(currentStoreProduct.department_store.longitude));
                var destination = new google.maps.LatLng(parseFloat($scope.loggedUser.profile.latitude), parseFloat($scope.loggedUser.profile.longitude));
                
                var service = new google.maps.DistanceMatrixService();
                service.getDistanceMatrix(
                {
                    origins: [origin],
                    destinations: [destination],
                    travelMode: mode,
                    avoidHighways: false,
                    avoidTolls: false
                }, function(response, status)
                {
                    
                    var distance = 0;
                    var time = 0;
                    if(typeof response.rows[0].elements[0].status !== 'undefined' && response.rows[0].elements[0].status === "ZERO_RESULTS")
                    {
                        distance = 0;
                        time = 0;
                    }
                    else
                    {
                        distance = parseFloat(response.rows[0].elements[0].distance.value) / 1000;
                        time = parseFloat(response.rows[0].elements[0].duration.value) / 60;
                    }
                    
                    currentStoreProduct.department_store.distance = distance;
                    currentStoreProduct.department_store.time = time;
                    $rootScope.$apply(function()
                    {
                        $rootScope.cart[i].store_product = currentStoreProduct;
						var relatedProducts = $scope.getRelatedProducts($rootScope.cart[i]);
						$rootScope.cart[i].different_store_products = relatedProducts[0];
						$rootScope.cart[i].different_format_products = relatedProducts[1];
                        $rootScope.sortCart();
                        $scope.orderByStore();
                        $scope.update_price_optimization();
                    });
                    
                });
                
                break;
            }
        }
    };
	
    $scope.update_price_optimization = function()
    {
        $scope.distance_optimization = 0;
        $scope.price_optimization = 0;

        for(var key in $scope.cart)
        {
            var cart_item = $scope.cart[key];

            if(typeof cart_item.store_product.worst_product === "undefined" || cart_item.store_product.worst_product === null)
            {
                continue;
            }
            $scope.price_optimization += (parseFloat(cart_item.store_product.worst_product.unit_price) - parseFloat(cart_item.store_product.unit_price)) * parseFloat(cart_item.quantity);
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
    
    $scope.update_travel_distance = function()
    {
        var traval_distance = 0;
        var stores = [];
        
        for(var key in $rootScope.cart)
        {
            var product = $rootScope.cart[key];
            
            if(typeof product.store_product.department_store !== 'undefined' && $.inArray(product.store_product.department_store.id, stores) === -1)
            {
                stores.push(product.store_product.department_store.id);
                traval_distance += parseInt(product.store_product.department_store.distance);
            }
        }
        
        $rootScope.travel_distance = traval_distance;
    };
    
    $rootScope.clearCart = function($event)
    {
        var confirmDialog = $rootScope.createConfirmDIalog($event, "Cela effacera tous les contenus de votre panier.");
        
        $mdDialog.show(confirmDialog).then(function() 
        {
            $http.post($rootScope.site_url.concat("/cart/destroy"), null).then(function(response)
            {
                $rootScope.cart = [];
                $rootScope.stores = [];
                $rootScope.departmenStores = [];
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
    
    $rootScope.getCartContents = function()
    {                
        var formData = new FormData();
        formData.append("longitude", $rootScope.longitude);
        formData.append("latitude", $rootScope.latitude);
        // Send request to server to get optimized list 	
        $scope.promise = $http.post($scope.site_url.concat("/cart/get_cart_contents"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            if(response.data)
            {
                $rootScope.cart = response.data;
            }
        });
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
    
    $scope.selectProduct = function(store_product)
    {
        window.location = $scope.site_url.concat("/cart/product/").concat(store_product.product.id).concat("/").concat(store_product.id);
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
	
}]);


