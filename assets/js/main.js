jQuery(document).ready(function($){
    
    // jQuery sticky Menu
    $(".mainmenu-area").sticky({topSpacing:0});
    
    $('.product-carousel').owlCarousel({
        loop:true,
        nav:true,
        margin:20,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
            },
            600:{
                items:3,
            },
            1000:{
                items:5,
            }
        }
    });  
    
    $('.related-products-carousel').owlCarousel({
        loop:true,
        nav:true,
        margin:20,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
            },
            600:{
                items:2,
            },
            1000:{
                items:2,
            },
            1200:{
                items:3,
            }
        }
    });  
    
    $('.brand-list').owlCarousel({
        loop:true,
        nav:true,
        margin:20,
        responsiveClass:true,
        responsive:{
            0:{
                items:1,
            },
            600:{
                items:3,
            },
            1000:{
                items:4,
            }
        }
    });    
       
});

function convert_to_string_date(date)
{
    return date.getFullYear().toString() + "-" + date.getMonth().toString() + "-" + date.getDate().toString();
}

// Define the `eapp Application` module
var eappApp = angular.module('eappApp', ['ngMaterial', 'md.data.table', 'lfNgMdFileInput', 'mdCountrySelect', 'ngNotificationsBar', 'ngAnimate']);

eappApp.factory('Form', [ '$http', 'notifications', function($http, notifications) 
{
    this.postForm = function (formData, url, redirect_url) 
    {       
        $http({
            url: url,
            method: 'POST',
            data: formData,
            //assign content-type as undefined, the browser
            //will assign the correct boundary for us
            headers: { 'Content-Type': undefined},
            //prevents serializing payload.  don't do it.
            transformRequest: angular.identity
        }).
        then(
        function successCallback(response) 
        {
            
            if(response.data.success)
            {
                if(redirect_url != null)
                {
                    window.location.href = redirect_url;
                }
                
                notifications.showSuccess(response.data.message);
            }
            else
            {
                notifications.showError(response.data.message);
            }
            
        }, 
        function errorCallback(response) 
        {
            notifications.showError("An unexpected server error occured. Please try again later. ");
        });
    };
    
    return this;
}]);

eappApp.controller('ProductsController', ['$scope','$rootScope', function($scope, $rootScope) {
  
    /**
     * This are the products displayed on the home page. The most recent products.
     */
    $scope.products = [];
    
    /**
     * Products currently in the cart
     */
    $scope.cart_items = [];
    
    $scope.add_to_cart = function(product_id)
    {
        
    };
    
    $scope.remove_to_cart = function(product_id)
    {
        
    };
    
    $scope.cart_total = function()
    {
        
    };
  
}]);

eappApp.controller('CartController', ['$scope','$rootScope', '$q', '$http', function($scope, $rootScope, $q, $http) {
  
    $rootScope.viewing_cart_optimization = true;
    
    $rootScope.selected = [];
    
    $rootScope.query = {
      order: 'nameToLower',
      limit: 5,
      page: 1
    };
    
    $rootScope.cart = [];
    
    $rootScope.optimized_cart = [];
    
    // Here we define the default desired distance of the user	
    $rootScope.distance_from_home = 10;
    
    $rootScope.distance = 10;
    
    $rootScope.distance_updated = function(new_distance)
    {
        $rootScope.distance = new_distance;
    };
    
    $rootScope.true_value = true;
    $rootScope.false_value = false;
    
    $rootScope.updateCartList = function()
    {
        $rootScope.viewing_cart_optimization = true;
        // Create array with selected store_product id's
        var store_products = [];
        // Get optimized list here
        for(var index in $rootScope.cart)
        {
            var cartItem = $rootScope.cart[index];
            var data = 
            {
                    id : cartItem.store_product.id,
                    rowid : cartItem.rowid,
                    quantity : cartItem.quantity
            };
            store_products.push(data);
        }

        var formData = new FormData();
        formData.append("store_products", JSON.stringify(store_products));
        formData.append("distance", $rootScope.distance);
        // Send request to server to get optimized list 	
        $rootScope.promise = 
            $http.post("http://"+ $scope.site_url.concat("/cart/getOptimizedList"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                $rootScope.optimized_cart = response.data;
            });
        
    };
    
    $scope.loading_store_products = false;
    
    $rootScope.update_product_list_by_store = function()
    {
        $rootScope.viewing_cart_optimization = false;
        $scope.loading_store_products = true;
        // Create array with selected store_product id's
        var store_products = [];
        // Get optimized list here
        for(var index in $rootScope.cart)
        {
            var cartItem = $rootScope.cart[index];
            var data = 
            {
                id : cartItem.store_product.id,
                rowid : cartItem.rowid,
                quantity : cartItem.quantity
            };
            store_products.push(data);
        }

        var formData = new FormData();
        formData.append("store_products", JSON.stringify(store_products));
        formData.append("distance", $scope.distance);
        // Send request to server to get optimized list 	
        $scope.store_cart_promise = 
            $http.post("http://"+ $scope.site_url.concat("/cart/optimize_product_list_by_store"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                
                $scope.close_stores = response.data.close_stores;
                $scope.store_products = response.data.products;
                
                for(var index in $scope.store_products)
                {
                    for(var key in $scope.store_products[index].store_products)
                    {
                        if($scope.store_products[index].store_products[key] === null || typeof $scope.store_products[index].store_products[key] === 'undefined')
                        {
                           $scope.store_products[index].store_products[key] = 
                           {
                               id : key,
                               price : 'item pas disponible dans ce magasin. '
                               
                           };
                        }
                    }
                }
                
                var close_stores_array = $.map($scope.close_stores, function(value, index) {
                    return [value];
                });
                
                var store_products_array = $.map($scope.store_products, function(value, index) {
                    return [value];
                });
                
                $scope.close_stores = close_stores_array;
                $scope.store_products = store_products_array;
                
                $scope.loading_store_products = false;
                
            });
        
    };
    
    $rootScope.getCart = function()
    {
        
        return $scope.cart;
        
        var deferred = $q.defer();
        
        if($scope.optimized_cart)
        {
            // Create array with selected store_product id's
	    var store_products = [];
            // Get optimized list here
	    for(var index in $rootScope.cart)
	    {
	    	var cartItem = $rootScope.cart[index];
		var data = 
		{
			id : cartItem.store_product.id,
			rowid : cartItem.rowid,
			quantity : cartItem.quantity
		};
		store_products.push(data);
	    }
	    
	    var formData = new FormData();
	    formData.append("store_products", JSON.stringify(store_products));
	    formData.append("distance", $scope.distance);
	    // Send request to server to get optimized list 	
	    $scope.promise = $http.post("http://"+ $scope.site_url.concat("/cart/getOptimizedList"), formData, {
				transformRequest: angular.identity,
				headers: {'Content-Type': undefined}}).then(
                            function(response)
                            {
                                $rootScope.cart = response.data;
                            });
        }
        else
        {
	    // In this case the user did not request an optimized list, return the cart list	
            deferred.resolve($rootScope.cart);
	    $scope.promise = deferred.promise;
            $scope.promise.then(function(cart)
            {
                // assign rootscope cart to isolate scope
                $scope.cart = cart;
            });
        }
        
        return $scope.promise;
    };
    
    $rootScope.getCartTotal = function()
    {
	var total = 0;
	    
    	for(var key in $rootScope.cart)
	{
            total += parseFloat($rootScope.cart[key].store_product.price);
	}
	    
	return total;
    };
    
    $rootScope.addProductToCart = function(product_id)
    {

	var data = 
	{
		product_id : product_id
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
                        product : response_data.product,
                        retailer : response_data.retailer,
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
    }
    
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
    
    $rootScope.removeProductFromCart = function(product_id)
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
				      
     $rootScope.canAddToCart = function(product_id)
     {
        for(var key in $rootScope.cart)
	{
            if(parseInt($rootScope.cart[key].store_product.id) === parseInt(product_id))
            {
                return false;
            }
	}
	
	return true;
     };
  
}]);

eappApp.controller('HomeController', ["$scope","$rootScope", function($scope, $rootScope) 
{
  	$scope.addProductToCart = function(id)
	{
	
	}
	
	$scope.viewProductDetails = function(id)
	{
	
	}
  
}]);


