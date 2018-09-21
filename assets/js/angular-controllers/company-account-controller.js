
angular.module('eappApp').controller('CompanyAccountController', function($scope, $company, $timeout)
{
    
    var ctrl = this;
    
    $scope.storeLogo = null;
    
    $scope.Init = function()
    {
        $scope.company = 
        {
            id : $scope.loggedUser.company.id,
            name : $scope.loggedUser.company.name,
            neq : $scope.loggedUser.company.neq
        };
                
        $scope.image_name = $scope.loggedUser.company.chain.image;
        
        // Get the web path of the store image if it is set. 
        if(!angular.isNullOrUndefined($scope.loggedUser.company.chain.image) && $scope.loggedUser.company.chain.image != '')
        {
            $scope.storeLogo = $scope.base_url.concat("/assets/img/stores/").concat($scope.loggedUser.company.chain.image);   
        }
        else
        {
            $scope.storeLogo = null;
        }
        
        
        if(sessionStorage.getItem("subscriptionChanged"))
        {
            if(JSON.parse(sessionStorage.getItem("subscriptionChanged")))
            {
                // Set message
                $scope.successMessage = "L'abonnement à votre compte a été modifié avec succès.";
            }
            
            sessionStorage.removeItem("subscriptionChanged");
        }
    };
    
    $scope.imageChanged= function(image)
    {
        $scope.storeLogo = image;
        
        $company.changeLogo($scope.storeLogo, $scope.storeLogo.name, function(){ ctrl.scrollToTop(); });
    };
    
    ctrl.scrollToTop = function()
    {
        document.body.scrollTop = document.documentElement.scrollTop = 0;
    };
    
    $scope.onFileRemoved = function()
    {
        $scope.image_name = '';
        $company.changeLogo(null, $scope.image_name, function(){ });
    };
    
    $scope.editCompany = function()
    {
        $company.editCompany($scope.company, success);
    };
    
    function success()
    {
        $scope.successMessage = "Les informations sur votre entreprise ont été enregistrées avec succès ";
        ctrl.scrollToTop();
        
        $scope.timeOutPromise = $timeout(cancelTimeout, 5000);
    }
    
    function cancelTimeout()
    {
        $scope.successMessage = null;
        
        $timeout.cancel($scope.timeOutPromise);
    }
        
    angular.element(document).ready(function()
    {
        $scope.Init();
    });
});





