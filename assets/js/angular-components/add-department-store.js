// Component to Select from user grocery list
angular.module('eappApp').component("addDepartmentStore", 
{
    templateUrl : "templates/components/addDepartmentStore.html",
    controller : function($scope, eapp, $mdDialog, $rootScope)
    {
        var ctrl = this;
        
        ctrl.$onInit = function()
        {
            $scope.addNewDepartmentStore = true;
            
            $scope.departmentStores = ctrl.departmentStores;
            
            if(angular.isNullOrUndefined($scope.departmentStores))
            {
                $scope.departmentStores = [];
            }
        };
        
        $scope.removeDepartmentStore = function(id, $event)
        {
            var confirmDialog = $rootScope.createConfirmDIalog ($event, "Êtes-vous sûr de vouloir supprimer cette sucursalle de votre liste??");
            
            $mdDialog.show(confirmDialog).then(function()
            {
             
                eapp.removeDepartmentStore(id).then(function(response)
                {
                    if(response.data)
                    {
                        var index = $scope.departmentStores.map(function(e){ return e.id; }).indexOf(id);

                        if(index > -1)
                        {
                            $scope.departmentStores.splice(index, 1);
                        }
                    }
                });
                
            }, function()
            {
                
            });
            
            
        };
        
        $scope.addStoreBranch = function (event) 
        {
            $mdDialog.show(
            {
                clickOutsideToClose: false,
                controller: function($scope, eapp)
                {
                    ctrl = this;
                    
                    ctrl.departmentStores = [];

                    $scope.departmentStore = 
                    {
                        country : 'Canada',
                        state : 'Quebec',
                        name : 'Nouvelle Succursale'
                    };
                    
                    $scope.addStoreBranch = function(addMore)
                    {
                        if($scope.departmentStoreForm.$valid)
                        {
                            eapp.addDepartmentStore($scope.departmentStore).then(function(response)
                            {
                                if(response.data.success)
                                {
                                    $scope.departmentStore.id = response.data.id;
                                    
                                    let storeAdded = $scope.departmentStore;
                                    
                                    ctrl.departmentStores.push(storeAdded);
                                    
                                    if(!addMore)
                                    {
                                        $mdDialog.hide(ctrl.departmentStores);
                                    }
                                    
                                    
                                    // Reset Department Store Object
                                    $scope.departmentStore = 
                                    {
                                        country : 'Canada',
                                        state : 'Quebec',
                                        name : 'Nouvelle Succursale'
                                    };

                                    $scope.departmentStoreForm.$setPristine();
                                    
                                    
                                }
                            });

                        }
                    };
                    
                    $scope.close = function()
                    {
                        $mdDialog.hide(ctrl.departmentStores);
                    };
                },
                controllerAs: 'ctrl',
                focusOnOpen: true,
                targetEvent: event,
                templateUrl: 'templates/dialogs/add-department-store-dialog.html'
                
            })
            .then(function(addedDepartmentStores)
            {
                addedDepartmentStores.forEach(function(store)
                {
                    $scope.departmentStores.push(store);
                });
                
            }).catch(function(err)
            {
                console.log(err);
            });
        };
    },
    bindings : 
    {
        departmentStores : '<',
        onNewStoreAdded : '&'
    }
});