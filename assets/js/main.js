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

// Define the `eapp Application` module
var eappApp = angular.module('eappApp', ['ngMaterial', 'md.data.table', 'lfNgMdFileInput', 'angularCountryState', 'ngNotificationsBar']);

eappApp.factory('Form', [ '$http', 'notifications', function($http, notifications) 
{
    this.postForm = function (formID, url, redirect_url) 
    {
        var form = document.getElementById(formID);
        
        var formData = new FormData(form);
        
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

eappApp.controller('AdminController', ['$scope', 'Form', '$http', 'notifications', function($scope, Form, $http, notifications) {
      
    /**
     * When true, the user will continue creating other
     * products after creating te current one. 
     */  
    $scope.continue = false;
    
    $scope.product = null;
    
    $scope.product_selected = function()
    {
        $scope.product = $scope.products[$scope.store_product.product_id];
        
        var image_url = "http://" + $scope.base_url.concat("/assets/img/products/") + $scope.product.image;
        
        $scope.api.removeAll();
        
        $scope.api.addRemoteFile(image_url, $scope.product.image,'image');
    };
    
    $scope.organic_select = 
    [
        {
            id: -1,
            name: "NA"
        },
        {
            id: 1,
            name: "No"
        },
        {
            id: 2,
            name: "Yes"
        }
        
    ];
    
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
                notifications.showSuccess("New Image Uploaded!!!");
                // do sometingh                   
            },function(err){
                // do sometingh
            });
            
        }
        
        var redirect_url = null;
        
        if($scope.continue)
        {
            $scope.store_product.compareunit_id = $scope.compareunits[1].id;
            $scope.store_product.unit_id = $scope.units[1].id;
            $scope.store_product.format = "1x1";
            $scope.store_product.product_id = $scope.products[1].id;
            $scope.store_product.price = 0;
            $scope.store_product.brand = "";
            
            $scope.product_selected();
            $scope.updateQuantity();
            $scope.updateUnitPrice();
            
            window.location.hash = "admin-container";
        }
        else
        {
            redirect_url = "http://" + $scope.site_url.concat("/admin/store_products");
        }
        
        var url = "http://" + $scope.site_url.concat("/admin/create_store_product");
        Form.postForm("create_store_product_form", url, redirect_url);
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
        Form.postForm("create_store_form", url, null);
    };
    
    $scope.upload_products = function()
    {
        var url = "http://" + $scope.site_url.concat("/admin/upload_products");
        Form.postForm("upload_products_form", url, null);
    };
    
    $scope.upload_chains = function()
    {
        var url = "http://" + $scope.site_url.concat("/admin/upload_chains");
        Form.postForm("upload_chains_form", url, null);
    };
    
    $scope.upload_stores = function()
    {
        var url = "http://" + $scope.site_url.concat("/admin/upload_stores");
        Form.postForm("upload_stores_form", url, null);
    };
    
    $scope.upload_categories = function()
    {
        var url = "http://" + $scope.site_url.concat("/admin/upload_categories");
        Form.postForm("upload_categories_form", url, null);  
    };
    
    $scope.upload_categories = function()
    {
        var url = "http://" + $scope.site_url.concat("/admin/upload_categories");
        Form.postForm("upload_categories_form", url, null);  
    };
    
    $scope.upload_units = function()
    {
        var url = "http://" + $scope.site_url.concat("/admin/upload_units");
        Form.postForm("upload_units_form", url, null);  
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



