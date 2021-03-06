
angular.module('eappApp').controller('CompanyAccountController', function($scope, $company, $timeout, appService)
{
    
    var ctrl = this;
    
    $scope.storeLogo = null;
    
    $scope.Init = function()
    {
        $scope.company = 
        {
            id : appService.loggedUser.company.id,
            name : appService.loggedUser.company.name,
            address : appService.loggedUser.company.address,
            phone : appService.loggedUser.company.phone,
            email : appService.loggedUser.company.email,
            website : appService.loggedUser.company.website,
            description : appService.loggedUser.company.description
        };
                
        $scope.image_name = appService.loggedUser.company.chain.image;
        
        // Get the web path of the store image if it is set. 
        if(!angular.isNullOrUndefined(appService.loggedUser.company.chain.image) && appService.loggedUser.company.chain.image != '')
        {
            $scope.storeLogo = appService.baseUrl.concat("/assets/img/stores/").concat(appService.loggedUser.company.chain.image);   
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
        if($scope.companyForm.$valid)
        {
            $company.editCompany($scope.company, success);
        }
        else
        {
            var invaldControl = angular.element('.ng-invalid').last();
            
            invaldControl.focus();
        }
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
        
        
   appService.ready.then(function()
   {
       $scope.Init();
   }); 
    
});





