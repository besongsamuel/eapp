<!DOCTYPE html>
<link rel="stylesheet" href="<?php echo base_url("assets/css/intlTelInput.css")?>">

<script src="<?php echo base_url("assets/js/intlTelInput.js")?>"></script>
<script src="<?php echo base_url("assets/js/utils.js")?>"></script>
<script>
    
$(document).ready(function()
{
    $("#phone").intlTelInput({utilsScript : "<?php echo base_url("assets/js/utils.js")?>"});
    
    $('#OpenImgUpload').click(function(){ $('#fileUploadButton').trigger('click'); });
});
</script>

<link rel="stylesheet" href="<?php echo base_url("assets/css/account.css")?>">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>

<md-content class="otiprix-section" id="admin-container" ng-cloak>
    
    
    
    <div class="product-big-title-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="product-bit-title text-center">
                        <h2>Mon Compte</h2>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End Page title area -->
    
    <div style="text-align: center;" ng-controller="AccountController">
        <div ng-show="isNewAccount" class="alert alert-success" role="alert">      
            Votre compte a été créé avec succès. Commencez par ajouter des sucursales à votre compte d'entreprise.
        </div>
    </div>
    
    <md-tabs md-dynamic-height md-border-bottom layout-padding md-selected="<?php echo $tabIndex; ?>">
        
        <div class="container">
            
            <md-tab label="Sucursales">
                <div class="row layout-padding" ng-controller="AccountController">
                    <add-department-store department-stores='loggedUser.company.chain.department_stores'></add-department-store>
                </div>
            </md-tab>
            
            <md-tab label="Vos produits">
                
                <div ng-controller="UploadController" style="text-align: center;">
                    <div ng-show="success" class="alert alert-success" role="alert">      
                    Vos produits ont été mis à jour.
                    </div>

                    <div ng-show="incomplete" class="alert alert-danger" role="alert">      
                        Certains produits n'ont pas été mis à jour car votre abonnement ne prend en charge que {{maxItems}} produits. Vous pouvez modifier votre abonnement à partir de "Informations sur l'entreprise".
                    </div>
                    
                    <div ng-show="loggedUser.company.is_valid == 0" class="alert alert-danger" role="alert">      
                        Vous ne pouvez pas encore ajouter de produits à votre compte. Nous validons toujours votre NEQ. Veuillez réessayer plus tard.
                    </div>
                    
                </div>
                
                
                <form method="get" action="<?php echo base_url("/assets/files/Formulaire de Produits.xlsx")?>">
                    <div class="row download-products-form layout-padding">
                        <div class="col-sm-12">
                            <md-button type="submit"  style="margin : auto; display: block;" class="md-raised md-primary">
                               Télécharger fichier des produits Otiprix
                            </md-button>
                        </div>
                        <p class="md-otiprix-text" style="text-align: center">Téléchargez ce document pour faciliter le téléversement de produits sur Otiprix</p>
                    </div>
                </form>
                
                <md-divider></md-divider>
                
                <div class="row layout-padding" ng-controller="UploadController">
                    <div class="col-sm-12">
                        <md-button ng-disabled="loggedUser.company.is_valid == 0" ng-click="selectFile()" style="margin : auto; display: block;" ng-click="uploadStoreProducts()" class="md-primary md-raised">
                           Téléverser vos produits
                        </md-button>
                    </div>
                    <p class="md-otiprix-text" style="text-align: center">Téléversez le fichier de produits</p>
                    <form id="uploadForm" method="post" action="<?php site_url('company/upload_products'); ?>" >
                        <input type="file" name="products" id="fileUploadInput" style="display:none"/> 
                    </form>
                </div>
                
                <md-divider></md-divider>
                
                <company-products ng-if="loggedUser.company.is_valid == 1"></company-products>
                
            </md-tab>
            
            <md-tab label="Informations sur l'entreprise">
                <div ng-controller="CompanyAccountController" >
                    
                    <div id="error_message" class="alert alert-success" ng-show="successMessage">
                        <p>{{successMessage}}</p>
                        <span></span>
                    </div>
                    
                    <form class="companyForm" novalidate ng-submit="editCompany()" class="layout-padding">
                        
                        <!-- COMPANY INFORMATION -->
                        <fieldset style="margin: 10px;">

                            <legend>RENSEIGNEMENTS D'ENTREPRISE</legend>
                            
                            <image-upload image="storeLogo" caption="Ajouter logo" id="store-product-image" on-file-removed="onFileRemoved()" on-file-selected="imageChanged(file)"></image-upload>

                            <!-- NEQ -->
                            <md-input-container class="md-block col-md-12" flex-gt-sm>
                                <label>NEQ</label>
                                <input required name="neq" ng-model="company.neq" />
                                <div ng-messages="companyForm.neq.$error">
                                    <div ng-message="required">Vous devez entrer le NEQ de l'entreprise</div>
                                </div>
                            </md-input-container>

                            <!-- NOM DE L'ENTREPRISE -->
                            <md-input-container class="md-block col-md-12" flex-gt-sm>
                                <label>Nom de l'entreprise</label>
                                <input required name="company_name" ng-model="company.name" />
                                <div ng-messages="companyForm.company_name.$error">
                                    <div ng-message="required">Vous devez entrer au moins un nom pour l'entreprise</div>
                                </div>
                            </md-input-container>
                            
                            <div class="col-sm-12" style="margin-top : 30px; margin-bottom: 30px;">
                                <md-button type="submit" class="pull-right md-primary md-raised">Valider</md-button>
                            </div>


                        </fieldset>
                        
                    </form>
                </div>
            </md-tab>
            
            <md-tab label="Modifier mes renseignements personnels" md-on-select="onTabSelected(2)">

                <div ng-controller="AccountController">
                
                    <!-- Change personal info -->    
                    <div layout-padding>
                        <form name="userInfoForm" novalidate ng-submit="updateProfile()">

                            <md-input-container class="md-block col-md-12 col-sm-12" flex-gt-sm>
                                <label>Email</label>
                                <md-icon style="color: #1abc9c;"><i class="material-icons">email</i></md-icon>
                                <input disabled="true" style="border-left: none; border-top: none; border-right: none;" type="email" name="email" ng-model="loggedUserClone.email" />
                            </md-input-container>
                                <!-- -->
                            <md-input-container class="md-block col-md-6 col-sm-12" flex-gt-sm>
                                <label>Prenom</label>
                                <md-icon style="color: #1abc9c;"><i class="material-icons">person</i></md-icon>
                                <input name="profile[firstname]" ng-model="loggedUserClone.profile.firstname" />
                            </md-input-container>
                                <!-- -->
                            <md-input-container class="md-block col-md-6 col-sm-12" flex-gt-sm>
                                <label>Nom</label>
                                <md-icon style="color: #1abc9c;"><i class="material-icons">person</i></md-icon>
                                <input required name="profile[lastname]" ng-model="loggedUserClone.profile.lastname" />
                                <div ng-messages="userInfoForm.lastname.$error">
                                    <div ng-message="required">Vous devez entrer au moins un nom</div>
                                </div>
                            </md-input-container>
                               
                            <!-- User Address -->    
                            <md-input-container class="md-block col-md-12" flex-gt-sm>

                                <input 
                                    vs-google-autocomplete
                                    vs-autocomplete-validator
                                    ng-model="loggedUserClone.profile.address"
                                    vs-city="loggedUserClone.profile.city"
                                    vs-state="loggedUserClone.profile.state"
                                    vs-country="loggedUserClone.profile.country"
                                    vs-post-code="loggedUserClone.profile.postcode"
                                    vs-longitude="loggedUserClone.profile.longitude"
                                    vs-latitude="loggedUserClone.profile.latitude"
                                    name="address"
                                >

                            </md-input-container>
                        
                            <!-- Country -->
                            <md-input-container class="md-block col-md-6" flex-gt-sm>
                                <label>Pays</label>
                                <input required name="country" ng-model="loggedUserClone.profile.country" />
                                <div ng-messages="signupForm.country.$error">
                                    <div ng-message="required">Vous devez entrer le pays</div>
                                </div>
                            </md-input-container>

                            <!-- State -->
                            <md-input-container class="md-block col-md-6" flex-gt-sm>
                                <label>État</label>
                                <input required name="state" ng-model="loggedUserClone.profile.state" />
                                <div ng-messages="signupForm.state.$error">
                                    <div ng-message="required">Veillez entrer l'état</div>
                                </div>
                            </md-input-container>

                            <!-- -->
                            <md-input-container class="md-block col-md-6 col-sm-12" flex-gt-sm>
                                <label>City</label>
                                <md-icon style="color: #1abc9c;"><i class="material-icons">place</i></md-icon>
                                <input required name="profile[city]" ng-model="loggedUserClone.profile.city" />
                                <div ng-messages="userInfoForm.city.$error">
                                    <div ng-message="required">Vous devex entrer une ville</div>
                                </div>
                            </md-input-container>

                            <md-input-container class="md-block col-md-6 col-sm-12" flex-gt-sm>
                                <label>Code Postal</label>
                                <md-icon style="color: #1abc9c;"><i class="material-icons">place</i></md-icon>
                                <input required name="profile[postcode]" ng-model="loggedUserClone.profile.postcode" />
                                <div ng-messages="userInfoForm.postcode.$error">
                                    <div ng-message="required">Veillez entrer votre code postale</div>
                                </div>
                            </md-input-container>
                                
                            <div class="alert alert-danger col-sm-12 message" ng-show="saveProfileError" id="saveProfileError">
                                <strong>Erreur!</strong> {{saveProfileErrorMessage}}.
                            </div>

                            <div class="alert alert-success col-sm-12 message" ng-show="saveProfileSucess" id="saveProfileSucess">
                                <strong>Success!</strong> {{saveProfileSuccessMessage}}
                            </div>

                            <div class="col-sm-12" style="margin-top : 30px; margin-bottom: 30px;">
                                <md-button type="submit" class="pull-right md-primary md-raised">Valider</md-button>
                            </div>

                        </form>
                    </div>
                    
                    <!-- Change Password -->
                    <div class="md-padding">

                        <div class="col-sm-12">
                            <p class="md-otiprix-text" style="text-align: center; margin: 5px;"><b>Changer votre mot de passe</b></p>
                        </div>

                        <form name="userSecurityForm" novalidate ng-submit="changePassword()">

                            <md-input-container class="md-block col-md-12" flex-gt-sm>
                                <label>Email</label>
                                <md-icon style="color: #1abc9c;"><i class="material-icons">email</i></md-icon>
                                <input disabled="true" style="border-left: none; border-top: none; border-right: none;" type="email" name="email" ng-model="loggedUser.email" />
                            </md-input-container>

                            <md-input-container class="md-block col-md-4 col-sm-12" flex-gt-sm>
                                <label>Ancien mot de passe</label>
                                <md-icon style="color: #1abc9c;"><i class="material-icons">lock</i></md-icon>
                                <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="old_password" ng-model="old_password" />
                                <div ng-messages="userSecurityForm.old_password.$error">
                                    <div ng-message="required">Vous devez confirmer votre ancien mot de passe.</div>
                                </div>
                            </md-input-container>

                            <md-input-container class="md-block col-md-4 col-sm-12" flex-gt-sm>
                                <label>Mot de passe</label>
                                <md-icon style="color: #1abc9c;"><i class="material-icons">lock</i></md-icon>
                                <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="password" equals="{{confirm_password}}" ng-pattern="/^(?=.*?[0-9])(?=.*?[a-z])(?=.*?[A-Z]).{8,}/" />
                                <div ng-messages="userSecurityForm.password.$error">
                                    <div ng-message="required">Un mot de passe est requis.</div>
                                    <div ng-message="pattern">Le mot de passe n'est pas assez fort. Le mot de passe doit comporter au moins huit caractères, au moins une lettre majuscule, une lettre minuscule et un chiffre.</div>
                                    <div ng-message="equals">Les mots de passe ne correspondent pas.</div>
                                </div>
                            </md-input-container>

                            <md-input-container class="md-block col-md-4 col-sm-12" flex-gt-sm>
                                <label>Confirmer mot de passe</label>
                                <md-icon style="color: #1abc9c;"><i class="material-icons">lock</i></md-icon>
                                <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="confirm_password" ng-model="confirm_password" equals="{{password}}" />
                                <div ng-messages="userSecurityForm.confirm_password.$error">
                                    <div ng-message="required">Vous devez confirmer votre mot de passe.</div>
                                    <div ng-message="equals">Les mots de passe ne correspondent pas.</div>
                                </div>
                            </md-input-container>
                            
                            <div class="alert alert-danger col-sm-12 message" ng-show="changePasswordError">
                                <strong>Erreur!</strong> {{changePasswordErrorMessage}}
                            </div>

                            <div class="alert alert-success col-sm-12 message" ng-show="changePasswordSuccess">
                                <strong>Success!</strong> {{changePasswordSuccessMessage}}
                            </div>

                            <div class="col-md-12">
                                <md-button class="md-raised md-primary pull-right" type="submit">
                                    Valider
                                </md-button>
                            </div>
                            
                            

                        </form>

                    </div>    
                    
                    <!-- Change security question -->
                    <div class="md-padding">

                        <div class="col-sm-12">
                            <p class="md-otiprix-text" style="text-align: center; margin: 5px;"><b>Changer la réponse et la question de sécurité</b></p>
                        </div>
                        
                        <form name="securityQuestionForm" ng-submit="changeSecurityQuestion()" novalidate>

                            <div>
                                <md-input-container class="md-block col-md-12" flex-gt-sm>
                                    <label>Question secrète</label>
                                    <md-select ng-model="loggedUser.security_question_id">
                                        <md-option ng-value="question.id" ng-repeat="question in securityQuestions">{{ question.name }}</md-option>
                                    </md-select>
                                </md-input-container>

                                <md-input-container class="md-block col-md-12" flex-gt-sm>
                                    <label>Reponse</label>
                                    <input required name="response" ng-model="loggedUser.security_question_answer" />
                                    <div ng-messages="securityQuestionForm.response.$error" ng-if="!showHints">
                                        <div ng-message="required">Une réponse de sécurité est nécessaire..</div>
                                    </div>
                                </md-input-container>
                                
                                <div class="alert alert-danger col-sm-12 message" ng-show="changeSecurityQuestionError">
                                    <strong>Erreur!</strong> {{changeSecurityQuestionErrorMessage}}
                                </div>

                                <div class="alert alert-success col-sm-12 message" ng-show="changeSecurityQuestionSuccess">
                                    <strong>Success!</strong> {{changeSecurityQuestionSuccessMessage}}
                                </div>

                                <div class="col-md-12">
                                    <md-button class="md-raised md-primary pull-right" type="submit">
                                        Valider
                                    </md-button>
                                </div>
                            </div>

                        </form>
                    </div> 
                    
                    <!-- Verify phone number -->
                    <div layout-padding>

                        <div class="col-sm-12"  ng-hide="userPhoneVerified">
                            <p class="md-otiprix-text message"><b>Vérifier votre numéro de téléphone</b></p>
                        </div>

                        <div class="col-sm-12" ng-show="enterVerificationNumber">
                            
                            <div class="alert alert-danger col-sm-12 message" ng-show="phoneNumberError">
                                <strong>Erreur!</strong> {{phoneNumberError}}.
                            </div>
                            
                            <div class="alert alert-success col-sm-12 message" ng-show="validateCodeMessage">
                                <strong>Success!</strong> {{validateCodeMessage}}
                            </div>

                            <div class="col-sm-12"  ng-show="userPhoneVerified">
                                <p class="md-otiprix-text message"><b>Verified : {{loggedUserClone.phone}}</b></p>
                            </div>

                            <p class="message">Veuillez entrer ci-dessous un numéro de téléphone où nous vous enverrons le code de vérification.</p>
                            
                            <md-input-container class="col-sm-12 col-md-6 col-md-offset-3">
                                <md-icon style="color: #1abc9c;"><i class="material-icons">phone</i></md-icon>
                                <input class="form-control" style="border-radius: 2px;" type="tel" id="phone">
                            </md-input-container>
                            
                            <div class="col-sm-12">
                                <md-button class="md-primary md-raised col-md-4 col-md-offset-4" ng-click="sendVerificationCode()">
                                    Valider
                                </md-button>
                            </div>
                            
                        </div>

                        <div class="col-sm-12" ng-hide="enterVerificationNumber">
                            <div class="alert alert-danger col-sm-12 message" ng-show="validateCodeMessage">
                                <strong>Erreur!</strong> {{validateCodeMessage}}
                            </div>
                            <p class="message">Veuillez entrer ci-dessous le code que vous avez reçu ou cliquez sur <a href ng-click="enterVerificationNumber = true">réessayez</a> pour renvoyer un autre code de vérification.</p>
                            <md-input-container class="col-sm-12 col-md-6 col-md-offset-3">
                                <label>Code</label>
                                <input ng-model="verificationCode">
                            </md-input-container>
                            <div class="col-sm-12">
                                <md-button class="md-primary md-raised col-md-4 col-md-offset-4" ng-click="validateCode()">
                                    Valider
                                </md-button>
                            </div>
                        </div>

                    </div>
                    
                </div>
                
                
                
            </md-tab>
            
        </div>
            
    </md-tabs>
   
</md-content>

<script src="<?php echo base_url("assets/js/company-account-products.js")?>"></script>
<script src="<?php echo base_url("assets/js/company-account-controller.js")?>"></script>
<script src="<?php echo base_url("assets/js/upload-controller.js")?>"></script>
 

