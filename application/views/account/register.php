<link href="<?php echo base_url("assets/css/register.css"); ?>" rel="stylesheet">


<div id="admin-container" class="container loginbox" ng-controller="AccountController">    

        <div id="signupbox" class="mainbox">
            <div class="panel panel-info">
                     
                <md-toolbar style="background-color: #1abc9c;">
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

                            <legend>FORMULAIRE D'INSCRIPTION</legend>
                            
                            <!-- -->
                            <md-input-container class="md-block col-md-6" flex-gt-sm>
                                <label>Prenom</label>
                                <md-icon style="color: #1abc9c;"><i class="material-icons">person</i></md-icon>
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

                            <!--Select the country and state origin of the product-->
                            <country-state-select country="user.country" flag="icons.flag" country-state="user.state" show-hints="showHints"></country-state-select>

                            <md-input-container class="md-block col-md-12" flex-gt-sm>
                                <label>Adresse</label>
                                <md-icon style="color: #1abc9c;"><i class="material-icons">place</i></md-icon>
                                <input required name="address" ng-model="user.address" />
                                <div ng-messages="signupForm.address.$error">
                                    <div ng-message="required">Vous devez entrer une adresse</div>
                                </div>
                            </md-input-container>
                            <!-- -->
                            <md-input-container class="md-block col-md-6" flex-gt-sm>
                                <label>City</label>
                                <input required name="city" ng-model="user.city" />
                                <div ng-messages="signupForm.city.$error">
                                    <div ng-message="required">Vous devex entrer une ville</div>
                                </div>
                            </md-input-container>
                            <md-input-container class="md-block col-md-6" flex-gt-sm>
                                <label>Code Postal</label>
                                <input required name="postcode" ng-model="user.postcode" />
                                <div ng-messages="signupForm.postcode.$error">
                                    <div ng-message="required">Veillez entrer votre code postale</div>
                                </div>
                            </md-input-container>

                        </fieldset>
                                
                        <fieldset style="margin: 10px;">

                            <legend>IDENTIFICATION</legend>

                            <md-input-container class="md-block col-md-12" flex-gt-sm>
                                <label>Email</label>
                                <md-icon style="color: #1abc9c;"><i class="material-icons">email</i></md-icon>
                                <input style="border-left: none; border-top: none; border-right: none;" type="email" required name="email" ng-model="user.email" />
                                <div ng-messages="signupForm.email.$error">
                                    <div ng-message="email">Entrez un email valide.</div>
                                    <div ng-message="required">Ce champ est requis.</div>
                                </div>
                            </md-input-container>

                            <md-input-container class="md-block col-md-6" flex-gt-sm>
                                <label>Mot de passe</label>
                                <md-icon style="color: #1abc9c;"><i class="material-icons">lock</i></md-icon>
                                <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="user.password" equals="{{user.confirm_password}}" ng-pattern="/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/" />
                                <div ng-messages="signupForm.password.$error">
                                    <div ng-message="required">Un mot de passe est requis.</div>
                                    <div ng-message="pattern">Le mot de passe n'est pas assez fort. Le mot de passe doit comporter au moins 8 caractères et doit contenir un nombre, un caractère et un caractère spécial.</div>
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
                            <md-button class="md-raised md-otiprix pull-right" type="submit">
                                &nbsp S'enregister
                            </md-button>
                        </div>
                                
                    </form>
                </md-content>
            </div> 
         </div> 
    </div>
    
