/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


angular.module('eappApp').controller('ActivateAccountController', function($scope,appService, eapp) 
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
    
    appService.ready.then(function()
    {
        $scope.accountActivated = !appService.isUserLogged || (appService.isUserLogged && parseInt(appService.loggedUser.is_active) === 1);
    });
   
});

