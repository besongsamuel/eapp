/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller('AdminController', ["$scope", "Form", "$http", "notifications", "$q", function($scope, Form, $http, notifications, $q) {
      
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
		if(result.data.success)
		{
			$scope.brands[result.data] = { id : result.data.id, name : brand_name } ;
            
            		notifications.showSuccess(result.data.message);
		}
            
            // do sometingh                   
        },function(err){
            // do sometingh
        });
    };
	
    $scope.createNewProduct = function(product_name)
    {
        //upload the image here
        var formData = new FormData();
        
        formData.append("name", product_name);
        
        $http.post("http://" + $scope.site_url.concat("/admin/create_product"), formData, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        }).then(function(result)
        {
		
		if(!result.data.success)
			return;
		
            	$scope.products[result.data] = { id : result.data.id, name : product_name } ;
            
            	notifications.showSuccess(result.data.message);
		
	    	// Upload product image
	    	var formData = new FormData();
		angular.forEach($scope.files,function(obj){
		    if(!obj.isRemote){
			formData.append("image", obj.lfFile);
		    }
		});
        
		if($scope.files.length > 0)
		{
			if($scope.selectedProduct !== null)
			{
				formData.append("product_id", result.data);

				$http.post("http://" + $scope.site_url.concat("/admin/upload_product_image"), formData, {
					transformRequest: angular.identity,
					headers: {'Content-Type': undefined}
				}).then(
				function(response)
				{
					if(response.data.success)
					{
						notifications.showSuccess(response.data.message);
					}
							
				},function(err)
				{
				})
			}

		}
            
            // do sometingh                   
        },function(err){
            // do sometingh
        });
    };
    
    $scope.post_create_product = function()
    {
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
	    
	if($scope.continue)
        {
            sessionStorage.setItem("retailer_id", $scope.store_product.retailer_id);
            sessionStorage.setItem("period_from", convert_to_string_date($scope.store_product.period_from));
            sessionStorage.setItem("period_to", convert_to_string_date($scope.store_product.period_to));
		
	    $scope.selectedProduct = null;
	    $scope.api.removeAll();
	    var tmp = 
	    {
	    	id : -1,
		organic : 0,
		in_flyer : 0,
		format : "1x1",
		retailer_id : $scope.store_product.retailer_id,
		period_from : $scope.store_product.period_from,
		period_to : $scope.store_product.period_to,
	    }
	    $scope.store_product = tmp;
	    
        }
	    
        Form.postForm(formData, url, redirect_url);
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
			if($scope.selectedProduct !== null)
			{
				formData.append("product_id", $scope.selectedProduct.id);

				$http.post("http://" + $scope.site_url.concat("/admin/upload_product_image"), formData, {
					transformRequest: angular.identity,
					headers: {'Content-Type': undefined}
				}).then(
				function(result)
				{
					if(result.data.success)
					{
						notifications.showSuccess(result.data.message);
						$scope.post_create_product();
					}
					else
					{
						$scope.post_create_product();
						//notifications.showError(result.data.message);
					}		
		        },function(err)
		        {
					// do sometingh
					$scope.post_create_product();
		        })
			}
            
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


angular.module("eappApp").controller('ProductsTableController', ['$scope', '$q', '$http', function($scope, $q, $http) 
{
    $scope.selected = [];
  
    $scope.filter = 
    {
        options: 
        {
            debounce: 500
        }
    };

    $scope.query = 
    {
        filter: '',
        limit: '25',
        order: 'name',
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
    };
	
    $scope.getProducts = function () 
    {
        if(typeof $scope.site_url === 'undefined')
        {
            // Not ready
            return;
        }
        
        var formData = new FormData();

        formData.append("page", $scope.query.page);
        formData.append("limit", $scope.query.limit);
        formData.append("filter", $scope.query.filter);
        formData.append("order", $scope.query.order);
        $scope.promise = $http.post("http://"+ $scope.site_url.concat("/admin/get_store_products"), formData, {
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}});

        $scope.promise.then(function(payload)
        {
            var array = $.map(payload.data.products, function(value, index) {
              return [value];
            });
            $scope.query_products = array;
            $scope.count = payload.data.count;
        });
      
    };
    
    $scope.removeFilter = function () 
    {
        $scope.filter.show = false;
        $scope.query.filter = '';

        if($scope.filter.form.$dirty) 
        {
          $scope.filter.form.$setPristine();
        }
    };
  
    $scope.$watch('query.filter', function (newValue, oldValue) 
    {
        if(!oldValue) 
        {
            bookmark = $scope.query.page;
        }

        if(newValue !== oldValue) 
        {
            $scope.query.page = 1;
        }

        if(!newValue) 
        {
            $scope.query.page = bookmark;
        }

        $scope.getProducts();
    });
  
}]);
