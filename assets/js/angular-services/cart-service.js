/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('eappApp').factory('cart', function($http, appService, profileData) 
{
    var service = this;
    
    service.getCart = function()
    {
        return $http.post(appService.siteUrl.concat("eapp/get_cart_contents"), null);
    };
    
    service.removeFromCart = function(rowid)
    {
        var formData = new FormData();
        formData.append("rowid", rowid);
        
        return $http.post(appService.siteUrl.concat("cart/remove"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    service.updateCart = function(item)
    {
        var formData = new FormData();
        formData.append("item", JSON.stringify(item));
        
        return $http.post(appService.siteUrl.concat("cart/update"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    service.addToCart = function(productID, storeProductID, longitude, latitude, quantity)
    {
        var formData = new FormData();
        formData.append("product_id", productID);
        formData.append("store_product_id", storeProductID);
        formData.append("longitude", longitude);
        formData.append("latitude", latitude);
        formData.append("quantity", quantity);
        
        return $http.post(appService.siteUrl.concat("cart/insert"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    service.clearCart = function()
    {
        return $http.post(appService.siteUrl.concat("cart/destroy"), null);
    };
    
    service.addProductToCart = function(product_id, store_product_id = -1, product_quantity = 1)
    {
        if(typeof store_product_id === 'undefined')
        {
            store_product_id = -1;
        }
        
        var addToCartPromise = service.addToCart(
                product_id, 
                store_product_id, 
                appService.isUserLogged ? appService.loggedUser.profile.longitude : appService.longitude, 
                appService.isUserLogged ? appService.loggedUser.profile.latitude : appService.latitude, 
                product_quantity);
        
        addToCartPromise.then(function(response)
        {
            if(Boolean(response.data.success))
            {
                var cart_item = 
                {
                    rowid : response.data.rowid,
                    store_product : response.data.store_product,
                    top_five_store_products : [],
                    quantity : product_quantity,
                    store_product_id : store_product_id
                };

                if(appService.cart === null || typeof appService.cart === 'undefined')
                {
                    appService.cart = [];
                }

                appService.cart.push(cart_item);
            }
        });
    };
    
    service.getRowID = function(product_id)
    {
        var rowid = -1;

        for(var key in appService.cart)
        {
            if(parseInt(appService.cart[key].store_product.product_id) === parseInt(product_id))
            {
                rowid = appService.cart[key].rowid;
                break;
            }
        }

        return rowid;
    };
    
    service.removeProductFromCart = function(product_id, callback)
    {
        var removePromise = service.removeFromCart(service.getRowID(product_id));

        removePromise.then(function(response)
        {
            if(Boolean(response.data.success))
            {
                var index = -1;

                for(var key in appService.cart)
                {
                    if(parseInt(appService.cart[key].store_product.product_id) === parseInt(product_id))
                    {
                        index = key;
                        break;
                    }
                }

                if(index > -1)
                {
                    appService.cart.splice(index, 1);
                }
                
                if(callback)
                {
                    callback();
                }
            }
        });

    };
    
    service.productInCart = function(product_id)
    {
        for(var key in appService.cart)
        {
            if(parseInt(appService.cart[key].store_product.product_id) === parseInt(product_id))
            {
                return true;
            }
        }

        return false;
    };
    
    /*
    * Get total number of items in the cart
    */
    service.getTotalItemsInCart = function()
    {
        var total = 0;

        for(var key in appService.cart)
        {
            total++;
        }

        return total;
    };
    
    service.getCartPrice = function()
    {
        var total = 0;

        if((!angular.isNullOrUndefined(profileData.instance) && profileData.instance.cartView) || appService.controller !== 'cart')
        {
            for(var key in appService.cart)
            {
                total += parseFloat(appService.cart[key].quantity * appService.cart[key].store_product.price);
            }
        }
        else
        {
            if(angular.isNullOrUndefined(appService.selectedStore) 
                    || angular.isNullOrUndefined(appService.selectedStore.store_products)
                    || angular.isNullOrUndefined(appService.selectedStore.missing_products))
            {
                return 0;
            }
            
            for(var y in appService.selectedStore.store_products)
            {
                total += parseFloat(appService.selectedStore.store_products[y].store_product.price * appService.selectedStore.store_products[y].quantity);
            }
            
            for(var y in appService.selectedStore.missing_products)
            {
                total += parseFloat(appService.selectedStore.missing_products[y].store_product.price * appService.selectedStore.missing_products[y].quantity);
            }
        }

        return total;
    };
    
    service.selectCartStore = function(store)
    {        
        // For each store product in the cart item, 
        // we select the least popular(most expensive)
        for(var i in appService.cart)
        {
            var related_products = appService.cart[i].store_product.related_products;
            
            if(!angular.isNullOrUndefined(related_products))
            {
                // There are no related items. Skip this product
                if(related_products.length === 0)
                {
                    continue;
                }
                
                // The last related product is the most expensive. 
                appService.cart[i].store_product = related_products[related_products.length - 1];
                appService.cart[i].store_product.related_products = related_products;
            }
            
            // Set the cart item store products to the 
            // selected store products
            for(var x in store.store_products)
            {
                if(parseInt(appService.cart[i].store_product.product.id) === parseInt(store.store_products[x].store_product.product.id))
                {
                    store.store_products[x].quantity = appService.cart[i].quantity;
                }
            }
            
            // reset the product price
            for(var x in store.missing_products)
            {
                if(parseInt(appService.cart[i].store_product.product.id) === parseInt(store.missing_products[x].store_product.product.id))
                {
                    store.missing_products[x].quantity = appService.cart[i].quantity;
                }
            }
        }
        
        return store;
    };
    
    service.getStoreProductFormat = function(storeProduct)
    {
        var formatVal = 1;
        
        if(storeProduct.format === 'undefined' || storeProduct.format === null)
        {
            return 1;
        }
        
        var format = storeProduct.format.toLowerCase().split("x");
        
        formatVal = 1;
        
        if(format.length === 1)
        {
            formatVal = parseFloat(format[0]);
        }
        
        if(format.length === 2)
        {
            formatVal = parseFloat(format[0]) * parseFloat(format[1]);
        }
        
        return formatVal;
    };
    
    service.getRelatedProducts = function(store_product)
    {
        var results = 
        {
            differentFormat : null,
            differentStore : null
        };
        
        // split related products to store related and format related
        var different_format_products = [];
        var different_store_products = [];

        for(var i in store_product.related_products)
        {
            if(parseInt(store_product.retailer.id) !== parseInt(store_product.related_products[i].retailer.id))
            {
                different_store_products.push(store_product.related_products[i]);
            }
            
            if(store_product.format.toString().trim() !== store_product.related_products[i].format.toString().trim()
                    && parseInt(store_product.retailer.id) === parseInt(store_product.related_products[i].retailer.id))
            {
                different_format_products.push(store_product.related_products[i]);
            }
        }
        
        // Sort them in ascending order
        different_store_products.sort(function(a, b)
        {
            if(parseFloat(a.compare_unit_price) < parseFloat(b.compare_unit_price))
            {
                return -1;
            }
            
            if(parseFloat(a.compare_unit_price) > parseFloat(b.compare_unit_price))
            {
                return 1;
            }
            
            return 0;
            
        });
        
        // Sort them in ascending order
        different_format_products.sort(function(a, b)
        {
            if(parseFloat(a.compare_unit_price) < parseFloat(b.compare_unit_price))
            {
                return -1;
            }
            
            if(parseFloat(a.compare_unit_price) > parseFloat(b.compare_unit_price))
            {
                return 1;
            }
            
            return 0;
            
        });
        
        results.differentFormat = different_format_products;
        results.differentStore = different_store_products;

        return results;

    };
    
    service.sortCartByStore = function()
    {
        appService.cart.sort(function(a, b)
        {
            var keyA = a.store_product.retailer.name.toString(),
            keyB = b.store_product.retailer.name.toString();
            return keyA.localeCompare(keyB);
        });
    };
    
    return service;
    
});


