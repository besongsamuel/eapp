/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


// Component to Select from user grocery list
angular.module('eappApp').component("addToList", 
{
    template : "<a style='text-align : center;' href ng-click='$ctrl.selectListItem($event)'>{{$ctrl.caption}}</a>",
    controller : function($mdDialog, $scope, appService, eapp)
    {
        var ctrl = this;
        
        ctrl.$onInit = function()
        {
            $scope.loggedUser = appService.loggedUser;
            $scope.product = ctrl.product;
        };
        
        ctrl.selectListItem = function(ev)
        {
            var scrollTop = $(document).scrollTop();
            
            $mdDialog.show({
                controller: DialogController,
                templateUrl: 'templates/components/selectUserGroceryLists.html',
                parent: angular.element(document.body),
                targetEvent: ev,
                locals : 
                {
                    product : $scope.product
                },
                clickOutsideToClose:true,
                fullscreen: false
            })
            .then(function(answer) 
            {
                $(document).scrollTop(scrollTop);
            }, function() 
            {
                $(document).scrollTop(scrollTop);
            });
        };
        
        function DialogController($scope, $mdDialog, product, eapp) 
        {
            eapp.getUserGroceryLists().then(function(response)
            {
                $scope.grocery_lists = response.data.grocery_lists;
                
                 $scope.refresh();
            });
            
            
            $scope.refresh = function()
            {
              for(var i in $scope.grocery_lists)
                {
                    $scope.grocery_lists[i].selected = false;

                    for(var j in $scope.grocery_lists[i].products)
                    {
                        if(parseInt($scope.grocery_lists[i].products[j].id) === parseInt(product.id))
                        {
                            $scope.grocery_lists[i].selected = true;
                            continue;
                        }
                    }
                }  
            };
            
            
            $scope.product = product;
            
            $scope.creatingNew = false;
            
            $scope.name = "Nouvelle liste";
            
            $scope.hide = function() {
              $mdDialog.hide();
            };

            $scope.cancel = function() {
              $mdDialog.cancel();
            };

            $scope.answer = function(answer) {
              $mdDialog.hide(answer);
            };
            
            $scope.createList = function()
            {
                var createNewListPromise = eapp.createNewList($scope.name);
                
                createNewListPromise.then(function(response)
                {
                    if(response.data.success)
                    {
                        var newList = response.data.data;
                        newList.selected = false;
                        $scope.grocery_lists.push(newList);
                        $scope.successMessage = response.data.message;
                    }
                    else
                    {
                        $scope.errorMessage = response.data.message;
                    }
                    
                    $scope.creatingNew = false;
                });
            };
            
            $scope.addToList = function(item)
            {
                $scope.successMessage = null;
                $scope.errorMessage =  null;
                if(item.selected)
                {
                    eapp.addProductToList($scope.product, item.id).then(function(response)
                    {
                        $scope.grocery_lists = response.data.grocery_lists;
                        $scope.refresh();
                    });
                }
                else
                {
                    eapp.removeProductFromList($scope.product, item.id).then(function(response)
                    {
                        $scope.grocery_lists = response.data.grocery_lists;
                        $scope.refresh();
                    });
                }
            };
        }
    },
    bindings : 
    {
        caption : '@',
        product : '<'
    }
});
