$(document).ready(function()
{
    var rootScope = angular.element($("html")).scope();
    
    rootScope.$apply(function()
    {
        rootScope.is_loading = false;
        rootScope.valid = true;
        rootScope.success_message = "";
        rootScope.error_message = "";
        rootScope.postcode = "";
        
        
        rootScope.currentAddress = "Rue Ste-Catherine Ouest, Québec, Montréal";
        rootScope.longitude = -73.5815;
        rootScope.latitude = 45.4921;
        
        if(!rootScope.isUserLogged)
        {
            if(window.localStorage.getItem("latitude"))
            {
                rootScope.latitude = window.localStorage.getItem("latitude");
            }

            if(window.localStorage.getItem("longitude"))
            {
                rootScope.longitude = window.localStorage.getItem("longitude");
            }

            if(window.localStorage.getItem("currentAddress"))
            {
                rootScope.currentAddress = window.localStorage.getItem("currentAddress");
            }
        }
        
        /* CART */
        
        /**
         * When this variable is true, the application is loading store optimizations. 
         * We display the progress bar
         */
        rootScope.loading_store_products = false;

        rootScope.travel_distance = 0;
        
        /**
         * If true, the user is in cart view
         * If false, the user is in store view
         */
        rootScope.cartView = true;
        
        /**
         * The store selected in the cart
         */
        rootScope.selectedStore = null;
        
        rootScope.clearSessionItems = function()
        {
            window.sessionStorage.removeItem("store_id");
            window.sessionStorage.removeItem("category_id");
        };
        
        rootScope.get_store_total = function(store_index)
        {   
            
            var total = 0;            
            
            for(var key in rootScope.cart)
            {
                //$rootScope.store_products[index].store_products
                total += 
                        !rootScope.cartView ? 
                        rootScope.cart[key].store_products[store_index].price * rootScope.cart[key].quantity : 
                        rootScope.cart[key].store_product.price * rootScope.cart[key].quantity;
            }

            return total;
        };
        
        /**
     * Ovverides the same function in the root scope
     * @returns {Number}
     */
    rootScope.get_cart_total_price = function()
    {
        var total = 0;

        if(rootScope.cartView)
        {
            for(var key in rootScope.cart)
            {
                total += parseFloat(rootScope.cart[key].quantity * rootScope.cart[key].store_product.price);
            }
        }
        else
        {
            for(var y in rootScope.selectedStore.store_products)
            {
                total += parseFloat(rootScope.selectedStore.store_products[y].store_product.price * rootScope.selectedStore.store_products[y].quantity);
            }
            
            for(var y in rootScope.selectedStore.missing_products)
            {
                total += parseFloat(rootScope.selectedStore.missing_products[y].store_product.price * rootScope.selectedStore.missing_products[y].quantity);
            }
        }
        

        return total;
    };
        
    rootScope.get_cart_total_available_products = function()
    {
        var total = 0;

        if(rootScope.cartView)
        {
            for(var key in rootScope.cart)
            {
                var sp = rootScope.cart[key].store_product;
                if(parseFloat(sp.department_store.distance) === 0)
                {
                    continue;
                }
                total += parseFloat(rootScope.cart[key].quantity * sp.price);
            }
        }
        else
        {
            for(var i in rootScope.selectedStore.store_products)
            {
                total += parseFloat(rootScope.selectedStore.store_products[i].quantity * rootScope.selectedStore.store_products[i].store_product.price);
            }
        }

        return total;
    };

    /*
    * Get total number of items in the cart
    */
    rootScope.get_cart_item_total = function()
    {
        var total = 0;

        for(var key in rootScope.cart)
        {
            //if(parseFloat(rootScope.cart[key].store_product.price) !== 0)
            {
                total++;
            }
        }

        return total;
    };

    rootScope.get_optimized_cart_details = function()
    {
        var total = 0;

        for(var key in rootScope.optimized_cart)
        {
            total += parseFloat(rootScope.optimized_cart[key].quantity * rootScope.optimized_cart[key].store_product.price);
        }

        return total;
    };
        
        
			
	rootScope.getRowID = function(product_id)
        {
            var rowid = -1;

            for(var key in rootScope.cart)
            {
                if(parseInt(rootScope.cart[key].store_product.product_id) === parseInt(product_id))
                {
                    rowid = rootScope.cart[key].rowid;
                    break;
                }
            }

            return rowid;
        };
		
	rootScope.removeItemFromCart = function(product_id)
        {
            var index = -1;

            for(var key in rootScope.cart)
            {
                if(parseInt(rootScope.cart[key].store_product.product_id) === parseInt(product_id))
                {
                    index = key;
                    break;
                }
            }

            if(index > -1)
            {
                rootScope.cart.splice(index, 1);
            }
        };
		
        rootScope.productInCart = function(product_id)
        {
            for(var key in rootScope.cart)
            {
                if(parseInt(rootScope.cart[key].store_product.product_id) === parseInt(product_id))
                {
                    return true;
                }
            }

            return false;
        };
    
                
	rootScope.getUserCoordinates = function()
        {
            // Get the current geo location only if it's not yet the case
            if ("geolocation" in navigator) 
            {
                navigator.geolocation.getCurrentPosition(function(position) 
                {
                    rootScope.longitude = position.coords.longitude;
                    rootScope.latitude = position.coords.latitude;
                    var geocoder = new google.maps.Geocoder;
                    rootScope.geocodeLatLng(geocoder, rootScope.latitude, rootScope.longitude);
                    
                    window.localStorage.setItem("longitude", rootScope.longitude);
                    window.localStorage.setItem("latitude", rootScope.latitude);
                });
            }
        };
        
        rootScope.getUserCoordinatesFromPostcode = function()
        {
            var geocoder = new google.maps.Geocoder;
            
            geocoder.geocode( { 'address': rootScope.postcode}, function(results, status) 
            {
                if (status == google.maps.GeocoderStatus.OK) 
                {
                    rootScope.longitude = results[0].geometry.location.lng();
                    rootScope.latitude =results[0].geometry.location.lat();
                    rootScope.geocodeLatLng(geocoder, rootScope.latitude, rootScope.longitude);
                    
                    window.localStorage.setItem("longitude", rootScope.longitude);
                    window.localStorage.setItem("latitude", rootScope.latitude);
                    
                }
            });      
        };
        
        rootScope.geocodeLatLng = function(geocoder, latitude, longitude) 
        {
            var latlng = {lat: latitude, lng: longitude};
            
            geocoder.geocode({'location': latlng}, function(results, status) 
            {
                if (status === 'OK') 
                {
                    if (results[0]) 
                    {
                        rootScope.$apply(function()
                        {
                            rootScope.currentAddress = results[0].formatted_address;
                            window.localStorage.setItem("currentAddress", rootScope.currentAddress);
                            
                            rootScope.successMessage = true;
                        });
                        
                    } 
                    else 
                    {
                        
                        window.alert('No results found');
                    }
                } 
                else 
                {
                    window.alert('Geocoder failed due to: ' + status);
                }
            });
        };
        
	rootScope.promptForZipCode = function(ev) 
        {
            if(!window.localStorage.getItem("longitude") && !window.localStorage.getItem("latitude") && !rootScope.isUserLogged && false)
            {
                // Appending dialog to document.body to cover sidenav in docs app
                var confirm = $mdDialog.prompt()
                  .title('Veillez entrer votre code postale. ')
                  .textContent('Ceci vas aider a optimiser les resultats.')
                  .placeholder('Votre Code Postale E.g. H1H 1H1')
                  .ariaLabel('Code Postale')
                  .initialValue('')
                  .targetEvent(ev)
                  .ok('Valider!')
                  .cancel('Annuler');

                $mdDialog.show(confirm).then(function(result) 
                {
                    var address = result;
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode( { 'address': address}, function(results, status) 
                    {
                        rootScope.latitude = results[0].geometry.location.lat();
                        rootScope.longitude = results[0].geometry.location.lng();
                        window.localStorage.setItem("longitude", rootScope.longitude);
                        window.localStorage.setItem("latitude", rootScope.latitude);
                        
                        if (status !== google.maps.GeocoderStatus.OK) 
                        {
                            rootScope.getUserCoordinates();
                        }
                        
                    });

                }, function() 
                {
                    rootScope.getUserCoordinates();
                });
            }
        };
    	
	/* CART END*/
		
	/* ACCOUNT */
        
	
        rootScope.inMyList = function(product_id)
        {
            if(rootScope.isUserLogged)
            {
                for(var key in rootScope.loggedUser.grocery_list)
                {
                    if(parseInt(rootScope.loggedUser.grocery_list[key].id) === parseInt(product_id))
                    {
                        return true;
                    }
                }
            }

            return false;
        };
        
	/*ACCOUNT END*/
        
        // THis is called for a non logged user to prompt for his zip code
        // If that's not already the case. 
        if(typeof rootScope.promptForZipCode !== "undefined")
        {
            rootScope.promptForZipCode();
        }
        
    });

});
