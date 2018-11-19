/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


angular.module('eappApp').component("settingsItem", 
{
    templateUrl : "templates/components/settingsItem.html",
    controller : SettingsItemController,
    bindings : 
    {
        settingsObject : '<',
        name : '@',
        onChange : '&',
        ready : '='
    }
});

function SettingsItemController($scope, appService, profileData)
{
    ctrl = this;
    
    ctrl.getData = function(allData)
    {
        var data = [];
        
        for(var x in allData)
        {
            //if(!allData[x].selected)
            {
                data.push(allData[x]);
            }
        }
        
        return data;
    };
    
    ctrl.$onInit = function()
    {
        ctrl.data = ctrl.getData(ctrl.settingsObject);
        
        $scope.data = ctrl.getData(ctrl.settingsObject);
        
        $scope.moreLessLabel = "Plus";
    };
    
    $scope.getDisplayName = function(name)
    {
        if(name == "0" || name == "1")
        {
            if(name == "0")
            {
                return "Non";
            }
            else
            {
                return "Oui";
            }
        }
        else
        {
            return name;
        }
    };
    
    ctrl.$onChanges = function(newData)
    {
        $scope.data = ctrl.getData(newData.settingsObject.currentValue);
        ctrl.data = ctrl.getData(newData.settingsObject.currentValue);
    };
    
    ctrl.change = function(item)
    {
        if(item.type == 'stores')
        {
            if(item.selected)
            {
                appService.recordRetailerHit(item.id, profileData.instance.optimizationDistance);
            }
        }
        
        ctrl.onChange({item : item});
    };
    
    $scope.showHideDetails = function(event)
    {
        
        if($scope.moreLessLabel == "Plus")
        {
            $scope.moreLessLabel = "Moins";
        }
        else
        {
            $scope.moreLessLabel = "Plus";
        }
    };
}

