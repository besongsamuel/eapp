
<div class="container" ng-controller="AccountController">    

        <div id="signupbox" style=" margin-top:50px" class="mainbox col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">FORMULAIRE D'INSCRIPTION</div>
                            <div style="float:right; font-size: 85%; position: relative; top:-10px; ">Vous avez un compte!  <a id="signinlink" href="#" onclick="$('#signupbox').hide(); $('#loginbox').show()">Se connecter</a></div>
                        </div>  
                        <div class="panel-body" >
                            <form id="signupForm" class="form-horizontal" role="form" novalidate ng-submit="register()">
                                
                                <div id="alert_enregist" style="display:none" class="alert alert-danger">
                                    <p>Erreur:</p>
                                    <span></span>
                                </div>
                                
                                <fieldset>
                                    
                                    <legend>FORMULAIRE D'INSCRIPTION</legend>
                                    <!-- -->
                                    <md-input-container class="md-block col-md-6" flex-gt-sm>
                                        <label>Prenom</label>
                                        <md-icon md-svg-src="http://{{base_url}}/assets/icons/ic_person_black_24px.svg"></md-icon>
                                    <input name="firstname" ng-model="user.firstname" />
                                        <div class="hint" ng-if="showHints">Entrez votre prenom</div>
                                    </md-input-container>
                                    <!-- -->
                                    <md-input-container class="md-block col-md-6" flex-gt-sm>
                                        <label>Nom</label>
                                        <input required name="lastname" ng-model="user.lastname" />
                                        <div class="hint" ng-if="showHints">Entrez votre nom de famille</div>
                                        <div ng-messages="signupForm.lastname.$error" ng-if="!showHints">
                                            <div ng-message="required">Vous devex entrer au moins un nom</div>
                                        </div>
                                    </md-input-container>
              
                                    <!--Select the country and state origin of the product-->
                                    <country-state-select country="user.country" state="user.state" class="col-sm-12"</country-state-select>

                                    <md-input-container class="md-block col-md-12" flex-gt-sm>
                                        <label>Adresse</label>
                                        <md-icon md-svg-src="http://{{base_url}}/assets/icons/ic_place_black_24px.svg"></md-icon>
                                        <input required name="address" ng-model="user.address" />
                                        <div class="hint" ng-if="showHints">Entrez votre adresse actuelle</div>
                                        <div ng-messages="signupForm.address.$error" ng-if="!showHints">
                                            <div ng-message="required">Vous devez entrer une adresse</div>
                                        </div>
                                    </md-input-container>

                                    <md-input-container class="md-block col-md-6" flex-gt-sm>
                                        <label>Ville</label>
                                            <md-icon md-svg-src="http://{{base_url}}/assets/icons/ic_home_black_24px.svg"></md-icon>
                                        <input required name="ville" ng-model="user.ville" />
                                        <div class="hint" ng-if="showHints">Entrez votre ville actuelle</div>
                                        <div ng-messages="signupForm.ville.$error" ng-if="!showHints">
                                            <div ng-message="required">Vous devez entrer une ville</div>
                                        </div>
                                    </md-input-container>

                                    <md-input-container class="md-block col-md-6" flex-gt-sm>
                                        <label>Code Postal</label>
                                        <input required name="ville" ng-model="user.postcode" />
                                        <div class="hint" ng-if="showHints">Entrez votre code postale</div>
                                        <div ng-messages="signupForm.postcode.$error" ng-if="!showHints">
                                            <div ng-message="required"></div>
                                        </div>
                                    </md-input-container>

                                    <md-input-container class="md-block col-md-6" flex-gt-sm>
                                        <label>Numbero de telephone N# 1</label>
                                        <md-icon md-svg-src="http://{{base_url}}/assets/icons/ic_local_phone_black_24px.svg"></md-icon>
                                        <input name="phone1" ng-model="user.phone1" />
                                        <div class="hint" ng-if="showHints">Entrez votre numéro de téléphone principal</div>   
                                    </md-input-container>

                                    <md-input-container class="md-block col-md-6" flex-gt-sm>
                                        <label>Numbero de telephone N# 2</label>
                                        <input name="phone2" ng-model="user.phone2" />
                                        <div class="hint" ng-if="showHints">Entrez votre numéro de téléphone secondaire</div>
                                    </md-input-container>
                                
                                </fieldset>
                                
                                <fieldset>
                                    
                                    <legend>IDENTIFICATION</legend>
                                
                                    <md-input-container class="md-block col-md-12" flex-gt-sm>
                                        <label>Email</label>
                                        <md-icon md-svg-src="http://{{base_url}}/assets/icons/ic_email_black_24px.svg"></md-icon>
                                        <input md-maxlength="30" required name="email" ng-model="user.email" />
                                        <div class="hint" ng-if="showHints">Entrez votre nom d'utilisateur ou votre email</div>
                                        <div ng-messages="signupForm.email.$error" ng-if="!showHints">
                                            <div ng-message="required">Name is required.</div>
                                            <div ng-message="md-maxlength">The username has to be less than 30 characters.</div>
                                        </div>
                                    </md-input-container>

                                    <md-input-container class="md-block col-md-6" flex-gt-sm>
                                        <label>Mot de passe</label>
                                        <md-icon md-svg-src="http://{{base_url}}/assets/icons/ic_lock_black_24px.svg"></md-icon>
                                        <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="user.password" />
                                        <div class="hint" ng-if="showHints">Entrez un mot de passe avec au moins 8 caractères</div>
                                            <div ng-messages="signupForm.password.$error" ng-if="!showHints">
                                            <div ng-message="required">Un mot de passe est requis.</div>
                                        </div>
                                    </md-input-container>

                                    <md-input-container class="md-block col-md-6" flex-gt-sm>
                                        <label>Confirmer ot de passe</label>
                                        <md-icon md-svg-src="http://{{base_url}}/assets/icons/ic_lock_black_24px.svg"></md-icon>
                                        <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="user.password" />
                                        <div class="hint" ng-if="showHints">Entrez un mot de passe avec au moins 8 caractères</div>
                                            <div ng-messages="signupForm.password.$error" ng-if="!showHints">
                                            <div ng-message="required">Un mot de passe est requis.</div>
                                        </div>
                                    </md-input-container>

                                    <md-input-container class="md-block col-md-12" flex-gt-sm>
                                        <label>Question secrète</label>
                                        <md-select ng-model="user.province">
                                            <md-option ng-value="question" ng-repeat="question in securityQuestions">{{ question }}</md-option>
                                        </md-select>
                                    </md-input-container>
                                    
                                    <md-input-container class="md-block col-md-12" flex-gt-sm>
                                        <label>Reponse</label>
                                        <input md-maxlength="30" required name="response" ng-model="user.response" />
                                        <div class="hint" ng-if="showHints">Entrez votre nom d'utilisateur ou votre email</div>
                                        <div ng-messages="signupForm.response.$error" ng-if="!showHints">
                                            <div ng-message="required">Name is required.</div>
                                            <div ng-message="md-maxlength">The username has to be less than 30 characters.</div>
                                        </div>
                                    </md-input-container>
                                
                                </fieldset>
                                

                                <div class="form-group" >
                                    <!-- Boutton -->                                        
                                    <div class="col-md-offset-3 col-md-9" style=" margin-top:20px;">
                                        <button id="btn-signup" type="button" class="btn btn-info col-md-12"><i class="icon-hand-right"></i> &nbsp S'enregister</button>
                                        
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
    
