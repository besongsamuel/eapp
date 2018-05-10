/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('eappApp').factory("$company", function($rootScope, $http)
{
    var service = 
    {
        getStoreProducts : function(query, success)
        {
            var formData = new FormData();
        
            formData.append("query", JSON.stringify(query));
        
            return $http.post(
                $rootScope.site_url.concat("/company/get_store_products"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
            
        },
        register : function(account, profile, company, logo)
        {
            var formData = new FormData();
            formData.append("account", JSON.stringify(account));
            formData.append("profile", JSON.stringify(profile));
            formData.append("company", JSON.stringify(company));
            if(logo)
            {
                formData.append('image', logo);
            }

            return $http.post($rootScope.site_url.concat("/company/register"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
        },
        addStoreProduct : function(storeProduct, image, success)
        {
            var toSave = JSON.stringify(storeProduct);
            
            var formData = new FormData();
        
            formData.append("store_product", toSave);
            
            formData.append("image", image);
            
            return $http.post(
                $rootScope.site_url.concat("/company/add_store_product"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
            
            
        },
        addProductBrand : function(name, success)
        {            
            var formData = new FormData();
        
            formData.append("name", name);
                        
            return $http.post(
                $rootScope.site_url.concat("/company/add_product_brand"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        addUnit : function(name, success)
        {
            var formData = new FormData();
        
            formData.append("name", name);
                        
            return $http.post(
                $rootScope.site_url.concat("/company/add_unit"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        batchDeleteStoreProducts : function(storeProducts, success)
        {
            var formData = new FormData();
        
            formData.append("store_products", JSON.stringify(storeProducts));
                        
            return $http.post(
                $rootScope.site_url.concat("/company/batch_delete_store_products"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        uploadProducts : function(file, success, replace)
        {
            var formData = new FormData();
        
            formData.append("products", file);
            formData.append("replace", JSON.stringify(replace))
                        
            return $http.post(
                $rootScope.site_url.concat("/company/upload_products"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        changeLogo : function(image, imageName,  success)
        {
            var formData = new FormData();
        
            formData.append("image", image);
            
            formData.append("image_name", imageName);
                        
            return $http.post(
                $rootScope.site_url.concat("/company/change_logo"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        editCompany : function(company, success)
        {
            var formData = new FormData();
        
            formData.append("company", JSON.stringify(company));
                        
            return $http.post(
                $rootScope.site_url.concat("/company/edit_company"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        getStats : function(order, limit, period, success)
        {
            var formData = new FormData();
        
            formData.append("order", order);
            formData.append("limit", limit);
            formData.append("period", period);
                        
            return $http.post(
                $rootScope.site_url.concat("/account/get_stats"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        selectSubscription : function(selectedSubscription, success)
        {
            var formData = new FormData();
        
            formData.append("subscription", selectedSubscription);
                        
            return $http.post(
                $rootScope.site_url.concat("/company/select_subscription"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        submitPayment : function(nonce, subscription, success)
        {
            var formData = new FormData();
        
            formData.append("nonce", JSON.stringify(nonce));
            
            formData.append("subscription", subscription);
                        
            return $http.post(
                $rootScope.site_url.concat("/company/submit_payment"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        getClientToken : function(success)
        {
            $http.post($rootScope.site_url.concat("/company/get_client_token"), null).then(success, onError);
        }
    };
    
    return service;
});

function onError(response)
{
    console.log(response);
}
