/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("HomeController", ["appService", "$scope", "eapp", "profileData", function(appService, $scope, eapp, profileData) 
{
    var ctrl = this;
    
    Promise.all([appService.ready, profileData.ready]).then(function()
    {
        ctrl.getProducts();
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
    
    ctrl.getProducts = function()
    {
        eapp.getHomeProducts().then(function(response)
        {
            $scope.ready = false;
            
            var allCategoryProducts = response.data;
            
            $scope.categoryProducts = allCategoryProducts.slice(0, 3);
            
            $scope.categoryProducts2 = allCategoryProducts.slice(3, allCategoryProducts.length);
            
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
                
                $scope.ready = true;
                
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
    
}]);

