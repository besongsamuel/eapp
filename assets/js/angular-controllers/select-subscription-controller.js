/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("SubscriptonController", ["$scope", "$company", function($scope, $company) 
{
    
    var ctrl = this;
    
    ctrl.selectSubscription = function(type)
    {
        
        $company.selectSubscription(type, function(response)
        {   
            if(response.data.success)
            {
                
                // Add message
                sessionStorage.setItem("subscriptionChanged", JSON.stringify(true));
                
                // Redirect to the company account page
                window.location =  $scope.site_url.concat("/account/index/2");
            }
        });
        
    };
    
    angular.element(document).ready(function()
    {
        
        (function()
        {
            
        })();
        
    });
    
}]);
