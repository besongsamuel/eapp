<link href="<?php echo base_url("assets/css/register.css"); ?>" rel="stylesheet">


<div id="admin-container" class="container mainbox" ng-controller="AccountController" ng-cloak>    


    
    <div id="signupbox">
        <div class="panel panel-info">

            <md-toolbar class="md-primary">
                <div>
                    <h2 class="md-toolbar-tools">INSCRIPTION</h2>
                </div>
            </md-toolbar>

            <md-content class="panel-body" >
                <form id="signupForm" name="signupForm" class="form-horizontal" novalidate ng-submit="register()">

                    <div id="error_message" class="alert alert-danger" ng-show="message">
                        <p>{{message}}</p>
                        <span></span>
                    </div>
                    <fieldset style="margin: 10px;">

                        <legend class="md-title">FORMULAIRE D'INSCRIPTION</legend>

                        <!-- -->
                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Prénom</label>
                            <md-icon class="md-primary"><i class="material-icons">person</i></md-icon>
                            <input name="firstname" ng-model="user.firstname" />
                        </md-input-container>

                        <!-- -->
                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Nom</label>
                            <input required name="lastname" ng-model="user.lastname" />
                            <div ng-messages="signupForm.lastname.$error">
                                <div ng-message="required">Vous devez entrer au moins un nom</div>
                            </div>
                        </md-input-container>
                        
                        <!-- User Address -->
                        <md-input-container class="col-sm-12">
                            <input 
                                vs-google-autocomplete
                                vs-autocomplete-validator
                                ng-model="user.address"

                                vs-city="user.city"
                                vs-state="user.state"
                                vs-country="user.country"
                                vs-post-code="user.postcode"
                                vs-longitude="user.longitude"
                                vs-latitude="user.latitude"
                                name="address"
                            >
                        </md-input-container>
                        
                         <!-- city -->
                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Ville</label>
                            <input required name="city" ng-model="user.city" />
                            <div ng-messages="signupForm.city.$error">
                                <div ng-message="required">Vous devez entrer une ville</div>
                            </div>
                        </md-input-container>
       
                        <!-- State -->
                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Province</label>
                            <input required name="state" ng-model="user.state" />
                            <div ng-messages="signupForm.state.$error">
                                <div ng-message="required">Veillez entrer la province</div>
                            </div>
                        </md-input-container>

                        
                        <!-- Country -->
                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Pays</label>
                            <input required name="country" ng-model="user.country" />
                            <div ng-messages="signupForm.country.$error">
                                <div ng-message="required">Vous devez entrer le pays</div>
                            </div>
                        </md-input-container>
                        
                        <!-- Postal Code -->
                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Code Postal</label>
                            <input required name="postcode" ng-model="user.postcode" />
                            <div ng-messages="signupForm.postcode.$error">
                                <div ng-message="required">Veillez entrer votre code postal</div>
                            </div>
                        </md-input-container>

                    </fieldset>

                    <fieldset style="margin: 10px;">

                        <legend class="md-title">IDENTIFICATION</legend>

                        <md-input-container class="md-block col-md-12" flex-gt-sm>
                            <label>Email</label>
                            <md-icon class="md-primary"><i class="material-icons">email</i></md-icon>
                            <input style="border-left: none; border-top: none; border-right: none;" type="email" required name="email" ng-model="user.email" />
                            <div ng-messages="signupForm.email.$error">
                                <div ng-message="email">Entrez un email valide.</div>
                                <div ng-message="required">Ce champ est requis.</div>
                            </div>
                        </md-input-container>

                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Mot de passe</label>
                            <md-icon class="md-primary"><i class="material-icons">lock</i></md-icon>
                            <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="user.password" equals="{{user.confirm_password}}" ng-pattern="/^(?=.*?[0-9])(?=.*?[a-z])(?=.*?[A-Z]).{8,}/" />
                            <div ng-messages="signupForm.password.$error">
                                <div ng-message="required">Un mot de passe est requis.</div>
                                <div ng-message="pattern">Le mot de passe n'est pas assez fort. Le mot de passe doit comporter au moins huit caractères, au moins une lettre majuscule, une lettre minuscule et un chiffre.</div>
                                <div ng-message="equals">Les mots de passe ne correspondent pas.</div>
                            </div>
                        </md-input-container>

                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Confirmer mot de passe</label>
                            <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="confirm_password" ng-model="user.confirm_password" equals="{{user.password}}" />
                            <div ng-messages="signupForm.confirm_password.$error">
                                <div ng-message="required">Vous devez confirmer votre mot de passe.</div>
                                <div ng-message="equals">Les mots de passe ne correspondent pas.</div>
                            </div>
                        </md-input-container>

                        <md-input-container class="md-block col-md-12" flex-gt-sm>
                            <label>Question secrète</label>
                            <md-select ng-model="user.security_question_id">
                                <md-option ng-value="$index" ng-repeat="question in securityQuestions">{{ question.name }}</md-option>
                            </md-select>
                        </md-input-container>

                        <md-input-container class="md-block col-md-12" flex-gt-sm>
                            <label>Reponse</label>
                            <input required name="response" ng-model="user.security_question_answer" />
                            <div ng-messages="signupForm.response.$error" ng-if="!showHints">
                                <div ng-message="required">Une réponse de sécurité est nécessaire..</div>
                            </div>
                        </md-input-container>

                    </fieldset>

                    <p style="text-align: center;">
                        Vous avez deja un compte? 
                        <a href="<?php echo site_url("/account/login") ?>" >Se Connecter</a>
                    </p>

                    <p class="pg_connex " style="text-align: center;">
                        <a  href  onclick="window.open('<?php echo base_url("/assets/files/terms_and_conditions.pdf")?>', '_blank', 'fullscreen=yes'); return false;">Terme et Condition</a>
                    </p>

                    <div class="col-sm-12">
                        <md-button class="md-raised md-primary pull-right" type="submit">
                            &nbsp S'enregister
                        </md-button>
                    </div>

                </form>
            </md-content>
        </div> 
     </div> 
    
</div>
    
