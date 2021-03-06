/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('eappApp').factory("$company", function(appService, $http)
{
    var service = 
    {
        getStoreProducts : function(query, success)
        {
            var formData = new FormData();
        
            formData.append("query", JSON.stringify(query));
        
            return $http.post(
                appService.siteUrl.concat("/company/get_store_products"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
            
        },
        register : function(account, profile, company, logo)
        {
            var formData = new FormData();
            formData.append("account[email]", account.email);
            formData.append("account[password]", account.password);
            formData.append("account[security_question_answer]", account.security_question_answer);
            formData.append("account[security_question_id]", account.security_question_id);
            formData.append("profile", JSON.stringify(profile));
            formData.append("company", JSON.stringify(company));
            if(logo)
            {
                formData.append('image', logo);
            }

            return $http.post(appService.siteUrl.concat("/company/register"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
        },
        addStoreProduct : function(storeProduct, image, success)
        {
            var toSave = JSON.stringify(storeProduct);
            
            var formData = new FormData();
        
            formData.append("store_product", toSave);
            
            formData.append("image", image);
            
            return $http.post(
                appService.siteUrl.concat("/company/add_store_product"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
            
            
        },
        addProductBrand : function(name, success)
        {            
            var formData = new FormData();
        
            formData.append("name", name);
                        
            return $http.post(
                appService.siteUrl.concat("/company/add_product_brand"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        addUnit : function(name, success)
        {
            var formData = new FormData();
        
            formData.append("name", name);
                        
            return $http.post(
                appService.siteUrl.concat("/company/add_unit"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        batchDeleteStoreProducts : function(storeProducts, success)
        {
            var formData = new FormData();
        
            formData.append("store_products", JSON.stringify(storeProducts));
                        
            return $http.post(
                appService.siteUrl.concat("/company/batch_delete_store_products"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        uploadProducts : function(file, success, replace)
        {
            var formData = new FormData();
        
            formData.append("products", file);
            formData.append("replace", JSON.stringify(replace))
                        
            return $http.post(
                appService.siteUrl.concat("/company/upload_products"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        changeLogo : function(image, imageName,  success)
        {
            var formData = new FormData();
        
            formData.append("image", image);
            
            formData.append("image_name", imageName);
                        
            return $http.post(
                appService.siteUrl.concat("/company/change_logo"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        editCompany : function(company, success)
        {
            var formData = new FormData();
        
            formData.append("company", JSON.stringify(company));
                        
            return $http.post(
                appService.siteUrl.concat("/company/edit_company"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        getStats : function(order, limit, from, to, success)
        {
            var formData = new FormData();
        
            formData.append("order", order);
            formData.append("limit", limit);
            formData.append("from_date", from);
            formData.append("to_date", to);
                        
            return $http.post(
                appService.siteUrl.concat("/account/get_stats"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        selectSubscription : function(selectedSubscription, success)
        {
            var formData = new FormData();
        
            formData.append("subscription", selectedSubscription);
                        
            return $http.post(
                appService.siteUrl.concat("/company/select_subscription"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        submitPayment : function(nonce, subscription, success)
        {
            var formData = new FormData();
        
            formData.append("nonce", JSON.stringify(nonce));
            
            formData.append("subscription", subscription);
                        
            return $http.post(
                appService.siteUrl.concat("/company/submit_payment"), 
                formData, 
                { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(success, onError);
        },
        getClientToken : function(success)
        {
            $http.post(appService.siteUrl.concat("/company/get_client_token"), null).then(success, onError);
        },
        get : function(id, retailer_id)
        {
            return $http.get(appService.siteUrl.concat("/company/get?id=" + id + "&retailer_id=" + retailer_id));
        }
    };
    
    return service;
});

function onError(response)
{
    console.log(response);
}
