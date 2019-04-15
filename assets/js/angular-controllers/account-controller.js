/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


angular.module('eappApp').controller('AccountController', ["$scope", "$http", "$mdToast", "eapp", "$company", "$rootScope", "appService", "$timeout", "$location", function($scope, $http, $mdToast, eapp, $company, $rootScope, appService, $timeout, $location) 
{   
    "use strict";
    
    var ctrl = this;
    
    $scope.address = 
    {
        name : ''
    };
    
    $scope.options = {
        types: ['(cities)'],
        componentRestrictions: { country: 'CA' }
      };
    
    $scope.false = false;
    
    $scope.true = true;
        
    $scope.enterVerificationNumber = true;
    
    $scope.message = null;
    
    $scope.confirm_password = null;
    
    $scope.userPhoneVerified = false;
    
    $scope.$watch('loggedUserClone', function(oldValue, newVale)
    {
        $scope.userPhoneVerified = !angular.isNullOrUndefined($scope.loggedUserClone) && parseInt($scope.loggedUserClone.phone_verified) === 1;
    });
    
    ctrl.statusChangeCallback = function(response)
    {
        if(response.status == 'not_authorized')
        {
            
        }
    };
    
    $scope.Init = function()
    {
        if(!appService.isUserLogged && appService.redirectToLogin)
        {
            window.location.href = appService.siteUrl.concat("/account/login");
        }

        $scope.load_icons();
        
        var securityQuestionsPromise = eapp.getSecurityQuestions();
        
        securityQuestionsPromise.then(function(response)
        {
            $scope.securityQuestions = response.data;
        });
        
        if($scope.isUserLogged && appService.loggedUser.company && appService.loggedUser.company.is_new == 1)
        {
            $scope.isNewAccount = true;
        }
        
        // Create a copy of the logged user
        $scope.loggedUserClone = angular.copy(appService.loggedUser);
        
    };
    
    
    
   $rootScope.showSimpleToast = function(message, parent_id) {
        $mdToast.show(
          $mdToast.simple()
            .textContent(message)
            .position("left bottom")
            .hideDelay(3000)
            .parent(document.getElementById(parent_id))
        );
    };
    
    $scope.load_icons = function()
    {
        $scope.icons = 
        {
            person :  appService.baseUrl + "/assets/icons/ic_person_white_24px.svg",
            flag :  appService.baseUrl + "/assets/icons/ic_flag_white_24px.svg",
            place :  appService.baseUrl + "/assets/icons/ic_place_white_24px.svg",
            phone :  appService.baseUrl + "/assets/icons/ic_local_phone_white_24px.svg",
            email :  appService.baseUrl + "/assets/icons/ic_email_white_24px.svg",
            lock :  appService.baseUrl + "/assets/icons/ic_lock_white_24px.svg",
            favorite :  appService.baseUrl + "/assets/icons/ic_favorite_white_24px.svg",
            delete :  appService.baseUrl + "/assets/icons/ic_delete_white_24px.svg",
            add :  appService.baseUrl + "/assets/icons/ic_add_circle_white_24px.svg",
            search :  appService.baseUrl + "/assets/icons/ic_search_black_24px.svg",
            add_img : appService.baseUrl + "/assets/img/add_image.png"
        };
    };

    $scope.user = 
    {
        email : '',
        password : '',
        security_question_id : 1,
        security_question_answer : '',
        firstname : '',
        lastname : '',
        country : 'Canada',
        state : 'Quebec',
        city : '',
        address : '',
        postcode : '',
        phone1 : '',
        phone2 : '',
	rememberme : false

    };
    
    $scope.profile = 
    {
        country : 'Canada',
        state : 'Quebec'
    };
    
    $scope.account = 
    {
        security_question_id : 1
    };
    
    $scope.storeLogo = null;
	
    $scope.submit_favorite_stores = function()
    {
        $scope.listChangedSuccess = false;
        if($scope.selected_retailers.length < $scope.max_stores)
        {
            $scope.showSimpleToast("Vous devez sélectionner au moins "+$scope.max_stores+" magasins.", "select-store-box");
        }
        else
        {
            $scope.registering_user = true;
            var formData = new FormData();
            formData.append("selected_retailers", JSON.stringify($scope.selected_retailers));
            formData.append("email", $scope.registered_email);
            // Send request to server to get optimized list 	
            $http.post( appService.siteUrl.concat("/account/submit_favorite_stores"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                if(response.data.success)
                {
                    // redirect to login. 
                    if(!$scope.isUserLogged)
                    {
                        window.sessionStorage.setItem("accountCreated", "Votre compte a été créé avec succès.");
                        window.location =  appService.siteUrl.concat("/account/login");
                    }
                    else
                    {
                        $scope.listChangedSuccess = true;
                        $scope.listChangedSuccessMessage = "Votre liste de magasins a été modifiée.";
                    }
                    
                }
                else
                {
                    $scope.showSimpleToast("une erreur inattendue est apparue. Veuillez réessayer plus tard.", "select-store-box");
                }
                
                $scope.registering_user = false;
            });
        }
    };
     
    $scope.register = function()
    {
        $scope.message = null;
        
        if($scope.signupForm.$valid)
        {
            var registrationPromise = eapp.registerUser($scope.user);

            registrationPromise.then
            (
                function(result)
                {

                    if(result.data.success)
                    {
                        appService.loggedUser = result.data.user;
                        
                        window.sessionStorage.setItem('newAccount', 'true');
                        
                        window.location =  appService.siteUrl.concat("/account/account_created");
                        
                    }

                    if(!result.data.success)
                    {
                        $scope.message = result.data.message;
                        
                        $("html").animate({ scrollTop: 0 }, "slow");
                    }
                },
                function(error)
                {
                    if(!error.data.success)
                    {
                        $scope.message = error.data.message;
                    }
                }
            );
        }
            
    };
    
    $scope.imageChanged= function(image)
    {
        $scope.storeLogo = image;
    };
    
    $scope.creatingAccount = false;
    
    $scope.registerCompany = function()
    {
        $scope.message = null;
        
        if($scope.signupForm.$valid)
        {
            $scope.creatingAccount = true;
            
            var registrationPromise = $company.register($scope.account, $scope.profile, $scope.company, $scope.storeLogo);

            registrationPromise.then
            (
                function(result)
                {

                    if(result.data.success)
                    {
                        appService.loggedUser = result.data.user;
                                                
                        $scope.creatingAccount = false;
                        
                        // redirect to subscription selection page
                        window.location =  appService.siteUrl.concat("/account/select_subscription");
                        
                    }

                    if(!result.data.success)
                    {
                        $scope.message = result.data.message;
                        
                        $scope.creatingAccount = false;
                        
                        $("html").animate({ scrollTop: 0 }, "slow");
                    }
                },
                function(error)
                {
                    if(!error.data.success)
                    {
                        $scope.timeoutPromise = $timeout(cancelTimeout, 5000);
                        $scope.message = error.data.message;
                    }
                }
            );
        }
    };
    
    function cancelTimeout()
    {
        $scope.message = null;
        $scope.saveProfileSucess = false;
        $timeout.cancel($scope.timeoutPromise);
    }
    
    $scope.finishCompanyRegistration = function()
    {
        eapp.toggleIsNew().then(function(response)
        {
            if(response.data)
            {
                // Send the user to his account
                window.location.href = appService.siteUrl.concat("/account");
            }
        });
    };
    
    $scope.login = function()
    {
        if($scope.loginForm.$valid)
        {
            var formData = new FormData();
            formData.append("email", $scope.user.email);
            formData.append("password", $scope.user.password);
            formData.append("rememberme", $scope.user.rememberme ? 1 : 0);
            // Send request to server to get optimized list 	
            $http.post( appService.siteUrl.concat("/account/perform_login"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                if(response.data.success)
                {
                    // redirect to home page. 
                    window.location =  appService.siteUrl.concat("/" + response.data.redirect);
                }
                else
                {
                    $scope.message = response.data.message;
                }
            });
        }
    };
    
    $scope.updateProfile = function()
    {
        if(!$scope.userInfoForm.$valid)
        {
            return;
        }
        
        $scope.saveProfileError = false;
        $scope.saveProfileSucess = false;
        
        var updatePromise = eapp.updateUserProfile($scope.loggedUserClone);
        
        updatePromise.then
        (
            function(response)
            {
                if(response.data.success)
                {
                    $scope.saveProfileSucess = true;
                    appService.loggedUser = response.data.user;
                    $scope.saveProfileSuccessMessage = "Les informations de votre profil ont été modifiées.";
                    document.getElementById('saveProfileSucess').scrollIntoView();
                }
                else
                {
                    $scope.saveProfileError = true;
                    $scope.saveProfileErrorMessage = "Une erreur de serveur est survenue. Veuillez réessayer plus tard.";
                    document.getElementById('saveProfileError').scrollIntoView();
                }
                
            }
        );
    };
    
    $scope.changePassword = function()
    {
        if(!$scope.userSecurityForm.$valid)
        {
            return;
        }
        
        $scope.changePasswordError = false;
        $scope.changePasswordSuccess = false;
        var formData = new FormData();
        formData.append("old_password", $scope.old_password);
        formData.append("password", $scope.password);
        
        $http.post( appService.siteUrl.concat("/account/change_password"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            if(response.data.success)
            {
                $scope.changePasswordSuccess = true;
                $scope.changePasswordSuccessMessage = response.data.message;
            }
            else
            {
                $scope.changePasswordError = true;
                $scope.changePasswordErrorMessage = response.data.message;
            }
        });
    };
    
    $scope.changeSecurityQuestion = function()
    {
        if(!$scope.securityQuestionForm.$valid)
        {
            return;
        }
        
        $scope.changeSecurityQuestionError = false;
        $scope.changeSecurityQuestionSuccess = false;
        var formData = new FormData();
        formData.append("security_question_answer", appService.loggedUser.security_question_answer);
        formData.append("security_question_id", appService.loggedUser.security_question_id);
        
        $http.post( appService.siteUrl.concat("/account/change_security_qa"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            if(response.data.success)
            {
                $scope.changeSecurityQuestionSuccess = true;
                $scope.changeSecurityQuestionSuccessMessage = response.data.message;
            }
            else
            {
                $scope.changeSecurityQuestionError = true;
                $scope.changeSecurityQuestionErrorMessage = response.data.message;
            }
        });
    };
    
    $scope.sendVerificationCode = function()
    {
        $scope.phoneNumberError = null;

        var isValid = $("#phone").intlTelInput("isValidNumber");

        if(isValid)
        {
            var intlNumber = $("#phone").intlTelInput("getNumber");
            
            var sendVerificationPromise = eapp.sendVerification(intlNumber);
            
            sendVerificationPromise.then(function(response)
            {
                if(response.data)
                {
                    $scope.enterVerificationNumber = false;
                }
            });
        }
        else
        {
            var error = $("#phone").intlTelInput("getValidationError");

            switch(error)
            {
                case intlTelInputUtils.validationError.IS_POSSIBLE:
                    $scope.phoneNumberError = "";
                    break;
                case intlTelInputUtils.validationError.INVALID_COUNTRY_CODE:
                    $scope.phoneNumberError = "Le pays n'est pas valide";
                    break;
                case intlTelInputUtils.validationError.TOO_SHORT:
                    $scope.phoneNumberError = "Le numéro de téléphone entré est trop court";
                    break;
                case intlTelInputUtils.validationError.TOO_LONG:
                    $scope.phoneNumberError = "Le numéro de téléphone entré est trop long";
                    break;
                case intlTelInputUtils.validationError.NOT_A_NUMBER:
                    $scope.phoneNumberError = "Le numéro de téléphone entré n'est pas valide.";
                    break;
                default:
                    $scope.phoneNumberError = "Le numéro de téléphone entré n'est pas valide.";
                        break;
            }
        }


    };
    
    $scope.validateCode = function()
    {
        $scope.validateCodeMessage = null;
        
        var validatePromise = eapp.validateCode($scope.verificationCode);
        
        validatePromise.then(function(response)
        {
            if(response.data.success)
            {
                appService.loggedUser.phone_verified = 1;
                $scope.enterVerificationNumber = true;
                $scope.validateCodeMessage = response.data.message;
            }
            else
            {
                $scope.validateCodeMessage = response.data.message;
            }
        });
        
    };
    
    appService.ready.then(function()
    {
        if($("#phone").length == 1)
        {
            $("#phone").intlTelInput({utilsScript : `${appService.baseUrl}/node_modules/intl-tel-input/build/js/utils.js`});
        }
        
        $scope.Init();
    });
   
}]);

