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
                
                <form id="signupForm" name="signupForm" class="form-horizontal" novalidate ng-submit="registerCompany()">

                    <div id="error_message" class="alert alert-danger" style="text-align: center;" ng-show="message">
                        <p>{{message}}</p>
                        <span></span>
                    </div>
                    
                    <!-- COMPANY INFORMATION -->
                    <fieldset style="margin: 10px;">

                        <legend>RENSEIGNEMENTS D'ENTREPRISE</legend>
                        
                        <!-- NEQ -->
                        <md-input-container class="md-block col-md-12" flex-gt-sm>
                            <label>NEQ</label>
                            <input required name="neq" ng-model="company.neq" />
                            <div ng-messages="signupForm.neq.$error">
                                <div ng-message="required">Vous devez entrer le NEQ de l'entreprise</div>
                            </div>
                        </md-input-container>
                        
                        <!-- NOM DE L'ENTREPRISE -->
                        <md-input-container class="md-block col-md-12" flex-gt-sm>
                            <label>Nom de l'entreprise</label>
                            <input required name="company_name" ng-model="company.name" />
                            <div ng-messages="signupForm.company_name.$error">
                                <div ng-message="required">Vous devez entrer au moins un nom pour l'entreprise</div>
                            </div>
                        </md-input-container>
                        
                        <image-upload 
                            caption="Ajouter logo" 
                            on-file-removed="onFileRemoved()" 
                            on-file-selected="imageChanged(file)">       
                        </image-upload>                     
            
                    </fieldset>
                    
                    <!-- PROFILE/CONTACT INFORMATION -->
                    <fieldset style="margin: 10px;">
                        
                        <legend>PERSONNE DE CONTACT</legend>

                        <!-- First Name -->
                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Prénom</label>
                            <md-icon class="md-primary"><i class="material-icons">person</i></md-icon>
                            <input name="firstname" ng-model="profile.firstname" />
                        </md-input-container>

                        <!-- Last Name -->
                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Nom</label>
                            <input required name="lastname" ng-model="profile.lastname" />
                            <div ng-messages="signupForm.lastname.$error">
                                <div ng-message="required">Vous devez entrer au moins un nom</div>
                            </div>
                        </md-input-container>
                        
                        <!-- User Address -->
                        <md-input-container class="col-sm-12">
                            <!-- <label>Adresse</label> -->
                            <input 
                                vs-google-autocomplete
                                vs-autocomplete-validator
                                ng-model="profile.address"

                                vs-city="profile.city"
                                vs-state="profile.state"
                                vs-country="profile.country"
                                vs-post-code="profile.postcode"
                                vs-longitude="profile.longitude"
                                vs-latitude="profile.latitude"
                                name="address"
                            >
                        </md-input-container>
                        
                        <!-- City -->
                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Ville</label>
                            <input required name="city" ng-model="profile.city" />
                            <div ng-messages="signupForm.city.$error">
                                <div ng-message="required">Vous devez entrer une ville</div>
                            </div>
                        </md-input-container>
                        
                        <!-- State -->
                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Province</label>
                            <input required name="state" ng-model="profile.state" />
                            <div ng-messages="signupForm.state.$error">
                                <div ng-message="required">Veillez entrer la province</div>
                            </div>
                        </md-input-container>
                        
                        <!-- Country -->
                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Pays</label>
                            <input required name="country" ng-model="profile.country" />
                            <div ng-messages="signupForm.country.$error">
                                <div ng-message="required">Vous devez entrer le pays</div>
                            </div>
                        </md-input-container>
                        
                        <!-- Postcode -->
                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Code Postal</label>
                            <input required name="postcode" ng-model="profile.postcode" />
                            <div ng-messages="signupForm.postcode.$error">
                                <div ng-message="required">Veillez entrer votre code postal</div>
                            </div>
                        </md-input-container>

                    </fieldset>

                    <!-- ACCOUNT INFORMATION -->
                    <fieldset style="margin: 10px;">

                        <legend>IDENTIFICATION</legend>

                        <md-input-container class="md-block col-md-12" flex-gt-sm>
                            <label>Email</label>
                            <md-icon class="md-primary"><i class="material-icons">email</i></md-icon>
                            <input style="border-left: none; border-top: none; border-right: none;" type="email" required name="email" ng-model="account.email" />
                            <div ng-messages="signupForm.email.$error">
                                <div ng-message="email">Entrez un email valide.</div>
                                <div ng-message="required">Ce champ est requis.</div>
                            </div>
                        </md-input-container>

                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Mot de passe</label>
                            <md-icon class="md-primary"><i class="material-icons">lock</i></md-icon>
                            <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="account.password" equals="{{user.confirm_password}}" ng-pattern="/^(?=.*?[0-9])(?=.*?[a-z])(?=.*?[A-Z]).{8,}/" />
                            <div ng-messages="signupForm.password.$error">
                                <div ng-message="required">Un mot de passe est requis.</div>
                                <div ng-message="pattern">Le mot de passe n'est pas assez fort. Le mot de passe doit comporter au moins huit caractères, au moins une lettre majuscule, une lettre minuscule et un chiffre.</div>
                                <div ng-message="equals">Les mots de passe ne correspondent pas.</div>
                            </div>
                        </md-input-container>

                        <md-input-container class="md-block col-md-6" flex-gt-sm>
                            <label>Confirmer mot de passe</label>
                            <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="confirm_password" ng-model="confirm_password" equals="{{account.password}}" />
                            <div ng-messages="signupForm.confirm_password.$error">
                                <div ng-message="required">Vous devez confirmer votre mot de passe.</div>
                                <div ng-message="equals">Les mots de passe ne correspondent pas.</div>
                            </div>
                        </md-input-container>

                        <md-input-container class="md-block col-md-12" flex-gt-sm>
                            <label>Question secrète</label>
                            <md-select ng-model="account.security_question_id">
                                <md-option ng-value="$index" ng-repeat="question in securityQuestions">{{ question.name }}</md-option>
                            </md-select>
                        </md-input-container>

                        <md-input-container class="md-block col-md-12" flex-gt-sm>
                            <label>Reponse</label>
                            <input required name="response" ng-model="account.security_question_answer" />
                            <div ng-messages="signupForm.response.$error" ng-if="!showHints">
                                <div ng-message="required">Une réponse de sécurité est nécessaire..</div>
                            </div>
                        </md-input-container>

                    </fieldset>
                    
                    <div class="col-sm-12">
                        <p style="text-align: center;">
                            Vous avez deja un compte? 
                            <a href="<?php echo site_url("/account/login") ?>" >Se Connecter</a>
                        </p>
                    </div>
                    

                    <p class="pg_connex col-sm-12" style="text-align: center;">
                        <a  href  onclick="window.open('<?php echo base_url("/assets/files/terms_and_conditions.pdf")?>', '_blank', 'fullscreen=yes'); return false;">Terme et Condition</a>
                    </p>

                    <div class="col-sm-12">
                        <md-progress-circular ng-show="creatingAccount"  class="md-hue-2 pull-right" md-diameter="20px" style="margin-top: 12px;"></md-progress-circular>
                        <md-button type="submit" class="md-raised md-primary pull-right" type="submit">
                            &nbsp Créer Entreprise 
                        </md-button>
                        
                    </div>

                </form>
            </md-content>
        </div> 
     </div> 
    
</div>
    
