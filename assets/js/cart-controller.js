/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("CartController", ["$scope","$rootScope", "$http", "$mdDialog", function($scope, $rootScope, $http, $mdDialog) 
{
    /**
     * When this is true, the user is viewing optimizations
     * based on the cart. When false, he is viewing optimization 
     * based on the closest stores. 
     */
    $rootScope.viewing_cart_optimization = { value: true};
    
    $rootScope.searchInMyList = false;
    
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
            $scope.update_product_list_by_store();
        }
    };
    
    /**
     * List of selected cart items
     */
    $rootScope.cart = [];
    
    /**
     * List of optimized cart store product items
     */
    $rootScope.optimized_cart = [];
    
    /**
     * Set distance
     */
    $scope.distance = 10;
    
    $scope.true_value = true;
    $scope.false_value = false;
    
    /**
     * When this variable is true, the application is loading store optimizations. 
     * We display the progress bar
     */
    $rootScope.loading_store_products = false;
    
    $rootScope.travel_distance = 0;
    
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
                    quantity : cartItem.quantity
            };
            store_products.push(data);
        }

        var formData = new FormData();
        formData.append("products", JSON.stringify(store_products));
        formData.append("distance", $scope.distance);
        formData.append("longitude", $scope.longitude);
        formData.append("latitude", $scope.latitude);
	formData.append("searchAll", !$rootScope.searchInMyList);
        // Send request to server to get optimized list 	
        $scope.promise = 
            $http.post("http://"+ $scope.site_url.concat("/cart/update_cart_list"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                $rootScope.cart = response.data;

                $rootScope.cart.sort(function(a, b)
                {
                    var a_retailer_name = a.store_product.retailer.name;
                    var b_retailer_name = b.store_product.retailer.name;
                    return a_retailer_name.toString().localeCompare(b_retailer_name.toString());
                });

                $scope.update_travel_distance();
            });
        
    };
    
    /**
     * Optimize product list by finding items in stores
     * close to you.
     * @returns {undefined}
     */
    $scope.update_product_list_by_store = function()
    {
        $rootScope.close_stores = [];
        $rootScope.store_products = [];
        $rootScope.loading_store_products = true;
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
                quantity : cartItem.quantity
            };
            store_products.push(data);
        }

        var formData = new FormData();
        formData.append("products", JSON.stringify(store_products));
        formData.append("distance", $scope.distance);
        formData.append("longitude", $scope.longitude);
        formData.append("latitude", $scope.latitude);
	formData.append("searchAll", !$rootScope.searchInMyList);
        // Send request to server to get optimized list 	
        $scope.store_cart_promise = 
            $http.post("http://"+ $scope.site_url.concat("/cart/optimize_product_list_by_store"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                
                $rootScope.close_stores = response.data.close_stores;
                $rootScope.store_products = response.data.products;
                                
                var close_stores_array = $.map($rootScope.close_stores, function(value, index) {
                    return [value];
                });
                
                var store_products_array = $.map($rootScope.store_products, function(value, index) {
                    return [value];
                });
                
                $rootScope.close_stores = close_stores_array;
                $rootScope.cart = store_products_array;
                $rootScope.loading_store_products = false;
                
            });
        
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
            
            if(typeof product.store_product.department_store !== 'undefined' && $.inArray(product.store_product.department_store.id, stores) == -1)
            {
                stores.push(product.store_product.department_store.id);
                traval_distance += parseInt(product.store_product.department_store.distance);
            }
        }
        
        $rootScope.travel_distance = traval_distance;
    };
      
    $rootScope.get_store_total = function(store_index)
    {        
        var total = 0;
        
        for(var key in $rootScope.cart)
        {
            //$rootScope.store_products[index].store_products
            total += 
                    !$rootScope.viewing_cart_optimization.value ? 
                        $rootScope.cart[key].store_products[store_index].price * $rootScope.cart[key].quantity : 
                        $rootScope.cart[key].store_product.price * $rootScope.cart[key].quantity;
        }
        
        return total;
    };
        
    $rootScope.get_cart_total_price = function()
    {
	var total = 0;
	    
    	for(var key in $rootScope.cart)
	{
            total += parseFloat($rootScope.cart[key].quantity * $rootScope.cart[key].store_product.price);
	}
	    
	return total;
    };
    
    $rootScope.get_cart_item_total = function()
    {
        var total = 0;
	    
    	for(var key in $rootScope.cart)
	{
            if(parseFloat($rootScope.cart[key].store_product.price) !== 0)
            {
                total++;
            }
	}
	    
	return total;
    };
    
    $rootScope.get_optimized_cart_details = function()
    {
	var total = 0;
	    
    	for(var key in $rootScope.optimized_cart)
	{
            total += parseFloat($rootScope.optimized_cart[key].quantity * $rootScope.optimized_cart[key].store_product.price);
	}
	    
	return total;
    };
    
    $rootScope.add_product_to_cart = function(product_id)
    {
	var data = 
	{
            product_id : product_id,
            longitude : $scope.longitude,
            latitude : $scope.latitude
	};
        
	$.ajax({
            type: 'POST',
            url:  "http://" + $scope.site_url.concat("/cart/insert"),
            data: data,
            success: function(response)
            {
                var response_data = JSON.parse(response);

                if(Boolean(response_data.success))
                {
                    // Add Global Cart list
                    var cart_item = 
                    {
                        rowid : response_data.rowid,
                        store_product : response_data.store_product,
                        top_five_store_products : [],
                        quantity : 1
                    };

                    // Get the root scope. That's where the cart will reside. 
                    var scope = angular.element($("html")).scope();

                    scope.$apply(function()
                    {
                        if (typeof scope.cart === 'undefined') 
                        {
                            // Create new cart. 
                            scope.cart = [];
                        }
                        
                        if(scope.cart == null || typeof scope.cart === 'undefined')
                        {
                            scope.cart = [];
                        }
                        
                        scope.cart.push(cart_item);
                    });
                }
            },
            async:true
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
    
    $rootScope.getRowID = function(product_id)
    {
        var rowid = -1;
        
        for(var key in $rootScope.cart)
	{
            if(parseInt($rootScope.cart[key].store_product.id) === parseInt(product_id))
            {
                rowid = $rootScope.cart[key].rowid;
                break;
            }
	}
        
        return rowid;
    };
    
    $rootScope.removeItemFromCart = function(product_id)
    {
        var index = -1;
        
        for(var key in $rootScope.cart)
	{
            if(parseInt($rootScope.cart[key].store_product.id) === parseInt(product_id))
            {
                index = key;
                break;
            }
	}
        
        if(index > -1)
        {
            $rootScope.cart.splice(index, 1);
        }
    };
    
    $rootScope.remove_product_from_cart = function(product_id)
    {
        
        var data = 
	{
            rowid : $scope.getRowID(product_id)
	};

	$.ajax({
            type: 'POST',
            url:  "http://" + $scope.site_url.concat("/cart/remove"),
            data: data,
            success: function(response)
            {
                var response_data = JSON.parse(response);

                if(Boolean(response_data.success))
                {
                    // Remove from Global Cart list
                    // Get the root scope. That's where the cart will reside. 
                    var scope = angular.element($("html")).scope();

                    scope.$apply(function()
                    {
                        $scope.removeItemFromCart(product_id);
                    });
                }
            },
            async:true
	});
    };
				      
     $rootScope.can_add_to_cart = function(product_id)
     {
        for(var key in $rootScope.cart)
	{
            if(parseInt($rootScope.cart[key].store_product.product_id) === parseInt(product_id))
            {
                return false;
            }
	}
	
	return true;
     }; 
     
 	$rootScope.getUserCoordinates = function()
	{
		// Get the current geo location only if it's not yet the case
        if ('https:' == document.location.protocol && "geolocation" in navigator && !window.localStorage.getItem("longitude") && !window.localStorage.getItem("latitude")) 
        {
            navigator.geolocation.getCurrentPosition(function(position) 
            {
                $rootScope.longitude = position.coords.longitude;
                $rootScope.latitude = position.coords.latitude;
                window.localStorage.setItem("longitude", $rootScope.longitude);
                window.localStorage.setItem("latitude", $rootScope.latitude);
                $rootScope.getCartContents();
            });
        }
		else
		{
			$rootScope.getCartContents();
		}
	}
         
    $rootScope.promptForZipCode = function(ev) 
    {
		$rootScope.longitude = null;
		$rootScope.latitude = null;
		
		if(!window.localStorage.getItem("longitude") && !window.localStorage.getItem("latitude"))
		{
			// Appending dialog to document.body to cover sidenav in docs app
			var confirm = $mdDialog.prompt()
			  .title('Veillez entrer votre code postale. ')
			  .textContent('Ceci vas aider a optimiser les resultats.')
			  .placeholder('Votre Code Postale E.g. H1H 1H1')
			  .ariaLabel('Code Postale')
			  .initialValue('')
			  .targetEvent(ev)
			  .ok('Valider!')
			  .cancel('Annuler');

			$mdDialog.show(confirm).then(function(result) 
			{
				var address = result;
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode( { 'address': address}, function(results, status) 
				{
					if (status === google.maps.GeocoderStatus.OK) 
					{
						 $rootScope.latitude = results[0].geometry.location.lat();
						 $rootScope.longitude = results[0].geometry.location.lng();
						 window.localStorage.setItem("longitude", $rootScope.longitude);
						 window.localStorage.setItem("latitude", $rootScope.latitude);
						 $rootScope.getCartContents();
					}
					else
					{
						$rootScope.getUserCoordinates();
					}
				});


			}, function() 
			{
				$rootScope.getUserCoordinates();
			});
		}
		else
		{
			$rootScope.getCartContents();
		}
  };
  
    $rootScope.getCartContents = function()
    {
        // get cart contents
		$.ajax(
		{
			type : 'POST',
			url : "http://" + $rootScope.site_url.concat("/cart/get_cart_contents"),
			data : { longitude : $rootScope.longitude, latitude : $rootScope.latitude},
			success : function(data)
			{
				if(data)
				{
					$rootScope.cart = JSON.parse(data);
				}
			},
			async : true
		});
    };
	
}]);
