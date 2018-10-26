/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('eappApp').factory('appService',function($http, profileData, $mdDialog, $location, eapp) 
{
    
    const APPLICATION_DEFAULT_ADDRESS = "Rue Ste-Catherine Ouest, Québec, Montréal";
    const APPLICATION_DEFAULT_LONGITUDE = -73.5815;
    const APPLICATION_DEFAULT_LATITUDE = 45.4921;
    
    
    var service = this;
    
    service.host = $location.host();
    
    if(service.host.toString() === "localhost")
    {
        service.host = service.host.concat("/eapp/");
    }
    
    service.getSiteUrl = function()
    {
        var siteName = window.location.hostname.toString();
        
        if(siteName == "localhost")
        {
            siteName = siteName.concat("/eapp/");
        }
        
        return location.protocol.concat("//", siteName, "/index.php/");
    };
    
    service.getBaseUrl = function()
    {
        var siteName = window.location.hostname.toString();
        
        if(siteName == "localhost")
        {
            siteName = siteName.concat("/eapp/");
        }
        
        return location.protocol.concat("//", siteName, "/");
    };
    
    service.baseUrl = this.getBaseUrl();
    
    service.siteUrl = this.getBaseUrl();
    
    
    service.currentAddress = APPLICATION_DEFAULT_ADDRESS;
    service.longitude = APPLICATION_DEFAULT_LONGITUDE;
    service.latitude = APPLICATION_DEFAULT_LATITUDE;
    service.method = "";
    service.controller = "";
    
    
    /**
     * The store that is selected in the cart. 
     */
    service.cartSelectedStore = null;
    
    service.ready = $http.post(this.siteUrl.concat("eapp/get_application_data"), null, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    
    service.ready.then(function(response)
    {
        service.path = $location.absUrl().toString().substring(($location.protocol() + "://" + service.host).length);
        
        var pathArray = service.path.split("/");
        
        
        if(pathArray.length > 0)
        {
            if(pathArray[0].toString() === "index.php")
            {
                service.controller = pathArray[1];
                
                if(pathArray.length > 2)
                {
                    service.method = pathArray[2];
                }
                
            }
            else
            {
                service.controller = pathArray[0];
                
                if(pathArray.length > 1)
                {
                    service.method = pathArray[1];
                }
            }
        }
        
        service.cart = response.data.cart;
        service.baseUrl = response.data.base_url;
        service.siteUrl = response.data.site_url;
        service.redirectToLogin = response.data.redirect_to_login;
        service.loggedUser = response.data.user;
        service.isUserLogged = service.loggedUser !== null;
        service.isRegularUser = service.isUserLogged && parseInt(service.loggedUser.subscription) <= 2;
        
        if(!service.isUserLogged)
        {
            if(window.localStorage.getItem("latitude"))
            {
                service.latitude = window.localStorage.getItem("latitude");
            }

            if(window.localStorage.getItem("longitude"))
            {
                service.longitude = window.localStorage.getItem("longitude");
            }

            if(window.localStorage.getItem("currentAddress"))
            {
                service.currentAddress = window.localStorage.getItem("currentAddress");
            }
        }
        
        service.changeLocationUrl = service.siteUrl.concat("/home/change_location");
        
        

        
    });
    
    service.clearSessionItems = function()
    {
        window.sessionStorage.removeItem("filterSettings");
        window.sessionStorage.removeItem("store_id");
        window.sessionStorage.removeItem("category_id");
    };
    
    service.gotoShop = function()
    {
        service.clearSessionItems(); 
        window.location =  service.siteUrl.concat("/shop");
    };
    
    service.get_store_total = function(store_index)
    {   

        var total = 0;            

        for(var key in service.cart)
        {
            total += 
                    !profileData.cartSettings.cartView ? 
                    service.cart[key].store_products[store_index].price * service.cart[key].quantity : 
                    service.cart[key].store_product.price * service.cart[key].quantity;
        }

        return total;
    };
    
    service.get_cart_total_price = function()
    {
        var total = 0;

        if((!angular.isNullOrUndefined(profileData.instance.cartSettings) && profileData.instance.cartSettings.cartView) || service.controller !== 'cart')
        {
            for(var key in service.cart)
            {
                total += parseFloat(service.cart[key].quantity * service.cart[key].store_product.price);
            }
        }
        else
        {
            if(angular.isNullOrUndefined(service.selectedStore) 
                    || angular.isNullOrUndefined(service.selectedStore.store_products)
                    || angular.isNullOrUndefined(service.selectedStore.missing_products))
            {
                return 0;
            }
            
            for(var y in service.selectedStore.store_products)
            {
                total += parseFloat(service.selectedStore.store_products[y].store_product.price * service.selectedStore.store_products[y].quantity);
            }
            
            for(var y in service.selectedStore.missing_products)
            {
                total += parseFloat(service.selectedStore.missing_products[y].store_product.price * service.selectedStore.missing_products[y].quantity);
            }
        }
        

        return total;
    };
        
    service.get_cart_total_available_products = function()
    {
        var total = 0;

        if(profileData.cartView)
        {
            for(var key in service.cart)
            {
                var sp = service.cart[key].store_product;
                if(parseFloat(sp.department_store.distance) === 0)
                {
                    continue;
                }
                total += parseFloat(service.cart[key].quantity * sp.price);
            }
        }
        else
        {
            for(var i in service.selectedStore.store_products)
            {
                total += parseFloat(service.selectedStore.store_products[i].quantity * service.selectedStore.store_products[i].store_product.price);
            }
        }

        return total;
    };



    service.get_optimized_cart_details = function()
    {
        var total = 0;

        for(var key in service.optimized_cart)
        {
            total += parseFloat(service.optimized_cart[key].quantity * service.optimized_cart[key].store_product.price);
        }

        return total;
    };
			
    
		
    service.removeItemFromCart = function(product_id)
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
    };
        
    service.getCartDistance = function()
    {
        return profileData.instance.cartDistance;
    };
	
    service.getOptimizationDistance = function()
    {
        return profileData.instance.optimizationDistance;
    };
        
    
                
    service.getUserCoordinates = function()
    {
        // Get the current geo location only if it's not yet the case
        if ("geolocation" in navigator) 
        {
            navigator.geolocation.getCurrentPosition(function(position) 
            {
                service.longitude = position.coords.longitude;
                service.latitude = position.coords.latitude;
                var geocoder = new google.maps.Geocoder;
                service.geocodeLatLng(geocoder, service.latitude, service.longitude);

                window.localStorage.setItem("longitude", service.longitude);
                window.localStorage.setItem("latitude", service.latitude);
            });
        }
    };
        
    service.getUserCoordinatesFromPostcode = function(postcode)
    {
        var geocoder = new google.maps.Geocoder;

        geocoder.geocode( { 'address': postcode}, function(results, status) 
        {
            if (status == google.maps.GeocoderStatus.OK) 
            {
                service.longitude = results[0].geometry.location.lng();
                service.latitude =results[0].geometry.location.lat();
                service.geocodeLatLng(geocoder, service.latitude, service.longitude);

                window.localStorage.setItem("longitude", service.longitude);
                window.localStorage.setItem("latitude", service.latitude);

            }
        });      
    };
        
    service.geocodeLatLng = function(geocoder, latitude, longitude) 
    {
        var latlng = {lat: latitude, lng: longitude};

        geocoder.geocode({'location': latlng}, function(results, status) 
        {
            if (status === 'OK') 
            {
                if (results[0]) 
                {
                    service.currentAddress = results[0].formatted_address;
                    window.localStorage.setItem("currentAddress", service.currentAddress);
                    service.successMessage = true;
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
        
    service.promptForZipCode = function(ev) 
    {
        if(!window.localStorage.getItem("longitude") && !window.localStorage.getItem("latitude") && !service.isUserLogged && false)
        {
            // Appending dialog to document.body to cover sidenav in docs app
            var confirm = $mdDialog.prompt()
              .title('Veillez entrer votre code postal. ')
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
                    service.latitude = results[0].geometry.location.lat();
                    service.longitude = results[0].geometry.location.lng();
                    window.localStorage.setItem("longitude", service.longitude);
                    window.localStorage.setItem("latitude", service.latitude);

                    if (status !== google.maps.GeocoderStatus.OK) 
                    {
                        service.getUserCoordinates();
                    }

                });

            }, function() 
            {
                service.getUserCoordinates();
            });
        }
    };
    	
    service.inMyList = function(product_id)
    {
        if(service.isUserLogged)
        {
            for(var key in service.loggedUser.grocery_list)
            {
                if(parseInt(service.loggedUser.grocery_list[key].id) === parseInt(product_id))
                {
                    return true;
                }
            }
        }

        return false;
    };
    
    service.selectCaregory = function(category)
    {
        service.clearSessionItems();
        var category_id = parseInt(category.id);
        eapp.recordHit("eapp_product_category ",category_id);
        window.sessionStorage.setItem("category_id", category_id);    
        window.sessionStorage.setItem("category_name", category.name);
        window.location =  service.siteUrl.concat("/shop");
    };
        
    // THis is called for a non logged user to prompt for his zip code
    // If that's not already the case. 
    if(typeof service.promptForZipCode !== "undefined")
    {
        service.promptForZipCode();
    }
        
    return service;
});


