/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


// Component to Select from user grocery list
angular.module('eappApp').component("addToList", 
{
    templateUrl : "templates/components/addToList.html",
    controller : function($mdDialog, $scope, appService)
    {
        var ctrl = this;
        
        ctrl.$onInit = function()
        {
            if(ctrl.type == "button")
            {
                ctrl.isButton = true;
            }
            else
            {
                ctrl.isLink = true;
            }
            
            $scope.loggedUser = appService.loggedUser;
            $scope.product = ctrl.product;
            $scope.products = ctrl.products;
        };
        
        ctrl.$onChanges = function(changeObj)
        {
            $scope.products = changeObj.products.currentValue;
            ctrl.products = changeObj.products.currentValue;
        };
        
        ctrl.selectListItem = function(ev)
        {
            
            if(appService.isUserLogged)
            {
                var scrollTop = $(document).scrollTop();
            
                $mdDialog.show({
                    controller: DialogController,
                    templateUrl: 'templates/components/selectUserGroceryLists.html',
                    parent: angular.element(document.body),
                    targetEvent: ev,
                    locals : 
                    {
                        product : $scope.product,
                        products : $scope.products 
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
            }
            else
            {
                $mdDialog.show({
                    controller: RegisterRequestController,
                    templateUrl: 'templates/dialogs/registerRequestDialog.html',
                    parent: angular.element(document.body),
                    targetEvent: ev,
                    clickOutsideToClose:true,
                    fullscreen: false
                });
            }
            
            
        };
        
        function DialogController($scope, $mdDialog, product, products, eapp) 
        {
            $scope.loadingLists = true;
            
            eapp.getUserGroceryLists().then(function(response)
            {
                $scope.loadingLists = false;
                
                $scope.grocery_lists = response.data.grocery_lists;
                
                 $scope.refresh();
            });
            
            
            $scope.refresh = function()
            {
                for(var i in $scope.grocery_lists)
                {
                    if(!angular.isNullOrUndefined(product))
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
                    
                    if(!angular.isNullOrUndefined(products))
                    {
                        
                        
                        for(var j in $scope.grocery_lists[i].products)
                        {
                            $scope.grocery_lists[i].selected = true;
                            
                            for(var x in products)
                            {
                                var product_id = products[x];
                                
                                var index = $scope.grocery_lists[i].products.map(function(e){ return e.id; }).indexOf(product_id);
                                
                                if(index == -1)
                                {
                                    $scope.grocery_lists[i].selected = false;
                                    break;
                                }
                            }
                        }
                    }
                }  
            };
            
            $scope.products = products;
            
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
                    if($scope.product)
                    {
                        eapp.addProductToList($scope.product, item.id).then(function(response)
                        {
                            $scope.grocery_lists = response.data.grocery_lists;
                            $scope.refresh();
                        });
                    }
                    
                    if($scope.products)
                    {
                        eapp.addProductsToList($scope.products, item.id).then(function(response)
                        {
                            $scope.grocery_lists = response.data.grocery_lists;
                            $scope.refresh();
                        });
                    }
                }
                else
                {
                    if($scope.product)
                    {
                        eapp.removeProductFromList($scope.product, item.id).then(function(response)
                        {
                            $scope.grocery_lists = response.data.grocery_lists;
                            $scope.refresh();
                        });
                    }
                    
                    if($scope.products)
                    {
                        eapp.removeProductsFromList($scope.products, item.id).then(function(response)
                        {
                            $scope.grocery_lists = response.data.grocery_lists;
                            $scope.refresh();
                        });
                    }
                }
            };
        }
        
        function RegisterRequestController($scope, appService)
        {
            $scope.gotoLoginPage = function()
            {
                location.href = appService.siteUrl.concat("account/login");
                $mdDialog.hide();
            };
            
            $scope.gotoRegisterPage = function()
            {
                location.href = appService.siteUrl.concat("account/register");
                $mdDialog.hide();
            };
            
        }
    },
    bindings : 
    {
        caption : '@',
        type : '@',
        product : '<',
        products : '<'
    }
});
