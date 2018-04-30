/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("SubscriptonController", ["$rootScope", "$scope", function($rootScope, $scope) 
{
    
    var ctrl = this;
    
    ctrl.selectSubscription = function(type)
    {
        if(type == 3)
        {
            var href = "https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=67YAR9BVCRBHN";
            
            var win = window.open(href, '_blank');
            win.focus();
        }
    };
    
    angular.element(document).ready(function()
    {
        
        (function()
        {
            
        })();
        
    });
    
}]);
