/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('eappApp').component("resultFilter", 
{
    templateUrl : "templates/components/resultFilter.html",
    controller : ResultFilterController,
    bindings : 
    {
        resultSet : '<',
        distance : '<',
        onSettingsChanged : '&',
        ready : '=',
        type : '@',
        onRefresh : '&',
        viewConfig : '=',
        onDistanceChanged : '&',
        isUserLogged : '<'
    }
});

function ResultFilterController($scope)
{
    var ctrl = this;
    
    $scope.trueVal = true;
    
    $scope.falseVal = false;
	
    $scope.viewConfig = {};
    
    ctrl.$onInit = function()
    {
        ctrl.settings = ctrl.resultSet;
        $scope.settings = ctrl.resultSet;
        $scope.distance = ctrl.distance;
        $scope.viewConfig = ctrl.viewConfig;
        $scope.isUserLogged = ctrl.isUserLogged;
                
        if(ctrl.type === 'CART')
        {
            $scope.viewConfig.viewAll = !$scope.viewConfig.optimizedCart;
            $scope.showAllResultsCaption = "Voir la liste originale";
            $scope.showOptimizedResultsCaption = "Voir la liste optimisée";
            $scope.isCart = true;
        }
        else
        {
            $scope.showAllResultsCaption = "Voir tout les produits";
            $scope.showOptimizedResultsCaption = "Voir produits optimisé";
        }
    };
    
    $scope.$watch("settings", function()
    {
        // Get selected items
        $scope.selectedItems = {};
        
        for(var x in $scope.settings)
        {
            for(var y in $scope.settings[x].values)
            {
                var type = $scope.settings[x].values[y].type;
                
                if($scope.settings[x].values[y].selected)
                {
                    if(angular.isNullOrUndefined($scope.selectedItems[$scope.settings[x].values[y].type]))
                    {
                        $scope.selectedItems[$scope.settings[x].values[y].type] = { name : $scope.settings[x].setting.caption, items : [] };
                    }
                    
                    $scope.selectedItems[$scope.settings[x].values[y].type].items.push($scope.settings[x].values[y]);
                }
            }
        }
    });
    
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
    
    ctrl.$onChanges = function(changesObj)
    {
        if(!angular.isNullOrUndefined(changesObj.resultSet))
        {
            $scope.settings = changesObj.resultSet.currentValue;
        }
        
        if(!angular.isNullOrUndefined(changesObj.distance))
        {
            $scope.distance = changesObj.distance.currentValue;
        }
        
        if(!angular.isNullOrUndefined(changesObj.isUserLogged))
        {
            $scope.isUserLogged = changesObj.isUserLogged.currentValue;
        }
    };
    
    ctrl.distanceChanged = function()
    {
        ctrl.onDistanceChanged({distance : $scope.distance});
    };
    
    ctrl.change = function(item)
    {
        ctrl.onSettingsChanged({ item : item});
    };
    
    ctrl.refresh = function()
    {
        $scope.viewConfig.optimizedCart = !$scope.viewConfig.viewAll;
        
        ctrl.onRefresh({ viewConfig : $scope.viewConfig });
    };
    
    $scope.removeFromFilter = function(item)
    {
        item.selected = false;
        ctrl.onSettingsChanged({ item : item});
    };
};