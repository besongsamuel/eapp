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

            return $http.post($rootScope.site_url.concat("account/register_company"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
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
        uploadProducts : function(file, success)
        {
            var formData = new FormData();
        
            formData.append("products", file);
                        
            return $http.post(
                $rootScope.site_url.concat("/company/upload_products"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        }
        
        
    };
    
    return service;
});

function onError(response)
{
    console.log(response);
}