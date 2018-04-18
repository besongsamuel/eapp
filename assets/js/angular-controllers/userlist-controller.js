/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

const STAT_TYPE_ADDED_TO_LIST = 2;


angular.module("eappApp").controller("UserListController", ["$rootScope", "$scope", "$mdDialog", "$http", "$q", "eapp", function($rootScope, $scope, $mdDialog, $http, $q, eapp) 
{
    
    var ctrl = this;
    
    $rootScope.isMainMenu = true;
    
    $rootScope.selectedProduct = null;
    
    $scope.selectedGroceryList = null;
    
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
    
    $scope.selectedList = { id : -1};
    
    $scope.selectedGroceryList = {};
    
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
    $scope.addNewProductToList = function(ev)
    {
        // No grocery list is selected
        if(parseInt($scope.selectedList.id) === -1 && $scope.loggedUser.grocery_lists.length === 0)
        {
            // Prompt user to create a list
            
            var confirm = $mdDialog.prompt()
                .title("Créer une nouvelle liste d'épicerie")
                .textContent("Entrez le nom de la nouvelle liste d'épicerie")
                .placeholder('Nom')
                .ariaLabel('Nom')
                .initialValue('Nouvelle liste')
                .targetEvent(ev)
                .ok('Ok')
                .cancel('Annuler');

          $mdDialog.show(confirm).then(function(result) 
          {
                var createNewListPromise = eapp.createNewList(result);
                
                createNewListPromise.then(function(response)
                {
                    if(response.data.success)
                    {
                        var newList = response.data.data;
                        $rootScope.loggedUser.grocery_lists.push(newList);
                        $scope.selectedList.id = newList.id;
                        eapp.showAlert(ev, 'Succès', response.data.message);
                        $scope.addToMyList($scope.selectedProduct);
                    }
                    else
                    {
                        eapp.showAlert(ev, 'Erreur', response.data.message);
                    }
                });
                
          });
        }
        else
        {
            $scope.addToMyList($scope.selectedProduct);
        }
        
    };

    $scope.addToMyList = function(product)
    {
        product.quantity = 1;

        $scope.AddProductToList(product);
        
        eapp.recordProductStat(product.id, STAT_TYPE_ADDED_TO_LIST, true);

        $scope.saveMyList();
    };
    
    $scope.AddProductToList = function(product)
    {
        if(!angular.isNullOrUndefined(product) &&  $scope.my_list_count() < $scope.maxNumItems)
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
        $scope.myCategories = [];
        
        if($scope.loggedUser !== null && typeof $scope.loggedUser !== 'undefined' && !angular.isNullOrUndefined($scope.selectedGroceryList))
        {
            for(var i in $scope.selectedGroceryList.products)
            {
                $scope.AddProductToList($scope.selectedGroceryList.products[i]);
            }
        }
    };
    
    $scope.getUserProductListCount = function()
    {
        var count = 0;
        
        if($scope.loggedUser !== null && typeof $scope.loggedUser !== 'undefined')
        {
            for(var i in $scope.selectedGroceryList.products)
            {
                count++;
            }
        }
        
        return count;
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
    
    $scope.deleteList = function(ev)
    {
        var confirmDialog = $rootScope.createConfirmDIalog (ev, "Êtes-vous sûr de vouloir supprimer votre liste d'épicerie?");
        
        $mdDialog.show(confirmDialog).then(function() 
        {
            eapp.deleteGroceryList($scope.selectedList.id).then(function(response)
            {
                if($scope.isUserLogged)
                {
                    $scope.selectedList.id = -1;
                    $scope.selectedGroceryList = null;
                    $scope.loggedUser.grocery_lists = response.data.grocery_lists;
                    
                    if($scope.loggedUser.grocery_lists.length > 0)
                    {
                        $scope.selectedList.id = $scope.loggedUser.grocery_lists[0].id;
                        $scope.selectedGroceryList = $scope.loggedUser.grocery_lists[0];
                    }
                }

                $scope.getUserProductList();
            });

        }, function() 
        {

        });
        
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
        formData.append("id", $scope.selectedList.id);
        // Send request to server to get optimized list 	
        $http.post( $scope.site_url.concat("/account/save_user_list"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            if(!response.data.success)
            {
                $scope.showSimpleToast("une erreur inattendue est apparue. Veuillez réessayer plus tard.", "mainmenu-area");
            }
            else
            {
                // update grocery lists
                $rootScope.loggedUser.grocery_lists = response.data.grocery_lists;
                // Refresh
                $scope.groceryListChanged();
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
        
    $scope.groceryListChanged = function()
    {        
        var index = $scope.loggedUser.grocery_lists.map(function(e) { return parseInt(e.id); }).indexOf(parseInt($scope.selectedList.id));
        
        if(index > -1)
        {
            $scope.selectedGroceryList = $scope.loggedUser.grocery_lists[index];
            $scope.getUserProductList();
        }
    };
    
    ctrl.getCheapestStoreProduct = function(storeProducts)
    {
        var cheapestPrice = 0;
        
        for(var i in storeProducts)
        {
            if(cheapestPrice === 0)
            {
                cheapestPrice = parseFloat(storeProducts[i].price);
            }
            else
            {
                if(parseFloat(storeProducts[i].price) < cheapestPrice)
                {
                    cheapestPrice = parseFloat(storeProducts[i].price);
                }
            }
        }
        
        return cheapestPrice;
    };
    
    $scope.getStorePrice = function(store)
    {
        var price = 0;
        
        if($scope.isUserLogged && !angular.isNullOrUndefined($scope.loggedUser.user_stores))
        {
            for(var i in store.store_products)
            {
                price += ctrl.getCheapestStoreProduct(store.store_products[i]);
            }
        }
        
        return price;
        
    };
    
    $scope.getStoreCount = function(store)
    {
        var count = 0;
        
        if($scope.isUserLogged && !angular.isNullOrUndefined($scope.loggedUser.user_stores))
        {
            for(var i in store.store_products)
            {
                count++;
            }
        }
        
        return count;
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
                for(var index in $scope.myCategories)
                {
                    for(var i in $scope.myCategories[index].products)
                    {
                        var product = $scope.myCategories[index].products[i];
                        
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
    
    
    $scope.renameList = function(ev)
    {
        var confirm = $mdDialog.prompt()
            .title("renommer votre liste d'épicerie")
            .textContent("Renommer votre liste d'épicerie")
            .placeholder('Nom')
            .ariaLabel('Nom')
            .initialValue($scope.selectedGroceryList.name)
            .targetEvent(ev)
            .ok('Ok')
            .cancel('Annuler');

        $mdDialog.show(confirm).then(function(result) 
        {
            var formData = new FormData();
            formData.append("my_list", JSON.stringify($scope.getProductList()));
            formData.append("id", $scope.selectedGroceryList.id);
            formData.append("name", result);
            // Send request to server to get optimized list 	
            $http.post( $scope.site_url.concat("/account/save_user_list"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                if(response.data.success)
                {
                    var index = $scope.loggedUser.grocery_lists.map(function(e) { return parseInt(e.id); }).indexOf(parseInt($scope.selectedList.id));
                    
                    if(index > -1)
                    {
                        $scope.loggedUser.grocery_lists[index].name = result;
                    }
                }
            });

        });
        
    };
    
    $scope.createList = function(ev)
    {
        var confirm = $mdDialog.prompt()
            .title("Créer une nouvelle liste d'épicerie")
            .textContent("Entrez le nom de la nouvelle liste d'épicerie")
            .placeholder('Nom')
            .ariaLabel('Nom')
            .initialValue('Nouvelle liste')
            .targetEvent(ev)
            .ok('Ok')
            .cancel('Annuler');

          $mdDialog.show(confirm).then(function(result) 
          {
                var createNewListPromise = eapp.createNewList(result);
                
                createNewListPromise.then(function(response)
                {
                    if(response.data.success)
                    {
                        var newList = response.data.data;
                        $rootScope.loggedUser.grocery_lists.push(newList);
                        $scope.selectedList.id = newList.id;
                        eapp.showAlert(ev, 'Succès', response.data.message);
                    }
                    else
                    {
                        eapp.showAlert(ev, 'Erreur', response.data.message);
                    }
                });
                
          });
        
    };
    
    angular.element(document).ready(function()
    {
        $scope.load_icons(); 
        
        if($scope.isUserLogged)
        {
            if($scope.loggedUser.grocery_lists.length > 0)
            {
                $scope.selectedList.id = $scope.loggedUser.grocery_lists[0].id;
                $scope.selectedGroceryList = $scope.loggedUser.grocery_lists[0];
            }
        }
           
        $scope.getUserProductList();
    });
    
}]);

