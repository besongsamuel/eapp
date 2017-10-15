$(document).ready(function()
{
    var rootScope = angular.element($("html")).scope();
    
    rootScope.$apply(function()
    {
        
        rootScope.is_loading = false;
        rootScope.valid = true;
        rootScope.success_message = "";
        rootScope.error_message = "";
        rootScope.currentAddress = "1953 Rue Ste-Catherine Ouest, Québec, Montréal";
        rootScope.longitude = -73.5815;
        rootScope.latitude = 45.4921;
        rootScope.postcode = "";
        
        if(rootScope.isUserLogged)
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
         * When this is true, the user is viewing optimizations
         * based on the cart. When false, he is viewing optimization 
         * based on the closest stores. 
         */
        rootScope.viewing_cart_optimization = { value: true};

        rootScope.searchInMyList = { value: false};
        
        /**
         * List of optimized cart store product items
         */
        rootScope.optimized_cart = [];
        
        /**
         * When this variable is true, the application is loading store optimizations. 
         * We display the progress bar
         */
        rootScope.loading_store_products = false;

        rootScope.travel_distance = 0;
        
        /**
         *  This is called whenever we want to record a click
         *  on an item
         * @param {type} table_name
         * @param {type} id
         */
        rootScope.hit = function(table_name, id)
        {
            var formData = new FormData();
            formData.append("table_name", table_name);
            formData.append("id", id);
            
            $.post(rootScope.site_url.concat("/admin/hit"), {table_name : table_name, id : id}).then(function(data)
            {
                
            });
        };
        
        rootScope.clearSessionItems = function()
        {
            window.sessionStorage.removeItem("store_id");
            window.sessionStorage.removeItem("category_id");
        };
        
        rootScope.select_category = function($event)
        {
            rootScope.clearSessionItems();
            var element = $event.target;
            var category_id = parseInt(element.id);
            rootScope.hit("eapp_product_category ",category_id);
            window.sessionStorage.setItem("category_id", category_id);    
            window.location =  rootScope.site_url.concat("/shop");
        };
        
        rootScope.get_store_total = function(store_index)
        {   
            
            var total = 0;            
            
            for(var key in rootScope.cart)
            {
                //$rootScope.store_products[index].store_products
                total += 
                        !rootScope.viewing_cart_optimization.value ? 
                        rootScope.cart[key].store_products[store_index].price * rootScope.cart[key].quantity : 
                        rootScope.cart[key].store_product.price * rootScope.cart[key].quantity;
            }

            return total;
        };
        
        rootScope.get_cart_total_price = function()
        {
            var total = 0;

            for(var key in rootScope.cart)
            {
                total += parseFloat(rootScope.cart[key].quantity * rootScope.cart[key].store_product.price);
            }

            return total;
        };
        
        rootScope.get_cart_total_available_products = function()
        {
            var total = 0;
            
            if(rootScope.viewing_cart_optimization.value)
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
        
        rootScope.get_cart_total_unavailable_products = function()
        {
            var total = 0;
            
            if(rootScope.viewing_cart_optimization.value)
            {
                for(var key in rootScope.cart)
                {
                    var sp = rootScope.cart[key].store_product;
                    if(parseFloat(sp.department_store.distance) > 0)
                    {
                        continue;
                    }
                    total += parseFloat(rootScope.cart[key].quantity * sp.price);
                }
            }
            else
            {
                for(var i in rootScope.selectedStore.missing_products)
                {
                    total += parseFloat(rootScope.selectedStore.missing_products[i].quantity * rootScope.selectedStore.missing_products[i].store_product.price);
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
        
        rootScope.add_product_to_cart = function(product_id, store_product_id)
        {
            if(typeof store_product_id === 'undefined')
            {
                store_product_id = -1;
            }
            
            var data = 
            {
                product_id : product_id,
                longitude : rootScope.longitude,
                latitude : rootScope.latitude,
                store_product_id : store_product_id
            };

            $.ajax({
                type: 'POST',
                url:   rootScope.site_url.concat("/cart/insert"),
                data: data,
                success: function(response)
                {
                    var response_data = JSON.parse(response);

                    if(Boolean(response_data.success))
                    {
                        // Add Global Cart list
                        var cart_item = 
                        {
                            rowid : response_data.rowid,
                            store_product : response_data.store_product,
                            top_five_store_products : [],
                            quantity : 1
                        };


                        rootScope.$apply(function()
                        {
                            if(rootScope.cart === null || typeof rootScope.cart === 'undefined')
                            {
                                rootScope.cart = [];
                            }
                            
                            rootScope.cart.push(cart_item);
                        });
                    }
                },
                async:true
            });
        };
		
	rootScope.can_add_to_cart = function(product_id)
        {
           for(var key in rootScope.cart)
            {
                if(parseInt(rootScope.cart[key].store_product.product_id) === parseInt(product_id))
                {
                    return false;
                }
            }

            return true;
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
            rootScope.longitude = window.localStorage.getItem("longitude");
            rootScope.latitude = window.localStorage.getItem("latitude");

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
                        if (status === google.maps.GeocoderStatus.OK) 
                        {
                            rootScope.latitude = results[0].geometry.location.lat();
                            rootScope.longitude = results[0].geometry.location.lng();
                            window.localStorage.setItem("longitude", rootScope.longitude);
                            window.localStorage.setItem("latitude", rootScope.latitude);
                            rootScope.getCartContents();
                        }
                        else
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
        
        rootScope.selectedProduct = null;
        rootScope.searchProductText = "";
        rootScope.myCategories = [];
        rootScope.maxNumItems = 50;
        
        rootScope.addNewProductToList = function()
        {
            rootScope.addToMyList(rootScope.selectedProduct);
        }
		
        rootScope.addToMyList = function(product)
        {
            product.quantity = 1;

            rootScope.AddProductToList(product);

            rootScope.saveMyList();
        };
        
        rootScope.AddProductToList = function(product)
        {
            if(typeof product !== "undefined" && product !== null &&  rootScope.my_list_count() < rootScope.maxNumItems)
            {
                product.quantity = (typeof product.quantity !== "undefined" && product.quantity) ? product.quantity : 1;
                // get product category id
                var category = product.category;
                // Check if category exists
                var index = rootScope.myCategories.map(function(e) { return e.id; }).indexOf(category.id);

                if(index !== -1)
                {
                    // Check if product exists in categories
                    var product_index = rootScope.myCategories[index].products.map(function(e) { return e.id; }).indexOf(product.id);
                    if(product_index !== -1)
                    {
                        rootScope.myCategories[index].products[product_index].quantity += product.quantity;
                    }
                    else
                    {
                        if(rootScope.myCategories[index].products === null || typeof rootScope.myCategories[index].products === 'undefined')
                        {
                            rootScope.myCategories[index].products = [];
                        }

                        rootScope.myCategories[index].products.push(product);
                    }
                }
                else
                {
                    // create category
                    category.products = [];
                    category.products.push(product);
                    rootScope.myCategories.push(category);
                }
            }
        };
        
        

        rootScope.removeFromMyList = function(product)
        {
            rootScope.removeProductFromList(product.id, null, false);
        };

        rootScope.favoriteChanged = function(product)
        {
            if(product.in_user_grocery_list)
            {
                rootScope.addToMyList(product);
            }
            else
            {
                rootScope.removeFromMyList(product);
            }
        };
        
        rootScope.getUserProductList = function()
        {
            if(rootScope.loggedUser !== null)
            {
                for(var i in rootScope.loggedUser.grocery_list)
                {
                    rootScope.AddProductToList(rootScope.loggedUser.grocery_list[i]);
                }
            }

            rootScope.currentProduct = null;

        };
		
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
        
        rootScope.my_list_count = function()
        {
            var count = 0;

            for(var index in rootScope.myCategories)
            {
                count += rootScope.myCategories[index].products.length;
            }

            return count;
        };
	
    rootScope.flyer_products_count = function()
    {
        var count = 0;

        for(var index in rootScope.myCategories)
        {
            for(var i in rootScope.myCategories[index].products)
            {
                var product = rootScope.myCategories[index].products[i];
                
                if(typeof product.store_products !== "undefined" && product.store_products !== null)
                {
                    count += product.store_products.length;
                }
                
            }
        }
        return count;
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
