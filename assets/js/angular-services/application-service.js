/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('eappApp').factory('appService',function($http, $mdDialog, $location) 
{
    
    const APPLICATION_DEFAULT_ADDRESS = "Rue Ste-Catherine Ouest, Québec, Montréal";
    const APPLICATION_DEFAULT_LONGITUDE = -73.5815;
    const APPLICATION_DEFAULT_LATITUDE = 45.4921;
    const APPLICATION_DEFAULT_POSTCODE = "H3B 1K1";
    
    
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
    service.postcode = APPLICATION_DEFAULT_POSTCODE;
    service.method = "";
    service.controller = "";
    
    service.path = $location.absUrl().toString().substring(($location.protocol() + "://" + service.host).length);
        
    var pathArray = service.path.split("/");

    var i = 0;

    if(pathArray.length > i)
    {
        while(pathArray[i] == "" && pathArray.length > i + 1)
        {
            i++;
        }

        if(pathArray[i].toString() === "index.php")
        {
            i++;

            while(pathArray[i] == "" && pathArray.length > i + 1)
            {
                i++;
            }

            service.controller = pathArray[i];

            i++;

            if(pathArray.length > i)
            {
                while(pathArray[i] == "" && pathArray.length > i + 1)
                {
                    i++;
                }

                if(pathArray.length > i)
                {
                    service.method = pathArray[i];
                }
            }
        }
        else
        {
            while(pathArray[i] == "" && pathArray.length > i + 1)
            {
                i++;
            }

            service.controller = pathArray[i];

            i++;

            if(pathArray.length > i)
            {
                while(pathArray[i] == "" && pathArray.length > i + 1)
                {
                    i++;
                }

                if(pathArray.length > i)
                {
                    service.method = pathArray[i];
                }
            }
        }
    }
    
    service.ready = $http.post(this.siteUrl.concat("eapp/get_application_data"), null, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    
    service.ready.then(function(response)
    {
        service.cart = response.data.cart;
        service.baseUrl = response.data.base_url.concat("/");
        service.siteUrl = response.data.site_url.concat("/");
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
            
            if(window.localStorage.getItem("postcode"))
            {
                service.postcode = window.localStorage.getItem("postcode");
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
	
    service.getUserCoordinates = function(callback)
    {
        // Get the current geo location only if it's not yet the case
        if ("geolocation" in navigator) 
        {
            navigator.geolocation.getCurrentPosition(function(position) 
            {
                service.longitude = position.coords.longitude;
                service.latitude = position.coords.latitude;
                var geocoder = new google.maps.Geocoder;
                service.geocodeLatLng(geocoder, service.latitude, service.longitude, callback);
            });
        }
    };
        
    service.getUserCoordinatesFromPostcode = function(postcode, callback)
    {
        var geocoder = new google.maps.Geocoder;

        geocoder.geocode( { 'address': postcode}, function(results, status) 
        {
            if (status == google.maps.GeocoderStatus.OK) 
            {
                callback(results[0]);
            }
        });      
    };
        
    service.geocodeLatLng = function(geocoder, latitude, longitude, callback) 
    {
        var latlng = {lat: latitude, lng: longitude};

        geocoder.geocode({'location': latlng}, function(results, status) 
        {
            if (status === 'OK') 
            {
                if (results[0]) 
                {
                    callback(results[0]);
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
    
    service.selectCategory = function(category)
    {
        service.clearSessionItems();
        var category_id = parseInt(category.id);
        service.recordHit("eapp_product_category ",category_id);
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
    
    service.getActiveUserCoordinates = function()
    {
        var longitude = service.isUserLogged ? service.loggedUser.profile.longitude : service.longitude;
        var latitude = service.isUserLogged ? service.loggedUser.profile.latitude : service.latitude;
        
        return {
            longitude : longitude,
            latitude : latitude
        };
    };
    
    service.recordRetailerHit = function(id, distance)
    {
        var formData = new FormData();
        formData.append("id", id);
        
        if(service.isUserLogged)
        {
            formData.append("distance", distance);
            formData.append("postcode", service.loggedUser.profile.postcode);
            return $http.post(service.siteUrl.concat("eapp/record_retailer_hit"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

        }
        else
        {
            var geocoder = new google.maps.Geocoder;
            var latlng = {lat: parseFloat(service.latitude), lng: parseFloat(service.longitude)};
            geocoder.geocode({'location': latlng}, function(results, status) 
            {
                
                var postcode = '';
                
                if (status === 'OK') 
                {
                    if(results[0]) 
                    {
                        if(results[0].address_components[8])
                        {
                            postcode = results[0].address_components[8].long_name;
                        }
                    } 
                } 
                
                formData.append("postcode", postcode);
                formData.append("distance", distance);
                return $http.post(service.siteUrl.concat("eapp/record_retailer_hit"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

                
            });
        }
        
    };
    
    service.recordProductStat = function(storeProductId, type, distance, is_product = false)
    {
        var formData = new FormData();
        formData.append("id", storeProductId);
        formData.append("type", type);
        formData.append("is_product", JSON.stringify(is_product));
        
        if(service.isUserLogged)
        {
            formData.append("distance", distance);
            formData.append("postcode", service.loggedUser.profile.postcode);
            return $http.post(service.siteUrl.concat("/eapp/record_stat"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

        }
        else
        {
            var geocoder = new google.maps.Geocoder;
            var latlng = {lat: parseFloat(service.latitude), lng: parseFloat(service.longitude)};
            geocoder.geocode({'location': latlng}, function(results, status) 
            {
                
                var postcode = '';
                
                if (status === 'OK') 
                {
                    if(results[0]) 
                    {
                        if(results[0].address_components[8])
                        {
                            postcode = results[0].address_components[8].long_name;
                        }
                    } 
                } 
                
                formData.append("postcode", postcode);
                formData.append("distance", distance);
                return $http.post(service.siteUrl.concat("/eapp/record_stat"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
            });
        }
        
        
    };
    
    service.recordHit = function(tableName, id)
    {
        var formData = new FormData();
        formData.append("table_name", tableName);
        formData.append("id", id);
        return $http.post(service.siteUrl.concat("admin/hit"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
        
    return service;
});


