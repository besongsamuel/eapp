/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

const STAT_TYPE_CLICK = 0;


// Create eapp service to get and update our data
angular.module('eappApp').factory('eapp', function($http, appService, $mdDialog, profileData, cart)
{
    var eappService = {};
    
   
    eappService.showAlert = function(ev, title, message) 
    {
        
        var scrollTop = $(document).scrollTop();
        // Appending dialog to document.body to cover sidenav in docs app
        // Modal dialogs should fully cover application
        // to prevent interaction outside of dialog
        $mdDialog.show(
                
                $mdDialog.alert()
                .title(title)
                .textContent(message)
                .ok('Ok')
                .targetEvent(ev)
        )
        .finally(function()
        {
            $(document).scrollTop(scrollTop);
        });
    };
    
    eappService.createPrompt = function(ev, caption, description, placeHolder, initialValue, yesLabel = 'Oui', noLabel = 'Non')
    {
        var prompt = $mdDialog.prompt()
            .title(caption)
            .textContent(description)
            .placeholder(placeHolder)
            .ariaLabel(placeHolder)
            .initialValue(initialValue)
            .targetEvent(ev)
            .required(true)
            .ok(yesLabel)
            .cancel(noLabel);
    
        return prompt;
    };
    
    eappService.scrollTo = function(divID)
    {
        $('html, body').animate({
            scrollTop: $("#" + divID).offset().top
        }, 2000);
    };
    
    eappService.getUrlParameter = function(sParam) 
    {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };
    
    eappService.getProduct = function(productId)
    {
        return $http.post(eappService.getSiteUrl().concat("cart/get_product/").concat(productId.toString()), null);
    };
    
    eappService.getOtiprixProduct = function(productId)
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_otiprix_product/").concat(productId.toString()), null);
    };
    
    eappService.getSiteUrl = function()
    {
        var siteName = window.location.hostname.toString();
        
        if(siteName == "localhost")
        {
            siteName = siteName.concat("/eapp/");
        }
        
        return location.protocol.concat("//", siteName, "/index.php/");
    };
    
    eappService.getBaseUrl = function()
    {
        var siteName = window.location.hostname.toString();
        
        if(siteName == "localhost")
        {
            siteName = siteName.concat("/eapp/");
        }
        
        return location.protocol.concat("//", siteName, "/");
    };
    
    eappService.siteUrl = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/site_url"), null);
    };
    
    eappService.baseUrl = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/base_url"), null);
    };
    
    eappService.getRetailers = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_retailers"), null);
    };
    
    eappService.getBrands = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_brands"), null);
    };
    
    eappService.getProductsCount = function(filter)
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_products_count/", filter), null);
    };
    
    eappService.deleteProduct = function(product_id)
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/delete_product/", product_id), null);
    };
    
    eappService.deleteSubCategory = function(product_id)
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/delete_sub_category/", product_id), null);
    };
    
    eappService.getHomeProducts = function()
    {
        var formData = new FormData();
        // User's longitude
        formData.append("longitude", appService.longitude);
        // user's latitude
        formData.append("latitude", appService.latitude);
        //
        formData.append("distance", profileData.instance.optimizationDistance);
        return $http.post(eappService.getSiteUrl().concat("home/get_category_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getProducts = function(query)
    {
        var formData = new FormData();
        
        formData.append("query", JSON.stringify(query));
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/get_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getLatestProducts = function()
    {
        return $http.post(eappService.getSiteUrl().concat("cart/get_latest_products"), null);
    };
    
    eappService.getCategoryProducts = function(id, query, resultsFilter, distance)
    {
        var formData = new FormData();
        formData.append("page", query.page);
        formData.append("limit", query.limit);
        formData.append("filter", query.filter);
        formData.append("order", query.order);
        // User's longitude
        formData.append("longitude", appService.longitude);
        // user's latitude
        formData.append("latitude", appService.latitude);
        formData.append("profileData", JSON.stringify(profileData.get()));
        
        if(!angular.isNullOrUndefined(resultsFilter))
        {
            resultsFilter.categories = null;
        }
        
        formData.append("resultsFilter", JSON.stringify(resultsFilter));
        
        if(!angular.isNullOrUndefined(id))
        {
            formData.append("category_id", id);
        }
        formData.append("distance", distance);
        
        return $http.post(eappService.getSiteUrl().concat("/shop/get_store_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.getMostViewedCategories = function()
    {
        return $http.post(eappService.getSiteUrl().concat("/eapp/get_most_viewed_categories"), null, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getFlyerProducts = function(id, query, resultsFilter, distance)
    {
        var formData = new FormData();
        formData.append("page", query.page);
        formData.append("limit", query.limit);
        formData.append("filter", query.filter);
        formData.append("order", query.order);
        formData.append("profileData", JSON.stringify(profileData.get()));
        // User's longitude
        formData.append("longitude", appService.longitude);
        // user's latitude
        formData.append("latitude", appService.latitude);
        if(!angular.isNullOrUndefined(resultsFilter))
        {
            resultsFilter.stores = null;
        }
        formData.append("resultsFilter", JSON.stringify(resultsFilter));
        
        if(!angular.isNullOrUndefined(id))
        {
            formData.append("store_id", id);
        }
        formData.append("distance", distance);
        
        return $http.post(eappService.getSiteUrl().concat("/shop/get_store_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.getStoreProducts = function(query, resultsFilter, distance)
    {
        var formData = new FormData();
        formData.append("page", query.page);
        formData.append("limit", query.limit);
        formData.append("filter", query.filter);
        formData.append("order", query.order);
        formData.append("profileData", JSON.stringify(profileData.get()));
        formData.append("resultsFilter", JSON.stringify(resultsFilter));
        // User's longitude
        formData.append("longitude", appService.longitude);
        // user's latitude
        formData.append("latitude", appService.latitude);
        formData.append("distance", distance);
        
        return $http.post(eappService.getSiteUrl().concat("/shop/get_store_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.addProductToList = function(product, listID)
    {
        var formData = new FormData();
        formData.append("product_id", product.id);
        formData.append("list_id", listID);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/add_product_to_list"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.removeProductFromList = function(product, listID)
    {
        var formData = new FormData();
        formData.append("product_id", product.id);
        formData.append("list_id", listID);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/remove_product_from_list"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.addProductsToList = function(products, listID)
    {
        var formData = new FormData();
        formData.append("products", JSON.stringify(products));
        formData.append("list_id", listID);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/add_products_to_list"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.removeProductsFromList = function(products, listID)
    {
        var formData = new FormData();
        formData.append("products", JSON.stringify(products));
        formData.append("list_id", listID);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/remove_products_from_list"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getCart = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_cart_contents"), null);
    };
    
    eappService.removeFromCart = function(rowid)
    {
        var formData = new FormData();
        formData.append("rowid", rowid);
        
        return $http.post(eappService.getSiteUrl().concat("cart/remove"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.updateCart = function(item)
    {
        var formData = new FormData();
        formData.append("item", JSON.stringify(item));
        
        return $http.post(eappService.getSiteUrl().concat("cart/update"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.addToCart = function(productID, storeProductID, longitude, latitude, quantity)
    {
        var formData = new FormData();
        formData.append("product_id", productID);
        formData.append("store_product_id", storeProductID);
        formData.append("longitude", longitude);
        formData.append("latitude", latitude);
        formData.append("quantity", quantity);
        
        return $http.post(eappService.getSiteUrl().concat("cart/insert"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.clearCart = function()
    {
        return $http.post(eappService.getSiteUrl().concat("cart/destroy"), null);
    };
    
    eappService.changeDistance = function(distToChange, newValue)
    {
        var formData = new FormData();
        formData.append("distance_to_change", distToChange);
        formData.append("value", newValue);
        return $http.post(eappService.getSiteUrl().concat("eapp/change_distance"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getCategories = function(offset = 0, limit = -1)
    {
        var formData = new FormData();
        formData.append("offset", offset);
        formData.append("limit", limit);
        return $http.post(eappService.getSiteUrl().concat("eapp/get_categories"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getSubCategories = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_subcategories"), null);  
    };
	
    eappService.getAdminSubCategories = function(query)
    {
        var formData = new FormData();
        formData.append("query", JSON.stringify(query));
        
        return $http.post(eappService.getSiteUrl().concat("eapp/get_admin_subcategories"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getCloseRetailers = function(distance)
    {
        var formData = new FormData();
        formData.append("distance", distance);
        formData.append("longitude", parseFloat(appService.longitude));
        formData.append("latitude", parseFloat(appService.latitude));
        
        return $http.post(eappService.getSiteUrl().concat("eapp/get_close_retailers"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.recordHit = function(tableName, id)
    {
        var formData = new FormData();
        formData.append("table_name", tableName);
        formData.append("id", id);
        return $http.post(eappService.getSiteUrl().concat("admin/hit"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.saveFavoriteStores = function(favoriteStores)
    {
        var formData = new FormData();
        formData.append("selected_retailers", favoriteStores);
        return $http.post(eappService.getSiteUrl().concat("account/save_favorite_stores"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getFavoriteStores = function()
    {
        return $http.post(eappService.getSiteUrl().concat("account/get_favorite_stores"), null);
    };
    
    eappService.updateUserProfile = function(userObject)
    {
        var formData = new FormData();
        formData.append("profile[firstname]", userObject.profile.firstname);
        formData.append("profile[lastname]", userObject.profile.lastname);
        formData.append("profile[country]", userObject.profile.country);
        formData.append("profile[state]", userObject.profile.state);
        formData.append("profile[city]", userObject.profile.city);
        formData.append("profile[address]", userObject.profile.address);
        formData.append("profile[postcode]", userObject.profile.postcode);
        
        return $http.post(eappService.getSiteUrl().concat("account/save_profile"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.updateAddress = function(newAddress)
    {
        var formData = new FormData();
        formData.append("profile", JSON.stringify(newAddress));
        return $http.post(eappService.getSiteUrl().concat("account/update_address"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.registerUser = function(user)
    {
        // Create form data
        var formData = new FormData();
        formData.append("account[email]", user.email);
        formData.append("account[password]", user.password);
        formData.append("account[security_question_id]", user.security_question_id);
        formData.append("account[security_question_answer]", user.security_question_answer);

        formData.append("profile[firstname]", user.firstname);
        formData.append("profile[lastname]", user.lastname);
        formData.append("profile[country]", user.country);
        formData.append("profile[state]", user.state);
        formData.append("profile[city]", user.city);
        formData.append("profile[address]", user.address);
        formData.append("profile[postcode]", user.postcode);
        
        return $http.post(eappService.getSiteUrl().concat("account/registration"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.getSecurityQuestions = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_security_questions"), null);
    };
    
    eappService.getLatestProducts = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_latest_products"), null);
    };
    
    eappService.sendPasswordReset = function(email)
    {
        var formData = new FormData();
        formData.append("email", email);
        
        return $http.post(eappService.getSiteUrl().concat("account/send_password_reset"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.resetPassword = function(password, reset_token)
    {
        var formData = new FormData();
        formData.append("password", password);
        formData.append("reset_token", reset_token);
        return $http.post(eappService.getSiteUrl().concat("account/modify_password"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.sendVerification = function(phone_number)
    {
        var formData = new FormData();
        formData.append("number", phone_number);
        
        return $http.post(eappService.getSiteUrl().concat("/account/send_verification"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.validateCode = function(code)
    {
        var formData = new FormData();
        formData.append("code", code);
        
        return $http.post(eappService.getSiteUrl().concat("/account/validate_code"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.viewProduct = function($scope, product_id, ev)
    {
        let quantity = $scope.storeProduct ? $scope.storeProduct.quantity : 1;
        // Get the latest products
        var promise = eappService.getProduct(product_id);
    
        promise.then(function(response)
        {
            appService.recordProductStat(product_id, profileData.instance.optimizationDistance, STAT_TYPE_CLICK);
            
            $scope.storeProduct = response.data;
            
            $scope.storeProduct.quantity = quantity;
            
            $scope.scrollTop = $(document).scrollTop();
            
            // Open dialog
            $mdDialog.show({
                controller: ViewProductController,
                templateUrl:  eappService.getBaseUrl() + 'assets/templates/otiprix-product.html',
                parent: angular.element(document.body),
                targetEvent: ev,
                clickOutsideToClose:true,
                disableParentScroll : true,
                preserveScope:true,
                scope : $scope,
                fullscreen: true,
                onRemoving : function()
                {
                    // Restore scroll
                    $(document).scrollTop($scope.scrollTop);
                },
                onShowing : function()
                {
                    $scope.RelatedProductsAvailable = !angular.isNullOrUndefined($scope.storeProduct.similar_products) && $scope.storeProduct.similar_products.length > 0;
                }
            })
            .then(function(answer) {

            }, function() {

            });
        },
        function(errorResponse)
        {
            $scope.storeProduct = null;
        });
    };
    
    function ViewProductController($scope, $mdDialog)
    {
        
        $scope.close = function() 
        {
            $mdDialog.cancel();
        };
        
        $scope.productInCart = function(product_id)
        {
            return cart.productInCart(product_id);
        };
    }
    
    eappService.getUnitCompareUnits = function(id)
    {
        var formData = new FormData();
        formData.append("id", id);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/get_unit_compareunits"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getCompareUnitUnits = function(id)
    {
        var formData = new FormData();
        formData.append("id", id);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/get_compareunit_units"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getUnits = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_units"), null);
    };
    
    eappService.getCompareUnits = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_compareunits"), null);
    };
    
    eappService.getUnitCompareUnit = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_unit_compareunit"), null);
    };
    
    eappService.getProductUnitCompareUnit = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_product_unit_compareunit"), null);
    };
    
    eappService.getUserOptimizations = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_user_optimizations"), null);
    };
    
    eappService.getUserGroceryLists = function()
    {
        return $http.post(eappService.getSiteUrl().concat("eapp/get_user_grocery_lists"), null);
    };
    
    eappService.getStoreProduct = function(spID)
    {
        var formData = new FormData();
        formData.append("id", spID);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/get_store_product"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.subscribe = function(email)
    {
        var formData = new FormData();
        formData.append("email", email);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/subscribe"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.unsubscribe = function(token)
    {
        var formData = new FormData();
        formData.append("token", token);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/unsubscribe"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getUnsubscribeEmailFromToken = function(token)
    {
        var formData = new FormData();
        formData.append("token", token);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/get_email_from_unsubscribe_token"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getProductsWithStoreProducts = function(filter)
    {
        var formData = new FormData();
        formData.append("filter", filter);
        
        return $http.post(eappService.getSiteUrl().concat("/eapp/get_products_with_store_products"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.createNewList = function(name)
    {
        var formData = new FormData();
        formData.append("name", name);
        return $http.post(eappService.getSiteUrl().concat("/account/create_new_list"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});

    };
    
    eappService.deleteGroceryList = function(id)
    {
        var formData = new FormData();
        formData.append("id", id);
        
        return $http.post(eappService.getSiteUrl().concat("/account/delete_grocery_list"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.addDepartmentStore = function(departmentStore)
    {
        var formData = new FormData();
        formData.append("department_store", JSON.stringify(departmentStore));
        
        return $http.post(eappService.getSiteUrl().concat("/account/add_department_store"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.removeDepartmentStore = function(id)
    {
        var formData = new FormData();
        formData.append("id", JSON.stringify(id));
        
        return $http.post(eappService.getSiteUrl().concat("/account/remove_department_store"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.toggleIsNew = function()
    {
        return $http.post(eappService.getSiteUrl().concat("account/toggle_new"), null);
    };
    
    eappService.getUserAccounts = function(query)
    {
        var formData = new FormData();
        formData.append("query", JSON.stringify(query));
        return $http.post(eappService.getSiteUrl().concat("account/get_user_accounts"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.getCompanyAccounts = function(query)
    {
        var formData = new FormData();
        formData.append("query", JSON.stringify(query));
        return $http.post(eappService.getSiteUrl().concat("account/get_company_accounts"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.ToggleAccountState = function(id, is_active)
    {
        var formData = new FormData();
        formData.append("id", id);
        formData.append("is_active", is_active);
        return $http.post(eappService.getSiteUrl().concat("account/toggle_account_state"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    
    eappService.ToggleNEQState = function(id, is_valid)
    {
        var formData = new FormData();
        formData.append("id", id);
        formData.append("is_valid", is_valid);
        return $http.post(eappService.getSiteUrl().concat("account/toggle_neq_state"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
    };
    

    
    
    
    eappService.deleteStoreProductImage = function(id)
    {
        var formData = new FormData();
        formData.append("id", id);
        
        return $http.post(
                eappService.getSiteUrl().concat("/eapp/delete_store_product_image"), 
                formData, 
                { 
                    transformRequest: angular.identity, 
                    headers: {'Content-Type': undefined}}
                );  
    };
    
    eappService.sendActivationEmail = function()
    {
        return $http.post(eappService.getSiteUrl().concat("account/send_activation_email"), null);
    };
    
    eappService.createConfirmDialog = function(ev, contentText) 
    {
        // Appending dialog to document.body to cover sidenav in docs app
        var confirm = $mdDialog.confirm()
              .title('Êtes-vous sûr?')
              .textContent(contentText)
              .ariaLabel('Êtes-vous sûr?')
              .targetEvent(ev)
              .ok('Oui')
              .cancel('Non');
      
        return confirm;
    };
    
    eappService.showConfirmDialog = function(ev, contentText) 
    {
        // Appending dialog to document.body to cover sidenav in docs app
        var confirm = $mdDialog.confirm()
              .title('Êtes-vous sûr?')
              .textContent(contentText)
              .ariaLabel('Êtes-vous sûr?')
              .targetEvent(ev)
              .ok('Oui')
              .cancel('Non');
      
        return $mdDialog.show(confirm);
    };

    return eappService;
});
