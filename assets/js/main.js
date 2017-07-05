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

eappApp.controller('ProductsTableController', ['$scope', '$q', '$http', function($scope, $q, $http) 
{
    $scope.selected = [];

    $scope.query = {
      order: 'name',
      limit: 5,
      page: 1
    };
    
  $scope.delete_store_product = function(store_product_id)
  {
	   	var formData = new FormData();
        
		formData.append("id", store_product_id);

		$scope.promise = $http.post("http://"+ $scope.site_url.concat("/admin/delete_store_product"), formData, {
				transformRequest: angular.identity,
				headers: {'Content-Type': undefined}});

		$scope.promise.then(function(payload)
		{
			// Refresh list
  			$scope.getProducts();
		});
  }
	
    $scope.getProducts = function () 
    {
    
        var formData = new FormData();

        formData.append("limit", $scope.query.limit);

        formData.append("page", $scope.query.page);

        $scope.promise = $http.post("http://"+ $scope.site_url.concat("/admin/get_store_products"), formData, {
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}});

        $scope.promise.then(function(payload)
        {
            $scope.query_products = payload.data;
        });
      
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

eappApp.controller('AdminController', ["$scope", "Form", "$http", "notifications", "$q", function($scope, Form, $http, notifications, $q) {
      
    $scope.selectedProduct = null;
    $scope.searchProductText = "";
    $scope.queryProducts = [];
	
    $scope.querySearch = function(searchProductText)
    {
    	var q = $q.defer();
	var formData = new FormData();
	formData.append("name", searchProductText);

	$http.post("http://" + $scope.site_url.concat("/admin/searchProducts"), formData, {
	    transformRequest: angular.identity,
	    headers: {'Content-Type': undefined}
	}).then(function(response)
	{
		var array = $.map(response.data, function(value, index) {
		    return [value];
		});
		q.resolve( array );
		
	});
	
	return q.promise;
	    
    };
    
    	
    /**
     * When true, the user will continue creating other
     * products after creating te current one. 
     */ 
    
    $scope.store_product = 
    {
        id : -1
    };
    
    $scope.continue = false;
    
    $scope.product = null;
    
    $scope.default_country = 'CA';
    
    $scope.getSaveLabel = function(){ return parseInt($scope.store_product.id) > -1 ? "Edit" : "Create"};
    
    $scope.product_selected = function(item)
    {
        
        if(typeof item === 'undefined')
        	return;
        
        var image_url = "http://" + $scope.base_url.concat("/assets/img/products/") + item.image;
        
        $scope.api.removeAll();
        
        $scope.api.addRemoteFile(image_url, item.image,'image'); 

    };
    
    $scope.createNewBrand = function(brand_name)
    {
        //upload the image here
        var formData = new FormData();
        
        formData.append("name", brand_name);
        
        $http.post("http://" + $scope.site_url.concat("/admin/create_new_brand"), formData, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        }).then(function(result)
        {
            $scope.brands[result.data] = { id : result.data, name : brand_name } ;
            
            notifications.showSuccess("Brand " + brand_name + " created!!!");
            
            // do sometingh                   
        },function(err){
            // do sometingh
        });
    };
    
    $scope.create_store_product = function()
    {
        //upload the image here
        var formData = new FormData();
        angular.forEach($scope.files,function(obj){
            if(!obj.isRemote){
                formData.append('image', obj.lfFile);
            }
        });
        
        if($scope.files.length > 0)
        {
            formData.append("product_id", $scope.store_product.product_id);
        
            $http.post("http://" + $scope.site_url.concat("/admin/upload_product_image"), formData, {
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}
            }).then(function(result)
            {
                if(result.data.success)
                {
                    notifications.showSuccess(result.data.message);
                }
                else
                {
                    //notifications.showError(result.data.message);
                }
                
            },function(err)
            {
                // do sometingh
            });
            
        }
        
        var redirect_url = null;
        
        if($scope.continue)
        {
            redirect_url = "http://" + $scope.site_url.concat("/admin/create_store_product");
            redirect_url = redirect_url.concat("#admin-container");
        }
        else
        {
            redirect_url = "http://" + $scope.site_url.concat("/admin/store_products");
        }
        
        var url = "http://" + $scope.site_url.concat("/admin/create_store_product");
        var form = document.getElementById("create_store_product_form");
        var formData = new FormData(form);
        // Manually add organic and in flyer form fields
        formData.append("product[organic]", $scope.store_product.organic ? 1 : 0);
        formData.append("product[in_flyer]", $scope.store_product.in_flyer ? 1 : 0);
        formData.append("product[country]", $scope.store_product.country);
        if($scope.selectedProduct != null)
        {
        	formData.append("product[product_id]", $scope.selectedProduct.id);
        }
        Form.postForm(formData, url, redirect_url);
        
        if($scope.continue)
        {
            sessionStorage.setItem("retailer_id", $scope.store_product.retailer_id);
            sessionStorage.setItem("period_from", convert_to_string_date($scope.store_product.period_from));
            sessionStorage.setItem("period_to", convert_to_string_date($scope.store_product.period_to));
        }
    };
    
    $scope.updateQuantity = function()
    {
        if($scope.store_product.format === 'undefined' || $scope.store_product.format === null)
        {
            return 1;
        }
        
        var format = $scope.store_product.format.toLowerCase().split("x");
        
        $scope.store_product.quantity = 1;
        
        if(format.length === 1)
        {
            $scope.store_product.quantity = parseFloat(format[0]);
        }
        
        if(format.length === 2)
        {
            $scope.store_product.quantity = parseFloat(format[0]) * parseFloat(format[1]);
        }
        
        if(parseInt($scope.store_product.unit_id) > 0 && parseInt($scope.store_product.compareunit_id) > 0)
        {
            $scope.store_product.quantity = $scope.store_product.quantity * $scope.getEquivalent(parseInt($scope.store_product.unit_id), parseInt($scope.store_product.compareunit_id));
            return $scope.store_product.quantity;
        }
    };
    
    $scope.updateUnitPrice = function()
    {
        $scope.store_product.unit_price =  $scope.store_product.price / $scope.updateQuantity();
    };
    
    $scope.getEquivalent = function(unit_id, compareunit_id)
    {
        for(var key in $scope.units)
        {
            var unit = $scope.units[key];
            
            if(parseInt(unit.id) === parseInt(unit_id))
            {
                if(parseInt(unit.compareunit_id) === parseInt(compareunit_id))
                {
                    return parseFloat(unit.equivalent);
                }
            }
        }
        
        return 1;
    };
    
    $scope.selected_store = 1;
    
    $scope.create_store = function()
    {
        var url = "http://" + $scope.site_url.concat("/admin/create_store");
        var form = document.getElementById("create_store_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);
    };
    
    $scope.upload_products = function()
    {
        var url = "http://" + $scope.site_url.concat("/admin/upload_products");
        var form = document.getElementById("upload_products_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);
    };
    
    $scope.upload_chains = function()
    {
        var url = "http://" + $scope.site_url.concat("/admin/upload_chains");
        var form = document.getElementById("upload_chains_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);
    };
    
    $scope.upload_stores = function()
    {
        var url = "http://" + $scope.site_url.concat("/admin/upload_stores");
        var form = document.getElementById("upload_stores_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);
    };
    
    $scope.upload_categories = function()
    {
        var url = "http://" + $scope.site_url.concat("/admin/upload_categories");
        var form = document.getElementById("upload_categories_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);  
    };
    
    $scope.upload_subcategories = function()
    {
        var url = "http://" + $scope.site_url.concat("/admin/upload_subcategories");
        var form = document.getElementById("upload_categories_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);  
    };
    
    $scope.upload_units = function()
    {
        var url = "http://" + $scope.site_url.concat("/admin/upload_units");
        var form = document.getElementById("upload_units_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);  
    };
        
    $scope.getBrandMatches  = function(query)
    {
        var brands_array = $.map($scope.brands, function(value, index) {
            return [value];
        });
        
        var results = query ? brands_array.filter( createFilterFor(query) ) : $scope.brands;
          
        return  results;
        
    };
    
    /**
     * Create filter function for a query string
     */
    function createFilterFor(query) 
    {
      var lowercaseQuery = angular.lowercase(query);

      return function filterFn(brand) 
      {
        return (angular.lowercase(brand.name).indexOf(lowercaseQuery) === 0);
      };

    };
   
    
}]);
