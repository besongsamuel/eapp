/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function() {
  $('a[href*=#]').on('click', function(e) {
    e.preventDefault();
    var element = $($(this).attr('href'));
    $('html, body').animate(
            { 
                scrollTop: element.offset().top - 100}, 
                500, 
                'linear');
  });
});

angular.module('eappApp').component('otiprixStep', {
    
    templateUrl: 'otiprixStep.html',
    controller : function($scope)
    {
        var ctrl = this;
        
        ctrl.$onInit = function()
        {
            $scope.displayBorder = ctrl.displayBorder;
        };
    },
    bindings: 
    {
        index: '@',
        image : '@',
        caption: '@',
        displayBorder: '<'
    }
});

angular.module("eappApp").controller("HomeController", ["$rootScope", "$scope", "eapp", function($rootScope, $scope, eapp) 
{
    
    angular.element(document).ready(function()
    {
        $rootScope.isHome = true;
    
        $rootScope.hideSearchArea = true;

        $scope.yes = true;
        
        $scope.no = false;
        
        var getLatestProductsPromise = eapp.getLatestProducts();

        getLatestProductsPromise.then(function(response)
        {
            var array = $.map(response.data, function(value, index) {
                return [value];
            });

            $scope.latestProducts = array;
        });
    });
    
}]);

