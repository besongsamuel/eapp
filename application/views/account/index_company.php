<!DOCTYPE html>

<link rel="stylesheet" href="<?php echo base_url("node_modules/intl-tel-input/build/css/intlTelInput.css")?>">
<link rel="stylesheet" href="<?php echo base_url("assets/css/account.css")?>">

<script>
    
$(document).ready(function()
{
    $("#phone").intlTelInput({utilsScript : "<?php echo base_url("node_modules/intl-tel-input/build/js/utils.js")?>"});
    $('#OpenImgUpload').click(function(){ $('#fileUploadButton').trigger('click'); });
});
</script>


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
    
    <div style="text-align: center;" ng-controller="AccountController">
        <div ng-show="isNewAccount" class="alert alert-success" role="alert">      
            Votre compte a été créé avec succès. Commencez par ajouter des succursales à votre compte d'entreprise.
        </div>
    </div>
    
    <div ng-controller="TabsController" layout-padding>
        <div class="row profile">
            <div class="col-md-3">
                    <div class="profile-sidebar">
                        <!-- SIDEBAR USERPIC -->
                        <div class="profile-userpic" ng-controller="CompanyAccountController">
                            <image-upload 
                                image="storeLogo" 
                                caption="Ajouter logo" 
                                on-file-removed="onFileRemoved()" 
                                on-file-selected="imageChanged(file)"></image-upload>
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
                        <!-- SIDEBAR BUTTONS -->
                        <div class="profile-userbuttons">
                            
                            <p class="subscription-header"><b>Votre Forfait : &nbsp;&nbsp;&nbsp;<span class='md-warn-color'>  {{loggedUser.company.subscription.name}}</span></b>
                            <form action="<?php echo site_url("account/select_subscription"); ?>">
                                <md-button class="md-raised md-warn btn" type="submit">
                                    Changer Forfait
                                </md-button>
                            </form>
                            
                        </div>
                        <!-- END SIDEBAR BUTTONS -->
                        <!-- SIDEBAR MENU -->
                        <div class="profile-usermenu">
                            <ul class="nav">
                                <li ng-class="{active : sessionData.accountMenuIndex == 1}">
                                    <a ng-click="sessionData.accountMenuIndex = 1;" href><i class="glyphicon glyphicon-user"></i>informations utilisateur</a>
                                </li>
                                <li ng-class="{active : sessionData.accountMenuIndex == 2}">
                                    <a ng-click="sessionData.accountMenuIndex = 2;" href><i class="glyphicon glyphicon-lock"></i>Securité</a>
                                </li>
                                <li ng-class="{active : sessionData.accountMenuIndex == 3}">
                                    <a ng-click="sessionData.accountMenuIndex = 3;" href><i class="glyphicon glyphicon-stats"></i>Statistiques</a>
                                </li>
                                <li ng-class="{active : sessionData.accountMenuIndex == 4}">
                                    <a ng-click="sessionData.accountMenuIndex = 4;" href><i class="glyphicon glyphicon-list"></i>Vos Produits</a>
                                </li>
                                <li ng-class="{active : sessionData.accountMenuIndex == 5}">
                                    <a ng-click="sessionData.accountMenuIndex = 5;" href><i class="glyphicon glyphicon-heart"></i>Vos Succursales</a>
                                </li>
                                <li ng-class="{active : sessionData.accountMenuIndex == 6}">
                                    <a ng-click="sessionData.accountMenuIndex = 6;" href><i class="glyphicon glyphicon-heart"></i>Informations Entreprise</a>
                                </li>
                            </ul>
                        </div>
				<!-- END MENU -->
                    </div>
		</div>
            <div class="col-md-9">
                <div class="profile-content">
                    
                    <div>
                        
                        <!-- Change personal info -->    
                        <div layout-padding ng-if="sessionData.accountMenuIndex == 1">
                            <div ng-controller="AccountController">
                                <div layout-padding>
                                    <form name="userInfoForm" novalidate ng-submit="updateProfile()">

                                        <md-input-container class="md-block col-md-12 col-sm-12" flex-gt-sm>
                                            <label>Email</label>
                                            <md-icon class="md-primary"><i class="material-icons">email</i></md-icon>
                                            <input disabled="true" style="border-left: none; border-top: none; border-right: none;" type="email" name="email" ng-model="loggedUserClone.email" />
                                        </md-input-container>
                                            <!-- -->
                                        <md-input-container class="md-block col-md-6 col-sm-12" flex-gt-sm>
                                            <label>Prénom</label>
                                            <md-icon class="md-primary"><i class="material-icons">person</i></md-icon>
                                            <input name="profile[firstname]" ng-model="loggedUserClone.profile.firstname" />
                                        </md-input-container>
                                            <!-- -->
                                        <md-input-container class="md-block col-md-6 col-sm-12" flex-gt-sm>
                                            <label>Nom</label>
                                            <md-icon class="md-primary"><i class="material-icons">person</i></md-icon>
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
                                                aria-label="Addresse"
                                            >

                                        </md-input-container>
                                        
                                        <!-- City -->
                                        <md-input-container class="md-block col-md-6 col-sm-12" flex-gt-sm>
                                            <label>Ville</label>
                                            <md-icon class="md-primary"><i class="material-icons">place</i></md-icon>
                                            <input required name="profile[city]" ng-model="loggedUserClone.profile.city" />
                                            <div ng-messages="userInfoForm.city.$error">
                                                <div ng-message="required">Vous devez entrer une ville</div>
                                            </div>
                                        </md-input-container>

                                        <!-- State -->
                                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                                            <label>Province</label>
                                            <input required name="state" ng-model="loggedUserClone.profile.state" />
                                            <div ng-messages="signupForm.state.$error">
                                                <div ng-message="required">Veillez entrer la province</div>
                                            </div>
                                        </md-input-container>
                                        
                                        <!-- Country -->
                                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                                            <label>Pays</label>
                                            <input required name="country" ng-model="loggedUserClone.profile.country" />
                                            <div ng-messages="signupForm.country.$error">
                                                <div ng-message="required">Vous devez entrer le pays</div>
                                            </div>
                                        </md-input-container>
                                        
                                        <!-- Postal Code -->
                                        <md-input-container class="md-block col-md-6 col-sm-12" flex-gt-sm>
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

                                        <div class="col-sm-12" style="margin-top : 30px; margin-bottom: 30px;">
                                            <md-button type="submit" class="pull-right md-primary md-raised">Valider</md-button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Security -->
                        <div ng-if="sessionData.accountMenuIndex == 2" layout-padding ng-controller="AccountController">

                            <!-- Change Password -->
                            <div class="row">
                                
                                <div class="col-sm-12">
                                    <p class="md-otiprix-text" style="text-align: center; margin: 5px;"><b>Changer votre mot de passe</b></p>
                                </div>

                                <form name="userSecurityForm" novalidate ng-submit="changePassword()">

                                    <md-input-container class="md-block col-md-4 col-sm-12" flex-gt-sm>
                                        <label>Ancien mot de passe</label>
                                        <md-icon class="md-primary"><i class="material-icons">lock</i></md-icon>
                                        <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="old_password" ng-model="old_password" />
                                        <div ng-messages="userSecurityForm.old_password.$error">
                                            <div ng-message="required">Vous devez confirmer votre ancien mot de passe.</div>
                                        </div>
                                    </md-input-container>

                                    <md-input-container class="md-block col-md-4 col-sm-12" flex-gt-sm>
                                        <label>Nouveau mot de passe</label>
                                        <md-icon class="md-primary"><i class="material-icons">lock</i></md-icon>
                                        <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="password" equals="{{confirm_password}}" ng-pattern="/^(?=.*?[0-9])(?=.*?[a-z])(?=.*?[A-Z]).{8,}/" />
                                        <div ng-messages="userSecurityForm.password.$error">
                                            <div ng-message="required">Un mot de passe est requis.</div>
                                            <div ng-message="pattern">Le mot de passe n'est pas assez fort. Le mot de passe doit comporter au moins huit caractères, au moins une lettre majuscule, une lettre minuscule et un chiffre.</div>
                                            <div ng-message="equals">Les mots de passe ne correspondent pas.</div>
                                        </div>
                                    </md-input-container>

                                    <md-input-container class="md-block col-md-4 col-sm-12" flex-gt-sm>
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
                                
                                <div ng-if="menuIndex == 2" layout-padding>

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
                                
                            </div>
                            
                            <!-- Verify phone number -->
                            <div class="row">
                                
                                <div ng-if="menuIndex == 2" layout-padding>

                                    <div class="row"  ng-hide="userPhoneVerified">
                                        <b><p class="md-otiprix-text message">Vérifier votre numéro de téléphone</p></b>
                                    </div>

                                    <div class="col-sm-12" ng-show="enterVerificationNumber">

                                        <div class="alert alert-danger col-sm-12 message" ng-show="phoneNumberError">
                                            <strong>Erreur!</strong> {{phoneNumberError}}.
                                        </div>

                                        <div class="alert alert-success col-sm-12 message" ng-show="validateCodeMessage">
                                            <strong>Success!</strong> {{validateCodeMessage}}
                                        </div>

                                        <div class="row"  ng-show="userPhoneVerified">
                                            <p class="md-otiprix-text message"><b>Verified : {{loggedUserClone.phone}}</b></p>
                                        </div>

                                        <div class="row">
                                            <p class="message">Veuillez entrer ci-dessous un numéro de téléphone où nous vous enverrons le code de vérification.</p>
                                        </div>

                                        <div class="row">
                                            <div class="center-block" style="width : 315px;">
                                                <input class="form-control" style="border-radius: 2px;" type="tel" id="phone">
                                                <md-button class="md-primary md-raised" ng-click="sendVerificationCode()">
                                                    Valider
                                                </md-button>
                                            </div>
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

                        </div>   
                        
                        <!-- Statistics -->
                        <div ng-if="sessionData.accountMenuIndex == 3">
                            
                            <div ng-controller="CompanyStatsController as ctrl">

                                <div layout="row" layout-align='center center'>
                                    <md-radio-group ng-change="ctrl.periodChanged()" ng-model="period" >

                                        <md-radio-button value="1" class="md-primary">Année</md-radio-button>
                                        <md-radio-button value="0" class="md-primary"> Mois </md-radio-button>

                                    </md-radio-group>
                                </div>

                                <div layout='row' layout-align='center center'>
                                    <div>
                                        <md-progress-circular ng-show='loading' md-mode="indeterminate"></md-progress-circular>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 30px;">

                                    <div class="panel-group" role="tablist" id="accordion" aria-multiselectable="true">

                                        <div id="origin-products" class="panel panel-default">

                                            <div class="panel-heading" role="tab" id="headingOne">
                                                <h4 class="panel-title">
                                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        Statistiques sur l'origine
                                                    </a>
                                                </h4>
                                            </div>

                                            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">

                                                <div class="panel-body">

                                                    <top-products 
                                                        ng-if="stats.top_viewed_product_states && stats.top_viewed_product_states.length > 0" 
                                                        data="stats.top_viewed_product_states" 
                                                        count-caption="Visites :"
                                                        caption="Origine des produits visités par les utilisateurs"></top-products>

                                                    <top-products 
                                                        ng-if="stats.top_cart_product_states && stats.top_cart_product_states.length > 0" 
                                                        data="stats.top_cart_product_states" 
                                                        count-caption="Nombre de fois ajoutées au panier :"
                                                        caption="Origine des produits ajoutés au panier par les utilisateurs"></top-products>

                                                    <span class="col-sm-12">{{stats.get_percentage_bio_added_to_cart}} % de produits  bio sont ajoutées au panier</span>
                                                    <div class="col-sm-12">
                                                        <div class="progress">
                                                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40"
                                                            aria-valuemin="0" aria-valuemax="100" style="width:{{stats.get_percentage_bio_added_to_cart}}%">
                                                                {{stats.get_percentage_bio_added_to_cart}} %
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <span class="col-sm-12">
                                                        {{stats.get_percentage_bio_viewed}} % de produits  bio sont visitées
                                                    </span>

                                                    <div class="col-sm-12">
                                                        <div class="progress">
                                                            <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50"
                                                                aria-valuemin="0" aria-valuemax="100" style="width:{{stats.get_percentage_bio_viewed}}%">
                                                                {{stats.get_percentage_bio_viewed}} %
                                                            </div>
                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="panel panel-default">

                                            <div class="panel-heading" role="tab" id="headingTwo">
                                                <h4 class="panel-title">
                                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                                        Statistiques sur les produits
                                                    </a>
                                                </h4>
                                            </div>

                                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">

                                                <div class="panel-body">
                                                    
                                                    <top-products 
                                                    ng-if="stats.get_top_recurring_products && stats.get_top_recurring_products.length > 0" 
                                                    data="stats.get_top_recurring_products" 
                                                    count-caption="Nombre de fois en circulaire :"
                                                    caption="Les 5 produits qui reviennent le plus souvent en circulaire"></top-products>

                                                    <top-products 
                                                    ng-if="stats.top_listed_products && stats.top_listed_products.length > 0" 
                                                    data="stats.top_listed_products" 
                                                    count-caption="Nombre d'utilisateurs avec produit dans leur liste :"
                                                    caption="Les 5 produits qui reviennent le plus souvent dans la liste d'épicerie des utilisateurs"></top-products>

                                                    <top-products 
                                                    ng-if="stats.top_viewed_products && stats.top_viewed_products.length > 0" 
                                                    data="stats.top_viewed_products" 
                                                    count-caption="Visites :"
                                                    caption="Les 5 produits les plus visités par les utilisateurs"></top-products>

                                                    <top-products 
                                                    ng-if="stats.top_searched_products && stats.top_searched_products.length > 0" 
                                                    data="stats.top_searched_products" 
                                                    count-caption="Nombre de fois recherché :"
                                                    caption="Les 5 produits les plus recherchés par les utilisateurs"></top-products>

                                                    <top-products 
                                                    ng-if="stats.top_product_categories && stats.top_product_categories.length > 0" 
                                                    data="stats.top_product_categories" 
                                                    count-caption="Visites :"
                                                    caption="Les cinq catégories de produits les plus visités par les utilisateurs"></top-products>

                                                    <top-products 
                                                    ng-if="stats.top_cart_products && stats.top_cart_products.length > 0" 
                                                    data="stats.top_cart_products" 
                                                    count-caption="Nombre de fois ajoutées au panier :"
                                                    caption="Les 5 produits les plus ajoutés au panier par les utilisateurs"></top-products>

                                                    <top-products 
                                                    ng-if="stats.top_product_brands && stats.top_product_brands.length > 0" 
                                                    data="stats.top_product_brands" 
                                                    count-caption="Visites :"
                                                    caption="Les 5 marques les plus ajoutées au panier par les utilisateurs"></top-products>
                                                    
                                                    <top-products 
                                                    ng-if="stats.least_recurring_products && stats.least_recurring_products.length > 0" 
                                                    data="stats.least_recurring_products" 
                                                    count-caption="Nombre de fois en circulaire :"
                                                    caption="Les 5 produits qui reviennent le moins souvent en circulaire"></top-products>
                                                    
                                                    <top-products 
                                                    ng-if="stats.least_listed_products && stats.least_listed_products.length > 0" 
                                                    data="stats.least_listed_products" 
                                                    count-caption="Nombre d'utilisateurs avec produit dans leur liste :"
                                                    caption="Les 5 produits qui reviennent le moins souvent dans la liste d'épicerie des utilisateurs"></top-products>

                                                    <top-products 
                                                    ng-if="stats.least_viewed_products && stats.least_viewed_products.length > 0" 
                                                    data="stats.least_viewed_products" 
                                                    count-caption="Visites :"
                                                    caption="Les 5 produits les moins visités par les utilisateurs"></top-products>
                                                    
                                                    <top-products 
                                                    ng-if="stats.least_searched_products && stats.least_searched_products.length > 0" 
                                                    data="stats.least_searched_products" 
                                                    count-caption="Nombre de fois recherché :"
                                                    caption="Les 5 produits les moins recherchés par les utilisateurs"></top-products>
                                                    
                                                    <top-products 
                                                    ng-if="stats.least_product_categories && stats.least_product_categories.length > 0" 
                                                    data="stats.least_product_categories" 
                                                    count-caption="Visites :"
                                                    caption="La catégorie de produits la moins visitée par les utilisateurs"></top-products>
                                                    
                                                    <top-products 
                                                    ng-if="stats.least_product_brands && stats.least_product_brands.length > 0" 
                                                    data="stats.least_product_brands" 
                                                    count-caption="Visites :"
                                                    caption="Les 5 marques les moins ajoutées au panier par les utilisateurs"></top-products>
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        <div class="panel panel-default">

                                            <div class="panel-heading" role="tab" id="headingThree">
                                                <h4 class="panel-title">
                                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                                                        Statistiques des magasins
                                                    </a>
                                                </h4>
                                            </div>

                                            <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">

                                                <div class="panel-body">

                                                    <div ng-if="stats.most_visited_store">

                                                        <span class="col-sm-12">
                                                            <b>{{stats.most_visited_store.retailer.name}}</b> entre environs {{stats.most_visited_store.avg}} produits par mois en moyenne.
                                                        </span>

                                                    </div>

                                                    <top-products 
                                                    ng-if="stats.get_top_visited_chains && stats.get_top_visited_chains.length > 0" 
                                                    data="stats.get_top_visited_chains" 
                                                    count-caption="Visites :"
                                                    caption="Les magasins les plus visitées"></top-products>


                                                    <top-products 
                                                    ng-if="stats.top_optimized_chains && stats.top_optimized_chains.length > 0" 
                                                    data="stats.top_optimized_chains" 
                                                    count-caption="Produits optimisées :"
                                                    caption="Les magasins les plus optimisées"></top-products>

                                                </div>

                                            </div>

                                        </div>

                                        <div class="panel panel-default">

                                            <div class="panel-heading" role="tab" id="headingFour">
                                                <h4 class="panel-title">
                                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                                                        Statistiques de mon Épicerie
                                                    </a>
                                                </h4>
                                            </div>

                                            <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">

                                                <div class="panel-body">

                                                    <div ng-if="stats.get_store_userlist_info">

                                                        <span class="col-sm-12">
                                                            {{stats.get_store_userlist_info.users}} % des utilisateurs m'ont comme magasin préféré.
                                                        </span>

                                                        <div class="col-sm-12">
                                                            <div class="progress">
                                                                <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50"
                                                                    aria-valuemin="0" aria-valuemax="100" style="width:{{stats.get_store_userlist_info.users}}%">
                                                                    {{stats.get_store_userlist_info.users}} %
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div ng-if="stats.get_store_visitors_info">

                                                        <span class="col-sm-12">
                                                            {{stats.get_store_visitors_info.visits}}% des utilisateurs visitent votre magasin à une distance d'environ {{stats.get_store_visitors_info.avg_distance}} km.
                                                        </span>

                                                        <div class="col-sm-12">
                                                            <div class="progress">
                                                                <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50"
                                                                    aria-valuemin="0" aria-valuemax="100" style="width:{{stats.get_store_visitors_info.visits}}%">
                                                                    {{stats.get_store_visitors_info.visits}} %
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>

                                                    <div ng-if="stats.get_product_visitors_info">

                                                        <span class="col-sm-12">
                                                            {{stats.get_product_visitors_info.visits}}% des produits ajoutées au panier sont de votre magasin et sont des utilisateurs à une distance d'environ {{stats.get_product_visitors_info.avg_distance}} km.
                                                        </span>

                                                        <div class="col-sm-12">
                                                            <div class="progress">
                                                                <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50"
                                                                    aria-valuemin="0" aria-valuemax="100" style="width:{{stats.get_product_visitors_info.visits}}%">
                                                                    {{stats.get_product_visitors_info.visits}} %
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                            </div>

                            </div>
                        </div>
                        
                        <!-- Products -->
                        <div ng-if="sessionData.accountMenuIndex == 4">
                            
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
                                                      
                        </div>
                        
                        <!-- Succursales -->
                        <div  ng-if="sessionData.accountMenuIndex == 5">
                            <div class="row layout-padding" ng-controller="AccountController">
                                <add-department-store department-stores='loggedUser.company.chain.department_stores'></add-department-store>
                            </div>
                        </div>
                        
                        <!-- Entreprise -->
                        <div ng-if="sessionData.accountMenuIndex == 6">
                            
                            <div ng-controller="CompanyAccountController" >

                                <div id="error_message" class="alert alert-success" ng-show="successMessage">
                                    <p style="text-align: center;">{{successMessage}}</p>
                                    <span></span>
                                </div>

                                <form class="companyForm" novalidate ng-submit="editCompany()" class="layout-padding">

                                    <!-- COMPANY INFORMATION -->
                                    <fieldset style="margin: 10px;">

                                        <legend>RENSEIGNEMENTS D'ENTREPRISE</legend>

                                        <p class="subscription-header"><b>Votre Forfait : &nbsp;&nbsp;&nbsp;<span class='md-warn-color'>  {{loggedUser.company.subscription.name}}</span></b> &nbsp;|&nbsp; <a href='<?php echo site_url("account/select_subscription"); ?>'>Changer</a></p>

                                        <!-- NEQ -->
                                        <md-input-container ng-disabled="loggedUser.company.is_valid == 1" class="md-block col-md-12" flex-gt-sm>
                                            <label>NEQ</label>
                                            <!-- <input required name="neq" ng-model="company.neq" /> -->
                                            <input ng-disabled="true" required name="neq" ng-model="company.neq" />
                                            <div ng-messages="companyForm.neq.$error">
                                                <div ng-message="required">Vous devez entrer le NEQ de l'entreprise</div>
                                            </div>
                                        </md-input-container>

                                        <!-- NOM DE L'ENTREPRISE -->
                                        <md-input-container class="md-block col-md-12" flex-gt-sm>
                                            <label>Nom de l'entreprise</label>
                                            <!-- <input required name="company_name" ng-model="company.name" /> -->
                                            <input ng-disabled="true" required name="company_name" ng-model="company.name" />
                                            <div ng-messages="companyForm.company_name.$error">
                                                <div ng-message="required">Vous devez entrer au moins un nom pour l'entreprise</div>
                                            </div>
                                        </md-input-container>

                                        <!-- <div class="col-sm-12" style="margin-top : 30px; margin-bottom: 30px;">
                                            <md-button type="submit" class="pull-right md-primary md-raised btn">Valider</md-button>
                                        </div> -->


                                    </fieldset>

                                </form>
                            </div>                            
                        </div>

                    </div>
                    
                </div>
            </div>
	</div>
    </div>
    
</md-content>



 

