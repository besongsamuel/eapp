/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("UserListController", ["$rootScope", "$scope", "$mdDialog", "$http", "$q", function($rootScope, $scope, $mdDialog, $http, $q) 
{
    
    $rootScope.isMainMenu = true;
    
    $rootScope.selectedProduct = null;
    
    /**
     * Query text for the product being searched. 
     */
    $rootScope.searchProductText = "";
    
    /**
     * The different product categories of the list
     */
    $rootScope.myCategories = [];
    
    /**
     * The maximum number of products the list can contain
     */
    $rootScope.maxNumItems = 50;
    
    /**
     * Counts the number of products in my list
     * @returns {Number}
     */
    $scope.my_list_count = function()
    {
        var count = 0;

        for(var index in $scope.myCategories)
        {
            count += $scope.myCategories[index].products.length;
        }

        return count;
    };
    
    /**
     * Counts the number of flyer products available from my list
     * @returns {Number}
     */
    $scope.flyer_products_count = function()
    {
        var count = 0;

        for(var index in $scope.myCategories)
        {
            for(var i in $scope.myCategories[index].products)
            {
                var product = $scope.myCategories[index].products[i];
                
                if(typeof product.store_products !== "undefined" && product.store_products !== null)
                {
                    count += product.store_products.length;
                }
                
            }
        }
        return count;
    };
    
    /**
     * Adds the selected product to my list of products
     * @returns {undefined}
     */
    $scope.addNewProductToList = function()
    {
        $scope.addToMyList($scope.selectedProduct);
    };

    $scope.addToMyList = function(product)
    {
        product.quantity = 1;

        $scope.AddProductToList(product);

        $scope.saveMyList();
    };
    
    $scope.AddProductToList = function(product)
    {
        if(typeof product !== "undefined" && product !== null &&  $scope.my_list_count() < $scope.maxNumItems)
        {
            product.quantity = (typeof product.quantity !== "undefined" && product.quantity) ? product.quantity : 1;
            // get product category id
            var category = product.category;
            
            if(angular.isNullOrUndefined(category))
            {
                category = 
                {
                    id : 0,
                    name : 'Aucune catégorie'
                };
                
                product.category = category;
            }
            
            // Check if category exists
            var index = $scope.myCategories.map(function(e) { return e.id; }).indexOf(category.id);

            if(index !== -1)
            {
                // Check if product exists in categories
                var product_index = $scope.myCategories[index].products.map(function(e) { return e.id; }).indexOf(product.id);
                if(product_index !== -1)
                {
                    // Update Quantity
                    var quantity = parseInt($scope.myCategories[index].products[product_index].quantity) + 1;
                    $scope.myCategories[index].products[product_index].quantity = quantity;
                }
                else
                {
                    if($scope.myCategories[index].products === null || typeof $scope.myCategories[index].products === 'undefined')
                    {
                        $scope.myCategories[index].products = [];
                    }

                    $scope.myCategories[index].products.push(product);
                }
            }
            else
            {
                // create category
                category.products = [];
                category.products.push(product);
                $scope.myCategories.push(category);
            }
        }
    };
    
    $scope.removeFromMyList = function(product)
    {
        $scope.removeProductFromList(product.id, null, false);
    };
    
    $scope.getUserProductList = function()
    {
        if($scope.loggedUser !== null && typeof $scope.loggedUser !== 'undefined')
        {
            for(var i in $scope.loggedUser.grocery_list)
            {
                $scope.AddProductToList($scope.loggedUser.grocery_list[i]);
            }
        }
    };
    
    $scope.removeProductFromList = function(product_id, $event, showDialog)
    {
        var confirmDialog = $rootScope.createConfirmDIalog ($event, "Ce produit sera supprimé de votre liste.");
        
        if(showDialog)
        {
            $mdDialog.show(confirmDialog).then(function() 
            {
                $scope.removeFromList(product_id);
                $scope.saveMyList();
                
            }, function() 
            {
                
            });
        }
        else
        {
            $scope.removeFromList(product_id);
            $scope.saveMyList();
        }
    };
    
    $scope.removeFromList = function(product_id)
    {
        for(var index in $scope.myCategories)
        {
            var pos = $scope.myCategories[index].products.map(function(e) { return e.id; }).indexOf(product_id);
            if(pos > -1)
            {
                $scope.myCategories[index].products.splice(pos, 1);

                if($scope.myCategories[index].products.length === 0)
                {
                    $scope.myCategories.splice(index, 1);
                }

                break;
            }
        }
    };
  
    $rootScope.saveMyList = function()
    {
        var formData = new FormData();
        formData.append("my_list", JSON.stringify($scope.getProductList()));
        // Send request to server to get optimized list 	
        $http.post( $scope.site_url.concat("/account/save_user_list"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            if(!response.data.success)
            {
                $scope.showSimpleToast("une erreur inattendue est apparue. Veuillez réessayer plus tard.", "mainmenu-area");
            }

            $scope.registering_user = false;
        });
    };
    
    $scope.clearMyList = function($event)
    {
        var confirmDialog = $scope.createConfirmDIalog($event, "Cela effacera tous les contenus de votre liste d'épicerie.");
		
        $mdDialog.show(confirmDialog).then(function() 
        {
            $http.post($rootScope.site_url.concat("/cart/destroy"), null).then(function(response)
            {
                $rootScope.myCategories = [];
                $scope.loggedUser.grocery_list = [];
                $rootScope.saveMyList();
            });

        });
    };
    
    $scope.getProductList = function()
    {
        var result = [];
        
        for(var index in $scope.myCategories)
        {
            for(var i in $scope.myCategories[index].products)
            {
                var data = 
                {
                    id : $scope.myCategories[index].products[i].id,
                    quantity : $scope.myCategories[index].products[i].quantity
                };    
                result.push(data);
            }
        }
        
        return result;
    };
    
    /**
     * This method searches for products in the database 
     * based on the search text
     * @param {type} searchProductText
     * @returns {.$q@call;defer.promise}
     */
    $scope.querySearch = function(searchProductText)
    {
    	var q = $q.defer();
        var formData = new FormData();
        formData.append("name", searchProductText);

        $http.post( $scope.site_url.concat("/admin/searchProducts"), formData, {
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
     * sets the current search product found. 
     * @param {type} item
     * @returns {undefined}
     */
    $scope.product_selected = function(item)
    {
        if(typeof item === 'undefined')
            return;
            
        $scope.selectedProduct = item;
    };
    
    $scope.getUserListStorePrices = function()
    {
        var stores = [];

        if($scope.loggedUser !== null && typeof $scope.loggedUser !== "undefined" && typeof $scope.loggedUser.grocery_list !== "undefined")
        {
            for(var i in $scope.loggedUser.grocery_list)
            {
                var product = $scope.loggedUser.grocery_list[i];
                
                for (var x in product.store)
                {
                    var productStore = product.store[x];
                    
                    var index = stores.map(function(e) { return e.id; }).indexOf(productStore.id);

                    if(index === -1) 
                    {
                        productStore.price = 0;
                        productStore.count = 0;
                        stores.push(productStore);
                        index = stores.length - 1;
                    }

                    if(typeof productStore.store_product !== "undefined")
                    {
                        stores[index].price += parseFloat(productStore.store_product.price);
                        stores[index].count++;
                    }
                }
                
            }
        }
	    
        // remove all stores with no items 
        var index = stores.map(function(e) { return e.count; }).indexOf(0);

        while(index > -1)
        {
            stores.splice(index, 1);
            var index = stores.map(function(e) { return e.count; }).indexOf(0);
        }    

        return stores;
    };
    
    $scope.optimizeMyList = function($event)
    {
        var confirmDialog = $rootScope.createConfirmDIalog($event, "Cela effacera tous les contenus de votre panier.");
        
        $mdDialog.show(confirmDialog).then(function() 
        {
            $http.post($rootScope.site_url.concat("/cart/destroy"), null).then(function(response)
            {
                $rootScope.cart = [];
                var items = [];
                
                // add cart contents from my list
                for(var index in $rootScope.myCategories)
                {
                    for(var i in $rootScope.myCategories[index].products)
                    {
                        var product = $rootScope.myCategories[index].products[i];
                        
                        var item = 
                        {
                            product_id : product.id,
                            quantity : product.quantity
                        };
                        
                        items.push(item);
                    }
                }
                
                var formData = new FormData();
                formData.append("items", JSON.stringify(items));

                $http.post($rootScope.site_url.concat("/cart/insert_batch"), 
                formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(function(response)
                {
                    window.location = $rootScope.site_url.concat("/cart");
                });
            });

        });
        
        
    };
    
    angular.element(document).ready(function()
    {
        $scope.load_icons(); 
           
        $scope.getUserProductList();
    });
    
}]);

