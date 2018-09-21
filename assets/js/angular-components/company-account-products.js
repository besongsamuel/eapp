
angular.module('eappApp').component('companyProducts', 
{
    templateUrl : "templates/components/company-products-view.html",
    controller : Controller,
    binding : 
    {
        companyId : '<'
    }
});

function Controller($scope, $rootScope, $mdDialog, $company)
{
    'use strict';
    
    var ctrl = this;
        
    ctrl.$onInit = function()
    {
        $scope.companyID = ctrl.companyID;
    };
  
    var bookmark;

    $scope.selected = [];
    
    $scope.storeProduct = {};

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
        limit: '10',
        order: 'nameToLower',
        page: 1
    };

    function success(storeProducts) 
    {
        $scope.storeProducts = storeProducts.data;
    }

    $scope.addNewStoreProduct = function (event) 
    {
        var addNewSPPromise = $mdDialog.show({
          clickOutsideToClose: true,
          controller: AddStoreProductController,
          parent: angular.element(document.body),
          controllerAs: 'ctrl',
          focusOnOpen: false,
          targetEvent: event,
          locals : 
            {
                storeProduct : null
            },
          templateUrl: 'templates/dialogs/add-store-company-product-dialog.html'
        });
        
        addNewSPPromise.then(function()
        {
            $scope.getStoreProducts();
        });
    };
    
    $scope.editStoreProduct = function (event, storeProduct) 
    {
        var editPromise = $mdDialog.show({
          clickOutsideToClose: true,
          controller: AddStoreProductController,
          parent: angular.element(document.body),
          controllerAs: 'ctrl',
          focusOnOpen: false,
          targetEvent: event,
          locals : 
            {
                storeProduct : storeProduct
            },
          templateUrl: 'templates/dialogs/add-store-company-product-dialog.html'
        });
        
        editPromise.then(function()
        {
            $scope.getStoreProducts();
        });
    };

    $scope.delete = function (event) 
    {
        var confirmDialog = $rootScope.createConfirmDIalog (event, "Êtes-vous sûr de vouloir supprimer ces produits?");
        
        $mdDialog.show(confirmDialog).then(function() 
        {
            $company.batchDeleteStoreProducts($scope.selected, $scope.getStoreProducts);
            
            $scope.selected = [];

        }, 
        function() 
        {

        });
    };

    $scope.getStoreProducts = function () 
    {
        $scope.promise = $company.getStoreProducts($scope.query, success).$promise;
    };

    $scope.removeFilter = function () {
      $scope.filter.show = false;
      $scope.query.filter = '';

      if($scope.filter.form.$dirty) {
        $scope.filter.form.$setPristine();
      }
    };
    
    $scope.$watch('query.filter', function (newValue, oldValue) {
      if(!oldValue) {
        bookmark = $scope.query.page;
      }

      if(newValue !== oldValue) {
        $scope.query.page = 1;
      }

      if(!newValue) {
        $scope.query.page = bookmark;
      }

      $scope.getStoreProducts();
    });
    
}

function getDate(mySQLDate)
{
    var dateParts = mySQLDate.toString().split("-");
    var jsDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2].substr(0,2));
    
    return jsDate;
}

function AddStoreProductController($scope, $q, $timeout, eapp, $company, $mdDialog, storeProduct)
{
    var ctrl = this;
    
    $scope.period_from = new Date();
    $scope.period_to = new Date();
    $scope.trueValue = true;
    $scope.zero = 0;
    $scope.one = 1;
        
    $scope.storeProduct = 
    {
        
    };
    
    $scope.hide = function()
    {
        $mdDialog.hide();
    };
    
    $scope.edit = false;
    
    $scope.buttonCaption = "Ajouter";
    
    if(storeProduct)
    {
        $scope.buttonCaption = "Modifier";
        
        $scope.storeProduct = storeProduct;
        
        $scope.period_from = getDate(storeProduct.period_from);
        $scope.period_to = getDate(storeProduct.period_to);
        
        $scope.storeProduct.price = parseFloat($scope.storeProduct.price);
        $scope.organic = parseInt($scope.storeProduct.organic) === 1;
        $scope.in_flyer = parseInt($scope.storeProduct.in_flyer) === 1;

        $scope.edit = true;
    }

    eapp.getBrands().then(function(response)
    {
        $scope.brands = $.map(response.data, function(value, index) {
            return [value];
        }).map(function(data)
        {
            var data =  
            {
                value : data.id,
                display : data.name
            };

            return data;

        });

        ctrl.brandSearchText = null;
        
        if($scope.storeProduct.brand_id)
        {
            var brand_index = $scope.brands.map(function(e){ return e.value; }).indexOf(storeProduct.brand_id);
        
            if(brand_index > -1)
            {
                $scope.brand = $scope.brands[brand_index];
                ctrl.brandSearchText = $scope.brand.display;
            }
        }

        

        $scope.queryBrands = queryBrands;
    });
    
    eapp.getUnits().then(function(response)
    {
        $scope.units = $.map(response.data, function(value, index) {
            return [value];
        }).map(function(data)
        {
            var data =  
            {
                value : data.id,
                display : data.name
            };

            return data;

        });
        
        ctrl.unitSearchText = null;
        
        if($scope.storeProduct.unit_id)
        {
            var unit_index = $scope.units.map(function(e){ return e.value; }).indexOf(storeProduct.unit_id);
        
            if(unit_index > -1)
            {
                $scope.unit = $scope.units[unit_index];
                
                ctrl.unitSearchText = $scope.unit.display;
            }
        }

        

        $scope.queryUnits = queryUnits;
    });

    function queryBrands(query)
    {
        var results = query ? $scope.brands.filter( createFilter(query) ) : $scope.brands;

        var deferred = $q.defer();

        $timeout(function () 
        { 
            deferred.resolve( results ); 
        }, 
        Math.random() * 500, 
        false);

        return deferred.promise;
    };
    
    function queryUnits(query)
    {
        var results = query ? $scope.units.filter( createFilter(query) ) : $scope.units;

        var deferred = $q.defer();

        $timeout(function () 
        { 
            deferred.resolve( results ); 
        }, 
        Math.random() * 500, 
        false);

        return deferred.promise;
    };

    function createFilter(query) 
    {
        var lowercaseQuery = angular.lowercase(query);

        return function filterFn(brand) 
        {
            return (brand.display.indexOf(lowercaseQuery) === 0);
        };

    }
    
    $scope.imageChanged = function(newImage)
    {
        $scope.selectedImage = newImage;
    };
    
    $scope.addStoreProduct = function()
    {
        if($scope.addStoreProductForm.$valid)
        {
            if($scope.brand !== null)
            {
                $scope.storeProduct.brand_id = $scope.brand.value;
            }
            else
            {
                $scope.storeProduct.brand_id = -1;
            }
            
            if($scope.unit !== null)
            {
                $scope.storeProduct.unit_id = $scope.unit.value;
            }
            else
            {
                $scope.storeProduct.unit_id = -1;
            }
            
            var image = $scope.selectedImage;
                        
            $scope.storeProduct.period_from = $scope.period_from.toISOString().substring(0, 19).replace('T', ' ');
            $scope.storeProduct.period_to = $scope.period_to.toISOString().substring(0, 19).replace('T', ' ');
            $scope.storeProduct.in_flyer = $scope.in_flyer ? 1 : 0;
            $scope.storeProduct.organic = $scope.organic ? 1 : 0;
            
            $company.addStoreProduct($scope.storeProduct, image, addStoreProductSuccess);
            
        }
        
        function addStoreProductSuccess()
        {
            $mdDialog.hide($scope.storeProduct);
        }
    };
    
    $scope.onFileRemoved = function()
    {
        eapp.deleteStoreProductImage($scope.storeProduct.id);
        $scope.storeProduct.image = '';
    };
    
    ctrl.addProductBrand = function(brandName)
    {
        $company.addProductBrand(brandName, ProductBrandAdded);
    };
    
    function ProductBrandAdded(response)
    {
        var newBrand = 
        {
            display : response.data.name,
            value : response.data.id
        };
        
        $scope.brands.push(newBrand);
        $scope.brand = newBrand;
        
    }
    
    ctrl.addUnit = function(unitName)
    {
        $company.addUnit(unitName, UnitAdded);
    };
    
    function UnitAdded(response)
    {
        var newUnit = 
        {
            display : response.data.name,
            value : response.data.id
        };
        
        $scope.brands.push(newUnit);
        $scope.unit = newUnit;
        
    }
    
}



