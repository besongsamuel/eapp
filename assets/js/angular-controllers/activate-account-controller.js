/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


angular.module('eappApp').controller('ActivateAccountController', ["$scope","$rootScope","eapp", function($scope,$rootScope, eapp) 
{   
    "use strict";
    
    var ctrl = this;
    
    $scope.accountActivated = true;
    
    ctrl.ResendActivationEmail = function(ev)
    {
        eapp.sendActivationEmail().then(function()
        {
            eapp.showAlert(ev, "Mail envoyé avec succès. ", "Veuillez suivre les instructions dans le mail envoyé pour activer votre compte."); 
        });
    };
    
    angular.element(document).ready(function()
    {
        $scope.accountActivated = !$rootScope.isUserLogged || ($rootScope.isUserLogged && parseInt($rootScope.loggedUser.is_active) === 1);
    });
   
}]);

