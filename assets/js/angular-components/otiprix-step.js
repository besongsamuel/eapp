/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('eappApp').component('otiprixStep', {
    
    templateUrl: 'templates/components/otiprixStep.html',
    controller : function($scope)
    {
        var ctrl = this;
        
        ctrl.$onInit = function()
        {
            $scope.displayBorder = ctrl.displayBorder;
            $scope.caption = ctrl.caption;
        };
    },
    bindings: 
    {
        index: '@',
        image : '@',
        caption: '<',
        displayBorder: '<'
    }
});


