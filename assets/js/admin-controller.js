/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").directive('apsUploadFile', apsUploadFile);

function apsUploadFile() {
  var directive = {
    restrict: 'E',
    template: '<input id="fileInput" type="file" style="" class="ng-hide"> <md-button id="uploadButton" class="md-raised md-primary" aria-label="attach_file">    Choose file </md-button><md-input-container  md-no-float>    <input id="textInput" ng-model="fileName" type="text" placeholder="No file chosen" ng-readonly="true"></md-input-container>',
    link: apsUploadFileLink
  };
  return directive;
}

function apsUploadFileLink(scope, element, attrs) 
{
    var input = $(element[0].querySelector('#fileInput'));
    var button = $(element[0].querySelector('#uploadButton'));
    var textInput = $(element[0].querySelector('#textInput'));

    if (input.length && button.length && textInput.length) 
    {
      button.click(function(e) 
      {
          input.click();
      });
      textInput.click(function(e) 
      {
          input.click();
      });
    }
    
    input.on('change', function(e) 
    {
        var files = e.target.files;
        if (files[0]) 
        {
            scope.fileName = files[0].name;
        } 
        else 
        {
            scope.fileName = null;
        }
        scope.$apply();
    });
}

angular.module("eappApp").controller('AdminController', ["$scope", "Form", "$http", "notifications", "$q", "$mdDialog", function($scope, Form, $http, notifications, $q, $mdDialog) {
      
    $scope.selectedProduct = null;
    $scope.searchProductText = "";
    $scope.queryProducts = [];
	
    $scope.querySearch = function(searchProductText)
    {
    	var q = $q.defer();
	var formData = new FormData();
	formData.append("name", searchProductText);

	$http.post($scope.site_url.concat("/admin/searchProducts"), formData, {
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
    
    $scope.getSaveLabel = function(){ return parseInt($scope.store_product.id) > -1 ? "Edit" : "Create";};
    
    $scope.product_selected = function(item)
    {
        if(typeof item === 'undefined')
        {
            return;
        }
        
        var image_url = item.image;
        
        $scope.api.removeAll();
        
        $scope.api.addRemoteFile(image_url, item.image,'image'); 

    };
    
    $scope.brand_selected = function(brand)
    {
        if(typeof brand === 'undefined')
        {
            return;
        }
        
        if(brand.image !== null && brand.image !== '' && typeof brand.image !== 'undefined' && brand.image !== 'no_image_available.png')
        {
            var image_url = $scope.base_url.concat("/assets/img/products/") + brand.image;
        
            $scope.api.removeAll();

            $scope.api.addRemoteFile(image_url, brand.image,'image'); 
        }
    };
    
    $scope.createNewBrand = function(ev)
    {
        $mdDialog.show({
            controller: DialogController,
            templateUrl:  $scope.base_url + 'assets/templates/create-new-brand.html',
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
    
    function DialogController($scope, $mdDialog, $http) 
    {
        $scope.hide = function() 
        {
            $mdDialog.hide();
        };

        $scope.cancel = function() 
        {
            $mdDialog.cancel();
        };

        $scope.submit = function() 
        {
            if($scope.createBrand.$valid)
            {
                var formData = new FormData();
                formData.append("name", $scope.searchText);
                formData.append("product_id", $scope.selectedProduct !== null ? $scope.selectedProduct.id : -1);
                angular.forEach($scope.brand_image_files, function(obj){
                    if(!obj.isRemote){
                        formData.append('image', obj.lfFile);
                    }
                });
                
                $http.post($scope.site_url.concat("/admin/create_product_brand"), formData, {transformRequest: angular.identity,
                headers: {'Content-Type': undefined}}).then(
                function(result)
                {
                    if(result.data.success)
                    {
                        $scope.store_product.brand = result.data.newBrand;
                        $scope.brands[result.data.newBrand.id] = result.data.newBrand;
                        notifications.showSuccess(result.data.message);
                        $mdDialog.cancel();
                    }
                    else
                    {
                    }		
                },
                function(err)
                {
                });
            }
                
            };
    };
    
    function CreateProductController($scope, $mdDialog, $http) 
    {
        $scope.hide = function() 
        {
            $mdDialog.hide();
        };

        $scope.cancel = function() 
        {
            $mdDialog.cancel();
        };

        $scope.submit = function() 
        {
            if($scope.createProduct.$valid)
            {
                var formData = new FormData();
                formData.append("name", $scope.searchProductText);
                formData.append("subcategory_id", $scope.subCategory);
                formData.append("unit_id", $scope.productUnit);
                
                angular.forEach($scope.product_image_files, function(obj){
                    if(!obj.isRemote){
                        formData.append('image', obj.lfFile);
                    }
                });
                
                $http.post($scope.site_url.concat("/admin/create_product"), formData, {transformRequest: angular.identity,
                headers: {'Content-Type': undefined}}).then(
                function(result)
                {
                    if(result.data.success)
                    {
                        $scope.selectedProduct = result.data.newProduct;
                        $scope.products[$scope.selectedProduct.id] = result.data.newProduct;
                        notifications.showSuccess(result.data.message);
                        $mdDialog.cancel();
                    }
                    else
                    {
                    }		
                },
                function(err)
                {
                });
            }
                
            };
    };
	
    $scope.createNewProduct = function(ev)
    {
        $mdDialog.show({
            controller: CreateProductController,
            templateUrl:  $scope.base_url + 'assets/templates/create-new-product.html',
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
    
    $scope.post_create_product = function()
    {
    	var redirect_url = null;
        
        if($scope.continue)
        {
            redirect_url = $scope.site_url.concat("/admin/create_store_product");
            redirect_url = redirect_url.concat("#admin-container");
        }
        else
        {
            redirect_url =  $scope.site_url.concat("/admin/store_products");
        }
        
        var url = $scope.site_url.concat("/admin/create_store_product");
        var form = document.getElementById("create_store_product_form");
        var formData = new FormData(form);
        // Manually add organic and in flyer form fields
        formData.append("product[organic]", $scope.store_product.organic ? 1 : 0);
        formData.append("product[in_flyer]", $scope.store_product.in_flyer ? 1 : 0);
        formData.append("product[country]", $scope.store_product.country);
        formData.append("product[state]", $scope.store_product.state);
        if($scope.selectedProduct !== null && typeof $scope.selectedProduct !== 'undefined')
        {
	    formData.append("product[product_id]", $scope.selectedProduct.id);
        }
        if($scope.store_product.brand !== null && typeof $scope.store_product.brand !== 'undefined')
        {
	    formData.append("product[brand_id]", $scope.store_product.brand.id);
        }
        else
        {
            formData.append("product[brand_id]", -1);
        }
	    
	if($scope.continue)
        {
            sessionStorage.setItem("retailer_id", $scope.store_product.retailer_id);
            sessionStorage.setItem("period_from", convert_to_string_date($scope.store_product.period_from));
            sessionStorage.setItem("period_to", convert_to_string_date($scope.store_product.period_to));
		
	    $scope.selectedProduct = null;
            $scope.store_product.brand = null;
	    $scope.api.removeAll();
	    var tmp = 
	    {
	    	id : -1,
		organic : 0,
		in_flyer : 0,
		format : "1x1",
                country : 'Canada',
                state : 'Quebec',
		retailer_id : $scope.store_product.retailer_id,
		period_from : $scope.store_product.period_from,
		period_to : $scope.store_product.period_to
	    };
	    $scope.store_product = tmp;
	    
        }
	    
        Form.postForm(formData, url, redirect_url);
    };
	
    $scope.create_store_product = function()
    {
        //upload the image here
        var formData = new FormData();
        angular.forEach($scope.product_image,function(obj){
            if(!obj.isRemote){
                formData.append('product_image', obj.lfFile);
            }
        });
        
        if($scope.product_image.length > 0)
        {
            if($scope.selectedProduct !== null)
            {
                formData.append("product_id", $scope.selectedProduct.id);
				$http.post($scope.site_url.concat("/admin/upload_product_image"), formData, {transformRequest: angular.identity,
                headers: {'Content-Type': undefined}}).then(
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
                },
                function(err)
                {
                    // do sometingh
                    $scope.post_create_product();
                });
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
    
    $scope.updateCompageUnit = function(unit_id)
    {
        var unit = $scope.units[unit_id];
        
        if(unit !== null)
        {
            $scope.store_product.compareunit_id = $scope.compareunits[unit.compareunit_id].id;
        }
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
        var url =  $scope.site_url.concat("/admin/create_store");
        var form = document.getElementById("create_store_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);
    };
    
    $scope.upload_products = function()
    {
        var url = $scope.site_url.concat("/admin/upload_products");
        var form = document.getElementById("upload_products_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);
    };
    
    $scope.upload_chains = function()
    {
        var url = $scope.site_url.concat("/admin/upload_chains");
        var form = document.getElementById("upload_chains_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);
    };
    
    $scope.upload_stores = function()
    {
        var url = $scope.site_url.concat("/admin/upload_stores");
        var form = document.getElementById("upload_stores_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);
    };
    
    $scope.upload_categories = function()
    {
        var url = $scope.site_url.concat("/admin/upload_categories");
        var form = document.getElementById("upload_categories_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);  
    };
    
    $scope.upload_subcategories = function()
    {
        var url = $scope.site_url.concat("/admin/upload_subcategories");
        var form = document.getElementById("upload_categories_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);  
    };
    
    $scope.upload_units = function()
    {
        var url = $scope.site_url.concat("/admin/upload_units");
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
            return (angular.lowercase(brand.name).indexOf(lowercaseQuery) === 0 
                && (($scope.selectedProduct !== null && parseInt($scope.selectedProduct.id) === parseInt(brand.product_id)) || parseInt(brand.product_id) === -1));
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
        order: 'period_from',
        page: 1
    };
    
    $scope.delete_store_product = function(store_product_id)
    {
        var formData = new FormData();

        formData.append("id", store_product_id);

        $scope.promise = $http.post( $scope.site_url.concat("/admin/delete_store_product"), formData, {
                        transformRequest: angular.identity,
                        headers: {'Content-Type': undefined}});

        $scope.promise.then(function(payload)
        {
                // Refresh list
                $scope.getProducts();
        });
    };
    
    $scope.prev_order = "period_from";
	
    $scope.getProducts = function () 
    {
        if(typeof $scope.site_url === 'undefined')
        {
            // Not ready
            return;
        }
        
        if($scope.prev_order != $scope.query.order)
        {
            $scope.query.page = 1;
            $scope.prev_order = $scope.query.order
        }
        
        var formData = new FormData();

        formData.append("page", $scope.query.page);
        formData.append("limit", $scope.query.limit);
        formData.append("filter", $scope.query.filter);
        formData.append("order", $scope.query.order);
        $scope.promise = $http.post( $scope.site_url.concat("/admin/get_store_products"), formData, {
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
