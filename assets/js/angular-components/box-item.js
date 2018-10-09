/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


// Component to Select from user grocery list
angular.module('eappApp').component("boxItem", 
{
    templateUrl : "templates/components/boxItem.html",
    controller : function()
    {
        var ctrl = this;
        
        ctrl.itemClicked = function(ev)
        {
            ctrl.onItemClicked({event : ev, item : ctrl.item});
        };
        
    },
    bindings : 
    {
        onItemClicked : '&',
        item : '<'
    }
});
