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

angular.module("eappApp").controller('AdminController', function($scope, Form, $http, $q, $mdDialog, eapp, $rootScope, appService) {
      
    $scope.selectedProduct = null;
    $scope.searchProductText = "";
    $scope.queryProducts = [];
    $scope.compareUnits = [];
    
    $scope.equivalent = 1;
    $scope.selected_store = 1;
    
    $scope.store_product = 
    {
        organic : 0,
        in_flyer : 0,
        retailer_id : 1,
        country : 'Canada',
        state : 'Quebec',
        format : '1x1',
        quantity : '1',
        price : 1,
        unit_id : 1
    };
    
    
    $scope.Init = function()
    {
        $rootScope.menu = "admin_create_product";
        var spID = angular.getSearchParam("product");
        
        $scope.$watch('store_product.period_from', function(newVal, oldVal)
        {
            if(!angular.isNullOrUndefined(newVal))
            {
                $scope.period_from = new Date($scope.store_product.period_from.toString().replace("-", "/"));
            }
            else
            {
                $scope.period_from = new Date();
            }
            
        });
        
        $scope.$watch('store_product.period_to', function(newVal, oldVal)
        {
            if(!angular.isNullOrUndefined(newVal))
            {
                $scope.period_to = new Date($scope.store_product.period_to.toString().replace("-", "/"));
            }
            else
            {
                $scope.period_to = new Date();
            }
            
        });
        
        $scope.$watch('equivalent', function()
        {
            $scope.updateQuantity();
        });
        
        if(!angular.isNullOrUndefined(spID))
        {
            // Get the store product
            var storeProductPromise = eapp.getStoreProduct(spID);
            
            storeProductPromise.then(function(response)
            {
                $scope.store_product = response.data;
                $scope.store_product.price = parseFloat($scope.store_product.price);
                $scope.store_product.unit_price = parseFloat($scope.store_product.unit_price);
                $scope.store_product.regular_price = parseFloat($scope.store_product.regular_price);
                $scope.store_product.organic = parseInt($scope.store_product.organic) === 0 ? false : true;
                $scope.store_product.in_flyer = parseInt($scope.store_product.in_flyer) === 0 ? false : true;
                
                $scope.onProductSelected($scope.store_product.product);
            });
        }
        
        var brandsPromise = eapp.getBrands();
        brandsPromise.then(function(response)
        {
            var array = $.map(response.data, function(value, index) {
                return [value];
            });
            $scope.brands = array;
        });
        
        var retailersPromise = eapp.getRetailers();
        retailersPromise.then(function(response)
        {
            var array = $.map(response.data, function(value, index) {
                return [value];
            });
            $scope.retailers = array;
        });
        
        
        $scope.$watch('store_product.compareunit_id', function(newVal)
        {
            $scope.updateEquivalent(newVal);
        });
        
        $scope.$watch('store_product.unit_id', function(newVal)
        {
            $scope.updateEquivalent(newVal);
        });
    };
    
    $scope.updateEquivalent = function(newVal)
    {
        $scope.equivalent = 0;
        
        if(angular.isNullOrUndefined(newVal))
        {
            return;
        }

        if(angular.isNullOrUndefined($scope.unitCompareUnit))
        {
            var getUnitCompareUnitPromise = eapp.getUnitCompareUnit();
        
            getUnitCompareUnitPromise.then(function(response)
            {
                $scope.unitCompareUnit = response.data;
            });
            
            return;
        }
        
        for(var x in $scope.unitCompareUnit)
        {
            var unitCompareUnit = $scope.unitCompareUnit[x];

            if(parseInt(unitCompareUnit.unit_id) === parseInt($scope.store_product.unit_id) 
                    && parseInt(unitCompareUnit.compareunit_id) === parseInt($scope.store_product.compareunit_id))
            {
                $scope.equivalent = unitCompareUnit.equivalent;

                return;
            }

            if(parseInt(unitCompareUnit.unit_id) === parseInt($scope.store_product.compareunit_id) 
                    && parseInt(unitCompareUnit.compareunit_id) === parseInt($scope.store_product.unit_id))
            {
                $scope.equivalent = 1 / unitCompareUnit.equivalent;

                return;
            }

        }
        
        $scope.equivalent = 1;
    };
    
    $scope.querySearch = function(searchProductText)
    {
    	var q = $q.defer();
	var formData = new FormData();
	formData.append("name", searchProductText);

	$http.post(appService.siteUrl.concat("/admin/searchProducts"), formData, {
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
    
    $scope.continue = false;
    
    $scope.product = null;
    
    $scope.default_country = 'CA';
    
    $scope.getSaveLabel = function(){ return parseInt($scope.store_product.id) > -1 ? "Edit" : "Create";};
    
    $scope.onProductSelected = function(item)
    {
        if(typeof item === 'undefined')
        {
            return;
        }
        
        $scope.selectedProduct = item;
        
        var image_url = item.image;
        
        $scope.api.removeAll();
        
        $scope.api.addRemoteFile(image_url, item.image,'image'); 
        
        // Manage Unit
        if(parseInt(item.unit_id) > 0)
        {
            $scope.productHasUnit = true;
            
            // The product has a compare unit on it. Set the compare unit. 
            var getCompareUnitsPromise = eapp.getCompareUnits();
            getCompareUnitsPromise.then(function(response)
            {
                var array = $.map(response.data, function(value, index) {
                    return [value];
                });
                
                $scope.compareUnits = [];
                
                for(var x in array)
                {
                    if(parseInt(array[x].id) === parseInt(item.unit_id))
                    {
                        $scope.compareUnits.push(array[x]);
                    }
                }
                
                // Set selected
                $scope.store_product.compareunit_id = parseInt(item.unit_id);
                
                // Get the units that are supported by this compare unit
                var getCompareUnitUnitsPromise = eapp.getCompareUnitUnits(parseInt(item.unit_id));
                
                getCompareUnitUnitsPromise.then(function(response)
                {
                    $scope.units = $.map(response.data, function(value, index) {
                        return [value];
                    });
                    
                    if(parseInt($scope.store_product.unit_id) <= 0)
                    {
                        // Select the first
                        for(var x in $scope.units)
                        {
                            $scope.store_product.unit_id = $scope.units[x].id;
                            break;
                        }
                    }
                    else
                    {
                        $scope.store_product.unit_id = parseInt($scope.store_product.unit_id);
                    }
                    
                });
            });
        }
        else
        {
            $scope.productHasUnit = false;
            // The selected product has no compare unit on it. 
            // Load all compare units. 
            var getCompareUnitsPromise = eapp.getCompareUnits();
            
            getCompareUnitsPromise.then(function(response)
            {
                // Load compare units
                $scope.compareUnits = $.map(response.data, function(value, index) {
                    return [value];
                });
                
                if(parseInt($scope.store_product.compareunit_id) <= 0)
                {
                    // Select the first compare unit
                    for(var x in $scope.compareUnits)
                    {
                        $scope.store_product.compareunit_id = $scope.compareUnits[x].id;
                        break;
                    }
                }
                
                $scope.updateUnits($scope.store_product.compareunit_id);
                
            });
        }

    };
    
    $scope.brand_selected = function(brand)
    {
        if(typeof brand === 'undefined')
        {
            return;
        }
        
        $scope.api.removeAll();

        $scope.api.addRemoteFile(brand.image, brand.image,'image');
    };
    
    $scope.createNewBrand = function(ev)
    {
        $mdDialog.show({
            controller: DialogController,
            templateUrl:  appService.baseUrl + 'assets/templates/create-new-brand.html',
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
                
                $http.post(appService.siteUrl.concat("/admin/create_product_brand"), formData, {transformRequest: angular.identity,
                headers: {'Content-Type': undefined}}).then(
                function(result)
                {
                    if(result.data.success)
                    {
                        $scope.store_product.brand = result.data.newBrand;
                        $scope.brands[result.data.newBrand.id] = result.data.newBrand;
                        $mdDialog.cancel();
                    }
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
                
                $http.post(appService.siteUrl.concat("/admin/create_product"), formData, {transformRequest: angular.identity,
                headers: {'Content-Type': undefined}}).then(
                function(result)
                {
                    if(result.data.success)
                    {
                        $scope.selectedProduct = result.data.newProduct;
                        $scope.products[$scope.selectedProduct.id] = result.data.newProduct;
                        $mdDialog.cancel();
                    }		
                });
            }
                
        };
    };
	
    $scope.createNewProduct = function(ev)
    {
        $mdDialog.show({
            controller: CreateProductController,
            templateUrl:  appService.baseUrl + 'assets/templates/create-new-product.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose:true,
            preserveScope:true,
            scope : $scope,
            fullscreen: false //
          });
    };
    
    $scope.post_create_product = function()
    {
    	var redirect_url = null;

        if(!$scope.continue)
        {
            redirect_url =  appService.siteUrl.concat("/admin/store_products");
        }
        
        var url = appService.siteUrl.concat("/admin/create_store_product");
        var form = document.getElementById("create_store_product_form");
        var formData = new FormData(form);
        // Manually add organic and in flyer form fields
        formData.append("product[organic]", $scope.store_product.organic ? 1 : 0);
        formData.append("product[in_flyer]", $scope.store_product.in_flyer ? 1 : 0);
        formData.append("product[country]", $scope.store_product.country);
        formData.append("product[state]", $scope.store_product.state);
        formData.append("product[id]", $scope.store_product.id);
        if($scope.selectedProduct !== null && typeof $scope.selectedProduct !== 'undefined')
        {
	    formData.append("product[product_id]", angular.isNullOrUndefined($scope.selectedProduct.id) ? null : $scope.selectedProduct.id);
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
            sessionStorage.setItem("period_from", convert_to_string_date($scope.period_from));
            sessionStorage.setItem("period_to", convert_to_string_date($scope.period_to));
		
	    $scope.selectedProduct = null;
            $scope.store_product.brand = null;
	    $scope.api.removeAll();
	    var tmp = 
	    {
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
            
            $scope.create_store_product_form.$setPristine();
            $scope.create_store_product_form.$setValidity();
            $scope.create_store_product_form.$setUntouched();
	    
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
                $http.post(appService.siteUrl.concat("/admin/upload_product_image"), formData, {transformRequest: angular.identity,
                headers: {'Content-Type': undefined}}).then(
                function(result)
                {
                    if(result.data.success)
                    {
                        $scope.post_create_product();
                    }
                    else
                    {
                        $scope.post_create_product();
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
        var formatValue;
        
        var quantity = 1;
        
        if($scope.store_product.format === 'undefined' || $scope.store_product.format === null)
        {
            return quantity;
        }
        
        var format = $scope.store_product.format.toLowerCase().split("x");
        
        $scope.store_product.quantity = 1;
        
        if(format.length === 1)
        {
            formatValue = parseFloat(format[0]);
        }
        
        if(format.length === 2)
        {
            formatValue = parseFloat(format[0]) * parseFloat(format[1]);
        }
        
        if(parseInt($scope.store_product.unit_id) > 0 && parseInt($scope.store_product.compareunit_id) > 0)
        {
            $scope.store_product.quantity = formatValue * $scope.equivalent;
        }
        
        $scope.store_product.unit_price = $scope.store_product.price / $scope.store_product.quantity;
        
    };
    
    $scope.updateUnits = function(compareUnitID)
    {
        var comareUnitsPromise = eapp.getCompareUnitUnits(compareUnitID);
        
        comareUnitsPromise.then(function(response)
        {
            $scope.units = $.map(response.data, function(value, index) {
                return [value];
            });
            
            if($scope.store_product.unit_id <= 0)
            {
                for(var x in $scope.units)
                {
                    $scope.store_product.unit_id = $scope.units[x].id;
                    break;
                }
            }
            else
            {
                var value = $scope.store_product.unit_id;
                $scope.store_product.unit_id = 0;
                $scope.store_product.unit_id = value;
            }
        });
    };
    
    $scope.create_store = function()
    {
        var url =  appService.siteUrl.concat("/admin/create_store");
        var form = document.getElementById("create_store_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null);
    };
    
    
    $scope.upload = function(name, $event)
    {
        var url = appService.siteUrl.concat("/admin/upload_" + name);
        var form = document.getElementById("upload_" + name + "_form");
        var formData = new FormData(form);
        Form.postForm(formData, url, null, $event);
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
   
    $scope.Init();
    
});

angular.module('eappApp').directive('fileUpload', function () 
{
    return {
        templateUrl:'emplates/components/tuploadFileView.html',
        link: function (scope, element) {

            scope.fileName = 'Choose a file...';

            element.bind('change', function () {
                scope.$apply(function () {
                    scope.fileName = document.getElementById('uploadFileInput').files[0].name;
                });
            });

            scope.uploadFile = function(){
                var formData = new FormData();

                formData.append('file', document.getElementById('uploadFileInput').files[0]);

                // Add code to submit the formData  
            };
        }
    };
});

angular.module("eappApp").controller("ViewSubCategoriesController", function($scope, eapp, $mdDialog, $http, appService)
{
    var ctrl = this;
    
    $scope.selected = [];
    
    $scope.newSubCategory = { product_category_id : 0, name : '' };
	
    $scope.newCategory = { name : '', image : ''};
	
    $scope.selectedSubCategory = null;
      
    $scope.query = 
    {
        filter: '',
        limit: 20,
        page: 1
    };
    
    $scope.filter = 
    {
        options: 
        {
            debounce: 500
        }
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

        $scope.getSubCategories();
    });
    
    ctrl.Init = function()
    {
        $scope.getSubCategories();
    };
    
    $scope.getSubCategories = function()
    {
        var getAdminSubCategoriesPromise = eapp.getAdminSubCategories($scope.query);
        
        $scope.promise = getAdminSubCategoriesPromise;
        
        getAdminSubCategoriesPromise.then(function(response)
        {
            var subCategories = $.map(response.data.sub_categories, function(value, index) {
                return [value];
            });
			
            var categories = $.map(response.data.categories, function(value, index) {
                return [value];
            });
            
            $scope.subCategories = subCategories;
            
            $scope.categories = categories;
            
            $scope.count = response.data.count;
        });
    };
                
    $scope.delete = function(ev, id)
    {
        // Appending dialog to document.body to cover sidenav in docs app
        var confirm = $mdDialog.confirm()
              .title('Delete sub category')
              .textContent('Are you sure?')
              .ariaLabel('Lucky day')
              .targetEvent(ev)
              .ok('Yes')
              .cancel('No');

        $mdDialog.show(confirm).then(function() 
        {
            eapp.deleteSubCategory(id).then(function()
            {
                var index = $scope.subCategories.map(function(e){ return e.id; }).indexOf(id);
                
                if(index > -1)
                {
                    $scope.subCategories.splice(index, 1);
                }
            });
        },
        function()
        {
            
        });
    };
    
    $scope.edit = function(ev, subCategory)
    {        
        var jsonSubCategory = JSON.stringify(subCategory);
        
        var formData = new FormData();
        formData.append("sub_category", jsonSubCategory);

        $http.post(appService.siteUrl.concat("/admin/edit_sub_category"), formData, {transformRequest: angular.identity,
        headers: {'Content-Type': undefined}}).then(
        function(result)
        {
            if(result.data.success)
            {
                eapp.showAlert(ev, "Success", "Sub category was edited successfully.");
            }		
        });
    };
	
    $scope.CreateSubcategory = function(ev)
    {
        var subCategory = $scope.newSubCategory;
        
        var jsonSubCategory = JSON.stringify(subCategory);
        
        var formData = new FormData();
        
        formData.append("sub_category", jsonSubCategory);

        $http.post(appService.siteUrl.concat("/admin/edit_sub_category"), formData, {transformRequest: angular.identity,
        headers: {'Content-Type': undefined}}).then(
        function(result)
        {
            if(result.data.success)
            {
                var value = { id : result.data.id, product_category_id : subCategory.product_category_id, name : subCategory.name };
                
                $scope.subCategories.push(value);
                
                $scope.newSubCategory = { product_category_id : 0, name : '' };
                
                eapp.showAlert(ev, "Success", "Sub category was created successfully.");
            }		
        });
    };

    $scope.CreateCategory = function(ev)
    {        
        var jsonCategory = JSON.stringify($scope.newCategory);

        var formData = new FormData();
        formData.append("category", jsonCategory);

        angular.forEach($scope.categoryImage, function(obj){
            if(!obj.isRemote){
                formData.append('image', obj.lfFile);
            }
        });

        $http.post(appService.siteUrl.concat("/admin/create_category"), formData, {transformRequest: angular.identity,
        headers: {'Content-Type': undefined}}).then(
        function(result)
        {
            if(result.data.success)
            {
                $scope.categories.push(result.data.category);
                $scope.newCategory = { name : '', image : ''};
                $scope.api.removeAll();
                eapp.showAlert(ev, "Success", "Your category was created successfully.");
            }		
        });
    };
    
    angular.element(document).ready(function()
    {
        ctrl.Init();
    });
    
});

angular.module("eappApp").controller("ViewProductsController", function($scope, eapp, $mdDialog, $http, appService)
{
    var ctrl = this;
    
    $scope.selected = [];
    
    $scope.product = null;
      
    $scope.query = 
    {
        filter: '',
        limit: 20,
        page: 1
    };
    
    $scope.filter = 
    {
        options: 
        {
            debounce: 500
        }
    };
    
    $scope.gotoCreateNewProduct = function()
    {
        window.location.href = appService.siteUrl.concat("/admin/create_otiprix_product");
    };
    
    $scope.imageRemoved = function(data)
    {
        ctrl.updateProductImage(null, data, function(){});
    };
    
    $scope.imageChanged = function(file, data)
    {
        ctrl.updateProductImage(file, data, function(){});
    };
    
    ctrl.updateProductImage = function(image, data, success)
    {
        var formData = new FormData();

        formData.append("image", image);

        formData.append("id", data.id);

        return $http.post(
            appService.siteUrl.concat("/admin/change_product_image"), 
            formData, 
            { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success);
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


        if(newValue != oldValue)
        {
            $scope.getProducts();
        }
        
    });
    
    ctrl.Init = function()
    {
        $scope.getProducts();
    };
    
    $scope.getProducts = function()
    {
        var getProductsPromise = eapp.getProducts($scope.query);
        
        $scope.promise = getProductsPromise;
        
        getProductsPromise.then(function(response)
        {
            var array = $.map(response.data.products, function(value, index) {
                return [value];
            });
            
            $scope.products = array;
            
            $scope.products.map(x => 
            {
                x.popular = x.is_popular == 1;
            });
            
            $scope.subcategories = response.data.subcategories;
            
            $scope.units = response.data.units;
            
            $scope.count = response.data.count;
        });
    };
    
    
    $scope.getChips = function(tags)
    {
        return tags.split(",");
    };
    
    $scope.getUnits = function()
    {
        var getUnitsPromise = eapp.getUnits();
        
        getUnitsPromise.then(function(response)
        {
            $scope.units = response.data;
        });
    };
    
    $scope.edit_product = function(product_id)
    {
        window.location.href = appService.siteUrl.concat("/admin/edit_product?product=", product_id.toString());
    };
    
    
    $scope.deleteProduct = function(ev, product_id)
    {
        // Appending dialog to document.body to cover sidenav in docs app
        var confirm = $mdDialog.confirm()
              .title('Delete product')
              .textContent('Are you sure?')
              .ariaLabel('Lucky day')
              .targetEvent(ev)
              .ok('Yes')
              .cancel('No');

        $mdDialog.show(confirm).then(function() 
        {
            eapp.deleteProduct(product_id).then(function()
            {
                var index = $scope.products.map(function(e){ return e.id; }).indexOf(product_id);
                
                if(index > -1)
                {
                    $scope.products.splice(index, 1);
                }
            });
        });
    };
    
    $scope.directEdit = function(ev, theProduct)
    {
        theProduct.tags = theProduct.tags_array.join();
        theProduct.is_popular = theProduct.popular ? 1 : 0;
        
        var product = JSON.stringify(theProduct);
        
        var formData = new FormData();
        formData.append("product", product);

        angular.forEach($scope.product_image, function(obj){
            if(!obj.isRemote){
                formData.append('image', obj.lfFile);
            }
        });

        $http.post(appService.siteUrl.concat("/admin/edit_otiprix_product"), formData, {transformRequest: angular.identity,
        headers: {'Content-Type': undefined}}).then(
        function(result)
        {
            if(result.data.success)
            {
                eapp.showAlert(ev, "Success", "Your product was edited successfully.");
            }		
        });
        
    };
    
    angular.element(document).ready(function()
    {
        ctrl.Init();
    });
    
});

angular.module("eappApp").controller("EditProductController", function($scope, eapp, $http, appService)
{
    var ctrl = this;
    
    $scope.product = null;
      
    ctrl.Init = function()
    {
        var pID = angular.getSearchParam("product");
        
        $scope.getSubCategories();
        $scope.getUnits();
        
        if(!angular.isNullOrUndefined(pID))
        {
            // Get the store product
            var getProductPromise = eapp.getOtiprixProduct(pID);
            
            getProductPromise.then(function(response)
            {
                $scope.product = response.data;
                
                $scope.product.tagsArray = $scope.product.tags.split(",");
                
                var image_url = $scope.product.image;
        
                $scope.api.removeAll();

                $scope.api.addRemoteFile(image_url, $scope.product.image,'image'); 
            });
        }
        else
        {
            $scope.product = 
            {
                name : 'New Product',
                tagsArray : [],
                tags : '',
                unit_id : 0,
                subcategory_id : 0,
                image : ''
        
            };
        }
    };
    
    $scope.getSubCategories = function()
    {
        var getSubCategoriesPromise = eapp.getSubCategories();
        
        getSubCategoriesPromise.then(function(response)
        {
            $scope.subcategories = response.data;
        });
    };
    
    $scope.getUnits = function()
    {
        var getUnitsPromise = eapp.getCompareUnits();
        
        getUnitsPromise.then(function(response)
        {
            $scope.units = response.data;
        });
    };
    
    $scope.submit = function(ev)
    {
        $scope.product.tags = $scope.product.tagsArray.join();
        
        var product = JSON.stringify($scope.product);
        
        if($scope.editProduct.$valid)
        {
            var formData = new FormData();
            formData.append("product", product);

            angular.forEach($scope.product_image, function(obj){
                if(!obj.isRemote){
                    formData.append('image', obj.lfFile);
                }
            });

            $http.post(appService.siteUrl.concat("/admin/edit_otiprix_product"), formData, {transformRequest: angular.identity,
            headers: {'Content-Type': undefined}}).then(
            function(result)
            {
                if(result.data.success)
                {
                    eapp.showAlert(ev, "Success", "Your product was edited successfully.")
                }		
            });
        }
        
    };
        
    $scope.gotoViewProducts = function()
    {
        window.location.href = appService.siteUrl.concat("/admin/view_products");
    };
    
    angular.element(document).ready(function()
    {
        ctrl.Init();
    });
    
});


angular.module("eappApp").controller('ProductsTableController', function($scope, $q, $http, $rootScope, appService) 
{
    $scope.selected = [];
    
    $rootScope.menu = "admin_view_products";
  
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

        $scope.promise = $http.post( appService.siteUrl.concat("/admin/delete_store_product"), formData, {
                        transformRequest: angular.identity,
                        headers: {'Content-Type': undefined}});

        $scope.promise.then(function()
        {
            // Refresh list
            $scope.getProducts();
        });
    };
    
    $scope.prev_order = "period_from";
	
    $scope.getProducts = function () 
    {
        if(typeof appService.siteUrl === 'undefined')
        {
            // Not ready
            return;
        }
        
        if($scope.prev_order != $scope.query.order)
        {
            $scope.query.page = 1;
            $scope.prev_order = $scope.query.order;
        }
        
        var formData = new FormData();

        formData.append("page", $scope.query.page);
        formData.append("limit", $scope.query.limit);
        formData.append("filter", $scope.query.filter);
        formData.append("order", $scope.query.order);
        $scope.promise = $http.post( appService.siteUrl.concat("/admin/get_store_products"), formData, {
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
    
  
});
