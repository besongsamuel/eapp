<script>
$(document).ready(function(){
    
    var scope = angular.element($("#admin-container")).scope();
    
    scope.$apply(function()
    {
       scope.load_icons(); 
    });
})
</script>


<div id="admin-container" class="container" ng-controller="AccountController">    

        <div id="signupbox" style=" margin-top:50px" class="mainbox">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">FORMULAIRE D'INSCRIPTION</div>
                    <div style="float:right; font-size: 85%; position: relative; top:-10px; ">Vous avez un compte!  <a id="signinlink" href="http://<?php echo addslashes(site_url("account/login")); ?>">Se connecter</a></div>
                </div>  
                <div class="panel-body" >
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
                                <md-icon style="background: #1abc9c;" md-svg-src="{{icons.person | trustUrl}}"></md-icon>
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
                                <md-icon style="background: #1abc9c;" md-svg-src="{{icons.place | trustUrl}}"></md-icon>
                                <input required name="address" ng-model="user.address" />
                                <div ng-messages="signupForm.address.$error" ng-if="!showHints">
                                    <div ng-message="required">Vous devez entrer une adresse</div>
                                </div>
                            </md-input-container>
                            <!-- -->
                            <md-input-container class="md-block col-md-6" flex-gt-sm>
                                <label>City</label>
                                <input required name="city" ng-model="user.city" />
                                <div ng-messages="signupForm.city.$error" ng-if="!showHints">
                                    <div ng-message="required">Vous devex entrer une ville</div>
                                </div>
                            </md-input-container>
                            <md-input-container class="md-block col-md-6" flex-gt-sm>
                                <label>Code Postal</label>
                                <input required name="postcode" ng-model="user.postcode" />
                                <div ng-messages="signupForm.postcode.$error" ng-if="!showHints">
                                    <div ng-message="required">Veillez entrer votre code postale</div>
                                </div>
                            </md-input-container>

                            <md-input-container class="md-block col-md-6" flex-gt-sm>
                                <label>Numbero de telephone principale</label>
                                <md-icon style="background: #1abc9c;" md-svg-src="{{icons.phone | trustUrl}}"></md-icon>
                                <input name="phone1" ng-model="user.phone1" />
                            </md-input-container>

                            <md-input-container class="md-block col-md-6" flex-gt-sm>
                                <label>Numbero de telephone secondaire</label>
                                <input name="phone2" ng-model="user.phone2" />
                            </md-input-container>

                        </fieldset>
                                
                        <fieldset style="margin: 10px;">

                            <legend>IDENTIFICATION</legend>

                            <md-input-container class="md-block col-md-12" flex-gt-sm>
                                <label>Email</label>
                                <md-icon style="background: #1abc9c;" md-svg-src="{{icons.email | trustUrl}}"></md-icon>
                                <input style="border-left: none; border-top: none; border-right: none;" type="email" required name="email" ng-model="user.email" />
                                <div ng-messages="signupForm.email.$error">
                                    <div ng-message="email">Entrez un email valide.</div>
                                    <div ng-message="required">Ce champ est requis.</div>
                                </div>
                            </md-input-container>

                            <md-input-container class="md-block col-md-6" flex-gt-sm>
                                <label>Mot de passe</label>
                                <md-icon style="background: #1abc9c;" md-svg-src="{{icons.lock | trustUrl}}"></md-icon>
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
                                    <md-option ng-value="$index" ng-repeat="question in securityQuestions">{{ question }}</md-option>
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
                                
                        <div class="form-group" >
                            <!-- Boutton -->                                        
                            <div class="col-md-offset-3 col-md-9" style=" margin-top:20px;">
                                <button id="btn-signup" type="submit" class="btn btn-info col-md-12"><i class="icon-hand-right"></i> &nbsp S'enregister</button>
                            </div>
                        </div>
                                
                        <div class="form-group" style="border-top: 1px solid #999; padding-top:20px">
                            <!-- Button -->                                        
                            <div class="col-md-offset-3 col-md-9">
                                <button id="fbsignup" type="button" class="btn btn-primary col-md-12"><i class="icon-facebook"></i></i> &nbsp S'enregistrer avec facebook</button>
                            </div>
                        </div>
                                
                        <div class="col-md-3  condition" style=" margin-top:20px;"> <!-- Lien vers page Terme -->
                            <p class="pg_connex ">
                                <a href="#">Terme et Condition</a>
                            </p>
                        </div>
                                
                    </form>
                </div>
            </div> 
         </div> 
    </div>
    
