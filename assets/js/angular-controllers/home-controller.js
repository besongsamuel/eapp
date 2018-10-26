/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("HomeController", ["appService", "$scope", "eapp", function(appService, $scope, eapp) 
{
    var ctrl = this;
    
    angular.element(document).ready(function()
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
    });
    
    ctrl.selectCategory = function(category)
    {
        appService.selectCategory(JSON.parse(category));
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

