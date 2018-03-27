/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('eappApp').factory('Form', [ '$http', 'eapp', function($http, eapp) 
{
    this.postForm = function (formData, url, redirect_url, ev) 
    {       
        $http({
            url: url,
            method: 'POST',
            data: formData,
            //assign content-type as undefined, the browser
            //will assign the correct boundary for us
            headers: { 'Content-Type': undefined},
            //prevents serializing payload.  don't do it.
            transformRequest: angular.identity
        }).
        then(
        function successCallback(response) 
        {
            
            if(response.data.success)
            {
                if(redirect_url != null)
                {
                    window.location.href = redirect_url;
                }
                
                eapp.showAlert(ev, "Success", response.data.message);
            }
            else
            {
                var message = response.data.message + "\n\n";
                
                for(var x in response.data.errors)
                {
                    message += "\n" + response.data.errors[x];
                }
                
                message += '\n';
                
                eapp.showAlert(ev, "Error", message);
            }
            
        }, 
        function errorCallback(response) 
        {
            eapp.showAlert(ev, "Error", "An unknown error occured. ");
        });
    };
    
    return this;
}]);


