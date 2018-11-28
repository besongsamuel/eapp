/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("HomeController", function(appService, $scope, eapp, profileData, $mdDialog) 
{
    var ctrl = this;
    
    $scope.loadingProducts = true;
    
    Promise.all([appService.ready, profileData.ready]).then(function()
    {
        ctrl.getProducts();
        ctrl.howItWorks();
    });
    
    ctrl.selectCategory = function(id, name)
    {
        var category = 
        {
            id : id,
            name : name
        };
        appService.selectCategory(category);
    };
    
    ctrl.howItWorks = function(ev)
    {        
        if(angular.isNullOrUndefined(window.localStorage.getItem("firstLaunch")))
        {
            $mdDialog.show({
                controller: function($scope)
                {
                    $scope.logo = appService.baseUrl.concat("/assets/img/logo.png");

                    $scope.close = function()
                    {
                        $mdDialog.hide();
                    };
                },
                templateUrl: 'templates/dialogs/howItWorks.html',
                parent: angular.element(document.body),
                targetEvent: ev,
                clickOutsideToClose:false,
                fullscreen: true
            });
            
            window.localStorage.setItem("firstLaunch", true);
        }
    };
    
    ctrl.getProducts = function()
    {
        eapp.getHomeProducts().then(function(response)
        {
            
            var allCategoryProducts = response.data;
            
            $scope.categoryProducts = allCategoryProducts.slice(0, 3);
            
            $scope.categoryProducts2 = allCategoryProducts.slice(3, allCategoryProducts.length);
            
            $scope.loadingProducts = false;
            
            setTimeout(function()
            {
                 $('.product-carousel').owlCarousel({
                    loop:true,
                    nav:false,
                    autoplay:false,
                    autoplayTimeout: 1000,
                    autoplayHoverPause:true,
                    margin:0,
                    responsiveClass:true,
                    navText : ['Précédent', 'Suivant'],

                    responsive:{
                        0:{
                            items:1
                        },
                        600:{
                            items:2
                        },
                        1000:{
                            items:4
                        }
                    }
                });
                
                
                
            }, 100);
            
            
        });
    };
    
    ctrl.getHomeCategories = function()
    {
        var categoriesPromise = eapp.getCategories(5, 8);
        
        $scope.loading = true;
        
        categoriesPromise.then(function(response)
        {
            $scope.homePageCategories = response.data;
            
            $scope.loading = false;
        });
    };
    
    ctrl.gotoShop = function()
    {
        appService.gotoShop();
    };
    
});

