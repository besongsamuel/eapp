/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('eappApp').factory('cart', function(eapp, appService, profileData) 
{
    var service = this;
    
    service.addProductToCart = function(product_id, store_product_id = -1, product_quantity = 1)
    {
        if(typeof store_product_id === 'undefined')
        {
            store_product_id = -1;
        }
        
        var addToCartPromise = eapp.addToCart(
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

        for(var key in service.cart)
        {
            if(parseInt(service.cart[key].store_product.product_id) === parseInt(product_id))
            {
                rowid = service.cart[key].rowid;
                break;
            }
        }

        return rowid;
    };
    
    service.removeProductFromCart = function(product_id)
    {
        var removePromise = eapp.removeFromCart(service.getRowID(product_id));

        removePromise.then(function(response)
        {
            if(Boolean(response.data.success))
            {
                var index = -1;

                for(var key in service.cart)
                {
                    if(parseInt(service.cart[key].store_product.product_id) === parseInt(product_id))
                    {
                        index = key;
                        break;
                    }
                }

                if(index > -1)
                {
                    service.cart.splice(index, 1);
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

        if((!angular.isNullOrUndefined(profileData.instance.cartSettings) && profileData.instance.cartSettings.cartView) || appService.controller !== 'cart')
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
    
    return service;
    
});


