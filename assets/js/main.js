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

eappApp.controller('CartController', ['$scope','$rootScope', '$http', function($scope, $rootScope, $http) {
  
    /**
     * When this is true, the user is viewing optimizations
     * based on the cart. When false, he is viewing optimization 
     * based on the closest stores. 
     */
    $rootScope.viewing_cart_optimization = { value: true};
    
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
        $scope.promise = 
                        $http.post("http://"+ $scope.site_url.concat("/cart/update_cart_list"), 
                        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
                        function(response)
                        {
                            $rootScope.optimized_cart = response.data;
                        });
        
    };
    
    /**
     * When this variable is true, the application is loading store optimizations. 
     * We display the progress bar
     */
    $rootScope.loading_store_products = false;
    
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
                
                $rootScope.close_stores = response.data.close_stores;
                $rootScope.store_products = response.data.products;
                
                for(var index in $rootScope.store_products)
                {
                    for(var key in $rootScope.store_products[index].store_products)
                    {
                        if($rootScope.store_products[index].store_products[key] === null || typeof $rootScope.store_products[index].store_products[key] === 'undefined')
                        {
                           $rootScope.store_products[index].store_products[key] = 
                           {
                               id : key,
                               price : 'item pas disponible dans ce magasin. '
                               
                           };
                        }
                    }
                }
                
                var close_stores_array = $.map($rootScope.close_stores, function(value, index) {
                    return [value];
                });
                
                var store_products_array = $.map($rootScope.store_products, function(value, index) {
                    return [value];
                });
                
                $rootScope.close_stores = close_stores_array;
                $rootScope.store_products = store_products_array;
                $rootScope.loading_store_products = false;
                
            });
        
    };
        
    $rootScope.getCartTotal = function()
    {
	var total = 0;
	    
    	for(var key in $rootScope.cart)
	{
            total += parseFloat($rootScope.cart[key].quantity * $rootScope.cart[key].store_product.price);
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

eappApp.controller('HomeController', ["$scope", function($scope) 
{
  
}]);


