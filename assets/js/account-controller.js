/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('eappApp').controller('AccountController', ["$scope", "$http", "$mdToast", "$q", "$rootScope", "$mdDialog", function($scope, $http, $mdToast, $q, $rootScope, $mdDialog) 
{
    
    $scope.Init = function()
    {
        $scope.load_icons();
    };
    
    $rootScope.createConfirmDIalog = function(ev, contentText) 
    {
        // Appending dialog to document.body to cover sidenav in docs app
        var confirm = $mdDialog.confirm()
              .title('Êtes-vous sûr?')
              .textContent(contentText)
              .ariaLabel('Êtes-vous sûr?')
              .targetEvent(ev)
              .ok('Oui')
              .cancel('Non');
      
        return confirm;
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
    
    $rootScope.load_icons = function()
    {
        $rootScope.icons = 
        {
            person :  $scope.base_url + "/assets/icons/ic_person_white_24px.svg",
            flag :  $scope.base_url + "/assets/icons/ic_flag_white_24px.svg",
            place :  $scope.base_url + "/assets/icons/ic_place_white_24px.svg",
            phone :  $scope.base_url + "/assets/icons/ic_local_phone_white_24px.svg",
            email :  $scope.base_url + "/assets/icons/ic_email_white_24px.svg",
            lock :  $scope.base_url + "/assets/icons/ic_lock_white_24px.svg",
            favorite :  $scope.base_url + "/assets/icons/ic_favorite_white_24px.svg",
            delete :  $scope.base_url + "/assets/icons/ic_delete_white_24px.svg",
            add :  $scope.base_url + "/assets/icons/ic_add_circle_white_24px.svg",
            search :  $scope.base_url + "/assets/icons/ic_search_black_24px.svg",
        };
    };
    
   $scope.registering_user = false;
   
   $scope.securityQuestions = 
    [
        "Choisissez une question",
        "La destination de votre premier voyage",
        "Quel était l'héros de votre enfance",
        "Le prénom de votre meilleur ami",
        "Le prénom de votre premier amour",
        "Le deuxième prenom de votre plus jeune enfant"
    ];
    
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
	
    $scope.message = null;
    
    $scope.retailers = [];
        
    $scope.selected_retailers = [];
    
    $scope.max_stores = 3;

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
            $http.post( $scope.site_url.concat("/account/submit_favorite_stores"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                if(response.data.success)
                {
                    // redirect to login. 
                    if(!$scope.isUserLogged)
                    {
                        window.sessionStorage.setItem("accountCreated", "Votre compte a été créé avec succès.");
                        window.location =  $scope.site_url.concat("/account/login");
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
            
            // Create form data
            var formData = new FormData();
            formData.append("account[email]", $scope.user.email);
            formData.append("account[password]", $scope.user.password);
            formData.append("account[security_question_id]", $scope.user.security_question_id);
            formData.append("account[security_question_answer]", $scope.user.security_question_answer);

            formData.append("profile[firstname]", $scope.user.firstname);
            formData.append("profile[lastname]", $scope.user.lastname);
            formData.append("profile[country]", $scope.user.country);
            formData.append("profile[state]", $scope.user.state);
            formData.append("profile[city]", $scope.user.city);
            formData.append("profile[address]", $scope.user.address);
            formData.append("profile[postcode]", $scope.user.postcode);

            $http.post(
                 $scope.site_url.concat("/account/registration"),formData,
                {
                        transformRequest: angular.identity,
                        headers: {'Content-Type': undefined}
                }).then
                (
                        function(result)
                        {
                            
                            if(result.data.success)
                            {
                                window.sessionStorage.setItem("registered_email", $scope.user.email);
                                // Redirect to select store page.
                                window.location =  $scope.site_url.concat("/account/select_store");
                            }

                            if(!result.data.success)
                            {
                                $scope.message = result.data.message;
                                document.getElementById("error_message").scrollIntoView();
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
    
    $scope.login = function()
    {
        if($scope.loginForm.$valid)
        {
            var formData = new FormData();
            formData.append("email", $scope.user.email);
            formData.append("password", $scope.user.password);
            formData.append("rememberme", $scope.user.rememberme ? 1 : 0);
            // Send request to server to get optimized list 	
            $http.post( $scope.site_url.concat("/account/perform_login"), 
            formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
            function(response)
            {
                if(response.data.success)
                {
                    // redirect to home page. 
                    window.location =  $scope.site_url.concat("/" + response.data.redirect);
                }
                else
                {
                    $scope.message = response.data.message;
                }
            });
        }
    };
    
    $scope.logout = function()
    {
        // Send request to server to get optimized list 	
        $http.post( $scope.site_url.concat("/account/logout"), 
        null, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            // redirect to home page. 
            window.location =  $scope.site_url.concat("/home");
            
        });
    };
    
    $scope.saveProfile = function()
    {
        if(!$scope.userInfoForm.$valid)
        {
            return;
        }
        
        $scope.saveProfileError = false;
        $scope.saveProfileSucess = false;
        var formData = new FormData();
        formData.append("profile[firstname]", $scope.loggedUser.profile.firstname);
        formData.append("profile[lastname]", $scope.loggedUser.profile.lastname);
        formData.append("profile[country]", $scope.loggedUser.profile.country);
        formData.append("profile[state]", $scope.loggedUser.profile.state);
        formData.append("profile[city]", $scope.loggedUser.profile.city);
        formData.append("profile[address]", $scope.loggedUser.profile.address);
        formData.append("profile[postcode]", $scope.loggedUser.profile.postcode);
        formData.append("profile[phone1]", $scope.loggedUser.profile.phone1);
        formData.append("profile[phone2]", $scope.loggedUser.profile.phone2);
        
        $http.post( $scope.site_url.concat("/account/save_profile"), 
        formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            if(response.data.success)
            {
                $scope.saveProfileSucess = true;
                
                $scope.loggedUser = response.data.user;
                
                $scope.saveProfileSuccessMessage = "Les informations de votre profil ont été modifiées.";
            }
            else
            {
                $scope.saveProfileError = true;
                $scope.saveProfileSuccessError = "Une erreur de serveur est survenue. Veuillez réessayer plus tard.";
            }
            
        });
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
        
        $http.post( $scope.site_url.concat("/account/change_password"), 
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
        formData.append("security_question_answer", $scope.loggedUser.security_question_answer);
        formData.append("security_question_id", $scope.loggedUser.security_question_id);
        
        $http.post( $scope.site_url.concat("/account/change_security_qa"), 
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

            $.ajax({
                type: 'POST',
                url:   $scope.site_url.concat("/account/send_verification"),
                data: { number : intlNumber},
                success: function(response)
                {
                    if(Boolean(response.toString()))
                    {
                        var accountScope = angular.element($("#admin-container")).scope();

                        accountScope.$apply(function()
                        {
                            accountScope.enterVerificationNumber = false;
                        });

                    }

                },
                async:true
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

        $.ajax({
            type: 'POST',
            url:   $scope.site_url.concat("/account/validate_code"),
            data: { code : $scope.verificationCode},
            success: function(response)
            {
                var result = JSON.parse(response);
                
                if(result.success)
                {
                    
                    var accountScope = angular.element($("#admin-container")).scope();
                    accountScope.$apply(function()
                    {
                        $scope.loggedUser.phone_verified = 1;
                        accountScope.enterVerificationNumber = true;
                        accountScope.validateCodeMessage = result.message;
                    });

                }
                else
                {
                    var accountScope = angular.element($("#admin-container")).scope();
                    
                    accountScope.$apply(function()
                    {
                        accountScope.validateCodeMessage = result.message;
                    });
                    
                }

            },
            async:true
        });
        
    };
   
}]);

