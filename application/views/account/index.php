<!DOCTYPE html>

<link rel="stylesheet" href="<?php echo base_url("node_modules/intl-tel-input/build/css/intlTelInput.min.css")?>">
<link rel="stylesheet" href="<?php echo base_url("assets/css/account.css")?>">


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
    
    <div class="otiprix-activate-account" ng-controller="ActivateAccountController as ctrl" ng-cloak>
        <div class="alert alert-danger" ng-if="!accountActivated">
            <?php echo $this->lang->line('activate_account'); ?>
            <?php echo $this->lang->line('or'); ?>
            <a href ng-click="ctrl.ResendActivationEmail($event)"><?php echo $this->lang->line('send_activation_email'); ?></a>
        </div>

        <div class="alert alert-success" ng-show="<?php echo isset($activated) ? "true" :"false" ?>">
            <?php echo $this->lang->line('account_activated'); ?>
        </div>
    </div>
    
    <div class="container-fluid" ng-controller="TabsController">
        <div class="row profile">
            <div class="col-md-3">
                    <div class="profile-sidebar  border-shadow">
                        <!-- SIDEBAR USERPIC -->
                        <div class="profile-userpic row justify-content-center">
                            <img src="<?php echo base_url("/assets/img/icons8-customer-80.png"); ?>" class="img-responsive" alt="">
                        </div>
                        <!-- END SIDEBAR USERPIC -->
                        <!-- SIDEBAR USER TITLE -->
                        <div class="profile-usertitle">
                            <div class="profile-usertitle-name">
                                {{loggedUser.profile.lastname}}, {{loggedUser.profile.firstname}}
                            </div>
                            <div class="profile-usertitle-job">
                                {{loggedUser.email}}
                            </div>
                        </div>
                        <!-- END SIDEBAR USER TITLE -->
                        <!-- SIDEBAR MENU -->
                        <div class="profile-usermenu">
                            <ul class="nav flex-column">
                                <li class="nav-item" ng-class="{active : sessionData.accountMenuIndex == 1}">
                                    <a class="nav-link" ng-click="sessionData.accountMenuIndex = 1;" href><i class="glyphicon glyphicon-user"></i>informations de l'utilisateur</a>
                                </li>
                                <li class="nav-item" ng-class="{active : sessionData.accountMenuIndex == 2}">
                                    <a class="nav-link" ng-click="sessionData.accountMenuIndex = 2;" href><i class="glyphicon glyphicon-lock"></i>Securité</a>
                                </li>
                                <li class="nav-item" ng-class="{active : sessionData.accountMenuIndex == 3}">
                                    <a class="nav-link" ng-click="sessionData.accountMenuIndex = 3;" href><i class="glyphicon glyphicon-stats"></i>Historique de mes économies</a>
                                </li>
                                <li class="nav-item" ng-class="{active : sessionData.accountMenuIndex == 4}">
                                    <a class="nav-link" ng-click="sessionData.accountMenuIndex = 4;" href><i class="glyphicon glyphicon-list"></i>Vos liste 'd'épicerie</a>
                                </li>
                                <li class="nav-item" ng-class="{active : sessionData.accountMenuIndex == 5}">
                                    <a class="nav-link" ng-click="sessionData.accountMenuIndex = 5;" href><i class="glyphicon glyphicon-heart"></i>Magasins préférés</a>
                                </li>
                            </ul>
                        </div>
				<!-- END MENU -->
                    </div>
		</div>
            <div class="col-md-9 profile-content border-shadow">
                <div class="p-5">
                    
                    <div>
                        
                        <!-- Change personal info -->    
                        <div layout-padding ng-if="sessionData.accountMenuIndex == 1">
                            
                            <h2 otiprix-title class="text-center">Informations de l'utilisateur</h2>
                            
                            <form  ng-controller="AccountController" name="userInfoForm" novalidate ng-submit="updateProfile()">

                                <!-- First name -->
                                <md-input-container class="md-block" flex-gt-sm>
                                    <label>Prénom</label>
                                    <md-icon class="md-primary"><i class="material-icons">person</i></md-icon>
                                    <input name="profile[firstname]" ng-model="loggedUserClone.profile.firstname" />
                                </md-input-container>
                                
                                <!-- Firstname -->
                                <md-input-container class="md-block" flex-gt-sm>
                                    <label>Nom</label>
                                    <md-icon class="md-primary"><i class="material-icons">person</i></md-icon>
                                    <input required name="profile[lastname]" ng-model="loggedUserClone.profile.lastname" />
                                    <div ng-messages="userInfoForm.lastname.$error">
                                        <div ng-message="required">Vous devez entrer au moins un nom</div>
                                    </div>
                                </md-input-container>

                                <!-- User Address -->    
                                <md-input-container class="md-block" flex-gt-sm>

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
                                        aria-label="Addresse"
                                    >

                                </md-input-container>
                                
                                <!-- City -->
                                <md-input-container class="md-block" flex-gt-sm>
                                    <label>Ville</label>
                                    <md-icon class="md-primary"><i class="material-icons">place</i></md-icon>
                                    <input required name="profile[city]" ng-model="loggedUserClone.profile.city" />
                                    <div ng-messages="userInfoForm.city.$error">
                                        <div ng-message="required">Vous devez entrer une ville</div>
                                    </div>
                                </md-input-container>

                                <!-- State -->
                                <md-input-container class="md-block" flex-gt-sm>
                                    <label>Province</label>
                                    <input required name="state" ng-model="loggedUserClone.profile.state" />
                                    <div ng-messages="signupForm.state.$error">
                                        <div ng-message="required">Veillez entrer la province</div>
                                    </div>
                                </md-input-container>
                                
                                 <!-- Country -->
                                <md-input-container class="md-block" flex-gt-sm>
                                    <label>Pays</label>
                                    <input required name="country" ng-model="loggedUserClone.profile.country" />
                                    <div ng-messages="signupForm.country.$error">
                                        <div ng-message="required">Vous devez entrer le pays</div>
                                    </div>
                                </md-input-container>

                                <!-- Postal Code -->
                                <md-input-container class="md-block" flex-gt-sm>
                                    <label>Code Postal</label>
                                    <md-icon class="md-primary"><i class="material-icons">place</i></md-icon>
                                    <input required name="profile[postcode]" ng-model="loggedUserClone.profile.postcode" />
                                    <div ng-messages="userInfoForm.postcode.$error">
                                        <div ng-message="required">Veillez entrer votre code postal</div>
                                    </div>
                                </md-input-container>

                                <div class="alert alert-danger col-sm-12 message" ng-show="saveProfileError" id="saveProfileError">
                                    <strong>Erreur!</strong> {{saveProfileErrorMessage}}.
                                </div>

                                <div class="alert alert-success col-sm-12 message" ng-show="saveProfileSucess" id="saveProfileSucess">
                                    <strong>Success!</strong> {{saveProfileSuccessMessage}}
                                </div>

                                <div class="col-sm-12">
                                    <md-button type="submit" class="pull-right md-primary md-raised btn">Valider</md-button>
                                </div>

                            </form>
                        </div>

                        <!-- Security -->
                        <div class="p-5" ng-if="sessionData.accountMenuIndex == 2" ng-controller="AccountController">

                            <!-- Change Password -->
                            <div class="row">
                                
                                <h2 otiprix-title class="text-center">Changer votre mot de passe</h2>

                                <form class="w-100" name="userSecurityForm" novalidate ng-submit="changePassword()">

                                    <md-input-container class="md-block" flex-gt-sm>
                                        <label>Ancien mot de passe</label>
                                        <md-icon class="md-primary"><i class="material-icons">lock</i></md-icon>
                                        <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="old_password" ng-model="old_password" />
                                        <div ng-messages="userSecurityForm.old_password.$error">
                                            <div ng-message="required">Vous devez confirmer votre ancien mot de passe.</div>
                                        </div>
                                    </md-input-container>

                                    <md-input-container class="md-block" flex-gt-sm>
                                        <label>Nouveau mot de passe</label>
                                        <md-icon class="md-primary"><i class="material-icons">lock</i></md-icon>
                                        <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="password" equals="{{confirm_password}}" ng-pattern="/^(?=.*?[0-9])(?=.*?[a-z])(?=.*?[A-Z]).{8,}/" />
                                        <div ng-messages="userSecurityForm.password.$error">
                                            <div ng-message="required">Un mot de passe est requis.</div>
                                            <div ng-message="pattern">Le mot de passe n'est pas assez fort. Le mot de passe doit comporter au moins huit caractères, au moins une lettre majuscule, une lettre minuscule et un chiffre.</div>
                                            <div ng-message="equals">Les mots de passe ne correspondent pas.</div>
                                        </div>
                                    </md-input-container>

                                    <md-input-container class="md-block " flex-gt-sm>
                                        <label>Confirmer mot de passe</label>
                                        <md-icon class="md-primary"><i class="material-icons">lock</i></md-icon>
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
                                        <md-button class="md-raised md-primary pull-right btn" type="submit">
                                            Valider
                                        </md-button>
                                    </div>

                                </form>
                                
                            </div>
                            
                            <!-- Change security question -->
                            <div class="row">
                                
                                <div class="w-100" ng-if="sessionData.accountMenuIndex == 2" layout-padding>

                                    <div class="col-sm-12">
                                        <p otiprix-text style="text-align: center; margin: 5px;"><b>Changer la réponse et la question de sécurité</b></p>
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
                                
                            </div>
                            
                            <!-- Verify phone number -->
                            <div class="w-100 my-4">
                                
                                <p otiprix-text class="text-center font-weight-bold" ng-hide="userPhoneVerified">Vérifier votre numéro de téléphone</p>

                                <div class="w-100" ng-show="enterVerificationNumber">

                                    <div class="alert alert-danger col-sm-12 message" ng-show="phoneNumberError">
                                        <strong>Erreur!</strong> {{phoneNumberError}}.
                                    </div>

                                    <div class="alert alert-success col-sm-12 message" ng-show="validateCodeMessage">
                                        <strong>Success!</strong> {{validateCodeMessage}}
                                    </div>

                                    <p ng-show="userPhoneVerified" otiprix-text class="message text-center"><b>Verified : {{loggedUserClone.phone}}</b></p>

                                    <p otiprix-text class="text-center">Veuillez entrer ci-dessous un numéro de téléphone où nous vous enverrons le code de vérification.</p>

                                    <div class="d-flex flex-row justify-content-center">
                                        <input class="form-control" style="border-radius: 2px;" type="tel" id="phone">
                                        <md-button class="md-primary md-raised my-0" ng-click="sendVerificationCode()">
                                            Valider
                                        </md-button>
                                    </div>

                                </div>

                                <div class="w-100" ng-hide="enterVerificationNumber">
                                    <div class="alert alert-danger col-sm-12 message" ng-show="validateCodeMessage">
                                        <strong>Erreur!</strong> {{validateCodeMessage}}
                                    </div>
                                    <p class="message text-center">Veuillez entrer ci-dessous le code que vous avez reçu ou cliquez sur <a href ng-click="enterVerificationNumber = true">réessayez</a> pour renvoyer un autre code de vérification.</p>

                                    <div class="w-100 d-flex flex-row justify-content-center align-items-center">
                                        <md-input-container>
                                            <label>Code</label>
                                            <input ng-model="verificationCode">
                                        </md-input-container>
                                        <md-button class="md-primary md-raised" ng-click="validateCode()">
                                            Valider
                                        </md-button>
                                    </div>

                                </div>

                                
                            </div>

                        </div>   
                        
                        <!-- History -->
                        <div ng-if="sessionData.accountMenuIndex == 3">
                            
                            <h2 otiprix-title  class="text-center">Historique de mes économies</h2>
                            
                            <div class="md-padding">
                                <md-list ng-controller="AccountOptimizationController">
                                    <md-list-item class="md-3-line" ng-repeat="item in optimizations">
                                      <div class="md-list-item-text"  style="margin-bottom: 10px;">
                                        <h3 otiprix-text><b>{{item.label}}</b></h3>
                                        <h4>Économies moyen : <b style="color : red;"><span ng-show="item.value != '-'">$ CAD</span> {{item.value}}</b></h4>
                                        <p>Nombre moyen de produits par panier : <b>{{item.count}}</b></p>
                                      </div>
                                        <md-button class="md-secondary md-primary" ng-click="viewOptimization($index, $event)">Voir détails</md-button>
                                      <md-divider ng-if="!$last"></md-divider>
                                    </md-list-item>
                                </md-list>
                            </div>
                        </div>
                        
                        <!-- Grocery List -->
                        <div ng-if="sessionData.accountMenuIndex == 4">
                            <h2 otiprix-title  class="text-center">Vos liste d'épicerie</h2>
                            <div  ng-controller="UserListController" id="groceryListContainer" ng-include="'<?php echo base_url(); ?>/assets/templates/user_grocery_list.html'"></div>
                        </div>
                        
                        <!-- Favorite stores -->
                        <div  ng-if="sessionData.accountMenuIndex == 5">
                            <div ng-controller="SelectAccountStoreController" id="select-store-container" ng-include="'<?php echo base_url(); ?>/assets/templates/account-select-favorite-stores.html'"></div>
                        </div>

                    </div>
                    
                </div>
            </div>
	</div>
    </div>
    
</md-content>


 

