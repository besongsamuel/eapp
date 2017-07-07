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
    
    // Bootstrap Mobile Menu fix
    $(".navbar-nav li a").click(function(){
        $(".navbar-collapse").removeClass('in');
    });    
    
    // jQuery Scroll effect
    $('.navbar-nav li a, .scroll-to-up').bind('click', function(event) {
        var $anchor = $(this);
        var headerH = $('.header-area').outerHeight();
        $('html, body').stop().animate({
            scrollTop : $($anchor.attr('href')).offset().top - headerH + "px"
        }, 1200, 'easeInOutExpo');

        event.preventDefault();
    });    
    
    // Bootstrap ScrollPSY
    $('body').scrollspy({ 
        target: '.navbar-collapse',
        offset: 95
    });
    
    // Slider
    $("#distance-slider").slider({tooltip: 'always'});
    
});

function convert_to_string_date(date)
{
    return date.getFullYear().toString() + "-" + date.getMonth().toString() + "-" + date.getDate().toString();
}

// Define the `eapp Application` module
var eappApp = angular.module('eappApp', ['ngMaterial', 'md.data.table', 'lfNgMdFileInput', 'mdCountrySelect', 'ngNotificationsBar']);

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

eappApp.controller("ShopController", ["$scope", "$rootScope", function($scope, $rootScope)
{
    $scope.filteredProducts = []
    ,$scope.currentPage = 1
    ,$scope.numPerPage = 10
    ,$scope.maxSize = 25;
    
    $scope.products = [];

    $scope.$watch("currentPage + numPerPage", function() {
      var begin = (($scope.currentPage - 1) * $scope.numPerPage)
      , end = begin + $scope.numPerPage;

      $scope.filteredProducts = $scope.products.slice(begin, end);
    });
}]);

eappApp.controller('CartController', ['$scope','$rootScope', '$q', '$http', function($scope, $rootScope, $q, $http) {
  
    $scope.selected = [];

    $scope.query = {
      order: 'nameToLower',
      limit: 5,
      page: 1
    };
    
    $rootScope.cart = [];
    
    $scope.optimized_cart = 0;
        
    // Here we define the default desired distance of the user	
    $scope.distance = 10;
    
    $scope.updateCartList = function()
    {
        var deferred = $q.defer();
        
        var optimized_cart= Boolean(parseInt($scope.optimized_cart));
        
        if(optimized_cart)
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
	    $scope.promise = 
                $http.post("http://"+ $scope.site_url.concat("/cart/getOptimizedList"), 
                formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
                function(response)
                {
                    $scope.cart = response.data;
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
    }
    
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
    }
    
    $scope.getRowID = function(product_id)
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
    
    $scope.removeItemFromCart = function(product_id)
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


