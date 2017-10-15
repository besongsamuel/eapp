<!DOCTYPE html>

<link rel="stylesheet" href="<?php echo base_url("assets/css/intlTelInput.css")?>">
<script src="<?php echo base_url("assets/js/intlTelInput.js")?>"></script>
<script src="<?php echo base_url("assets/js/utils.js")?>"></script>
<script>
$(document).ready(function()
{
    $("#phone").intlTelInput({utilsScript : "<?php echo base_url("assets/js/utils.js")?>"});
});
</script>

<script>
    $(document).ready(function()
    {
        var scope = angular.element($("#admin-container")).scope();
    
        scope.$apply(function()
        {
           scope.load_icons(); 
           
           scope.getUserProductList();
           
           scope.retailers = JSON.parse('<?php echo $retailers; ?>');
           
           scope.enterVerificationNumber = true;
           
            if(sessionStorage.getItem("registered_email") || scope.isUserLogged)
            {
                if(sessionStorage.getItem("registered_email"))
                {
                    scope.registered_email = sessionStorage.getItem("registered_email");
                    window.sessionStorage.removeItem("registered_email");
                }
                else
                {
                    scope.registered_email = scope.loggedUser.email;
                }
            }
            else
            {
                // redirect to home page
                window.location = scope.site_url.concat("/home");
            }
        });
        
        scope.optimizations = [];
        if(scope.isUserLogged)
        {
            var data = 
            {
                label : "Économies général",
                value : optimization_avg(scope.loggedUser.optimizations.overall),
                count : items_count(scope.loggedUser.optimizations.overall)
            };
            
            scope.optimizations.push(data);
            
            var data = 
            {
                label : "Économies cette semaine",
                value : optimization_avg(scope.loggedUser.optimizations.currentWeek),
                count : items_count(scope.loggedUser.optimizations.currentWeek)
            };
            
            scope.optimizations.push(data);
            
            
            var data = 
            {
                label : "Économies ce mois",
                value : optimization_avg(scope.loggedUser.optimizations.currentMonth),
                count : items_count(scope.loggedUser.optimizations.currentMonth)
            };
            
            scope.optimizations.push(data);
            
            var data = 
            {
                label : "Économies cette année",
                value : optimization_avg(scope.loggedUser.optimizations.currentYear),
                count : items_count(scope.loggedUser.optimizations.currentYear)
            };
            
            scope.optimizations.push(data);
            
        }
        
    });
    
    function optimization_avg(list)
    {
        var average = 0;
        var count = 0;
        var display = "-";
        
        for(var i in list)
        {
            average += parseFloat(list[i].price_optimization);
            count++;
        }
        
        if(parseFloat(average) !== 0)
        {
            display = parseInt(average / count);
        }
        
        return display;
    }
    
    function items_count(list)
    {
        var average = 0;
        var display = "-";
        var count = 0;
        
        for(var i in list)
        {
            var items = JSON.parse(list[i].items);
            average += items.length;
            count++;
        }
        
        if(parseFloat(average) !== 0)
        {
            display = parseInt(average / count);
        }
        
        return display;
    }
    
</script>

<div id="admin-container" ng-controller="AccountController">
    <md-tabs md-dynamic-height md-border-bottom class="container" layout-padding>
        
        <md-content>
            
            <md-tab label="Historique de mes économies">
                <div class="md-padding">
                <md-list>
                    <md-list-item class="md-3-line" ng-repeat="item in optimizations">
                      <div class="md-list-item-text"  style="margin-bottom: 10px;">
                        <h3 style="color : #1abc9c;">{{item.label}}</h3>
                        <h4>Économies moyen : <b style="color : red;"><span ng-show="item.value != '-'">$ CAD</span> {{item.value}}</b></h4>
                        <p>Nombre moyen de produits par panier : <b>{{item.count}}</b></p>
                      </div>
                      <md-button class="md-secondary md-otiprix">Voir détails</md-button>
                      <md-divider ng-if="!$last"></md-divider>
                    </md-list-item>
                </md-list>
                </div>
            </md-tab>
            
            <md-tab label="Modifier ma liste d’épicerie">
                <div id="groceryListContainer" ng-include="'<?php echo base_url(); ?>/assets/templates/user_grocery_list.html'"></div>
            </md-tab>
            
            <md-tab label="Modifier mes renseignements personnels">

                <div class="alert alert-danger" ng-show="saveProfileError">
                    <strong>Erreur!</strong> {{saveProfileErrorMessage}}.
                </div>
                
                <div class="alert alert-success" ng-show="saveProfileSucess">
                    <strong>Success!</strong> {{saveProfileSuccessMessage}}
                </div>

                <div layout-padding>

                    <form name="userInfoForm" novalidate ng-submit="saveProfile()">
                        <md-input-container class="md-block col-md-12 col-sm-12" flex-gt-sm>
                            <label>Email</label>
                            <md-icon style="color: #1abc9c;"><i class="material-icons">email</i></md-icon>
                            <input disabled="true" style="border-left: none; border-top: none; border-right: none;" type="email" name="email" ng-model="loggedUser.email" />
                        </md-input-container>
                        <!-- -->
                        <md-input-container class="md-block col-md-6 col-sm-12" flex-gt-sm>
                            <label>Prenom</label>
                            <md-icon style="color: #1abc9c;"><i class="material-icons">person</i></md-icon>
                            <input name="profile[firstname]" ng-model="loggedUser.profile.firstname" />
                        </md-input-container>
                        <!-- -->
                        <md-input-container class="md-block col-md-6 col-sm-12" flex-gt-sm>
                            <label>Nom</label>
                            <md-icon style="color: #1abc9c;"><i class="material-icons">person</i></md-icon>
                            <input required name="profile[lastname]" ng-model="loggedUser.profile.lastname" />
                            <div ng-messages="userInfoForm.lastname.$error">
                                <div ng-message="required">Vous devez entrer au moins un nom</div>
                            </div>
                        </md-input-container>

                        <!--Select the country and state origin of the product-->
                        <country-state-select country="loggedUser.profile.country" flag="icons.flag" country-state="loggedUser.profile.state" show-hints="showHints"></country-state-select>

                        <md-input-container class="md-block col-md-12 col-sm-12" flex-gt-sm>
                            <label>Adresse</label>
                            <md-icon style="color: #1abc9c;"><i class="material-icons">place</i></md-icon>
                            <input required name="profile[address]" ng-model="loggedUser.profile.address" />
                            <div ng-messages="userInfoForm.address.$error">
                                <div ng-message="required">Vous devez entrer une adresse</div>
                            </div>
                        </md-input-container>
                        <!-- -->
                        <md-input-container class="md-block col-md-6 col-sm-12" flex-gt-sm>
                            <label>City</label>
                            <md-icon style="color: #1abc9c;"><i class="material-icons">place</i></md-icon>
                            <input required name="profile[city]" ng-model="loggedUser.profile.city" />
                            <div ng-messages="userInfoForm.city.$error">
                                <div ng-message="required">Vous devex entrer une ville</div>
                            </div>
                        </md-input-container>
                        <md-input-container class="md-block col-md-6 col-sm-12" flex-gt-sm>
                            <label>Code Postal</label>
                            <md-icon style="color: #1abc9c;"><i class="material-icons">place</i></md-icon>
                            <input required name="profile[postcode]" ng-model="loggedUser.profile.postcode" />
                            <div ng-messages="userInfoForm.postcode.$error">
                                <div ng-message="required">Veillez entrer votre code postale</div>
                            </div>
                        </md-input-container>

                        <div layout-padding>
                            <md-subheader class="md-warn" ng-hide="loggedUser.phone_verified == 1">Vérifier votre numéro de téléphone</md-subheader>
                    
                            <div class="col-sm-12" ng-show="enterVerificationNumber">
                                <div class="alert alert-danger" ng-show="phoneNumberError">
                                    <strong>Erreur!</strong> {{phoneNumberError}}.
                                </div>
                                <div class="alert alert-success" ng-show="validateCodeMessage">
                                    <strong>Success!</strong> {{validateCodeMessage}}
                                </div>
                                <p style="text-align: center; color: green;" ng-show="loggedUser.phone_verified == 1">Verified : {{loggedUser.phone}}</p>
                                <p style="text-align: center;">Veuillez entrer ci-dessous un numéro de téléphone où nous vous enverrons le code de vérification.</p>
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
                                <div class="alert alert-danger" ng-show="validateCodeMessage">
                                    <strong>Erreur!</strong> {{validateCodeMessage}}
                                </div>
                                <p style="text-align: center;">Veuillez entrer ci-dessous le code que vous avez reçu ou cliquez sur <a href ng-click="enterVerificationNumber = true">réessayez</a> pour renvoyer un autre code de vérification.</p>
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

                        <div class="pull-right">
                            <input type="submit" class="btn btn-primary" value="Changer" />
                        </div>
                        
                    </form>
                </div>
            </md-tab>

            <md-tab label="Modifier mes magasins préférés">
                <div class="alert alert-success" ng-show="listChangedSuccess">
                    <strong>Success!</strong> {{listChangedSuccessMessage}}
                </div>
                <div id="select-store-container" ng-include="'<?php echo base_url(); ?>/assets/templates/select-favorite-stores.html'"></div>
                <div class="form-group">
                    <!-- Button -->  
                    <div class="col-md-offset-0 col-md-3 pull-right" style="padding-top:25px;">
                        <md-button id="btn-signup"  ng-click="submit_favorite_stores()" class="md-otiprix md-raised col-md-12">Sélectionner</md-button>
                    </div>
                </div>
            </md-tab>
           
            <md-tab label="Sécurité du compte">
                
                <md-content class="md-padding">
                    
                    <md-subheader class="md-warn">Changer votre mot de passe</md-subheader>
                    
                    <div class="alert alert-danger" ng-show="changePasswordError">
                        <strong>Erreur!</strong> {{changePasswordErrorMessage}}
                    </div>

                    <div class="alert alert-success" ng-show="changePasswordSuccess">
                        <strong>Success!</strong> {{changePasswordSuccessMessage}}
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
                            <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="password" equals="{{confirm_password}}" ng-pattern="/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/" />
                            <div ng-messages="userSecurityForm.password.$error">
                                <div ng-message="required">Un mot de passe est requis.</div>
                                <div ng-message="pattern">Le mot de passe n'est pas assez fort. Le mot de passe doit comporter au moins 8 caractères et doit contenir un nombre, un caractère et un caractère spécial.</div>
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

                        <div class="pull-right">
                            <input type="submit" class="btn btn-primary" value="Changer" />
                        </div>

                    </form>
                </md-content>    
                
                <md-content class="md-padding">
                    <md-subheader class="md-warn">Changer la réponse et la question de sécurité</md-subheader>
                    
                    <form name="securityQuestionForm" ng-submit="changeSecurityQuestion()" novalidate>

                        <div class="alert alert-danger" ng-show="changeSecurityQuestionError">
                            <strong>Erreur!</strong> {{changeSecurityQuestionErrorMessage}}
                        </div>

                        <div class="alert alert-success" ng-show="changeSecurityQuestionSuccess">
                            <strong>Success!</strong> {{changeSecurityQuestionSuccessMessage}}
                        </div>

                        <md-content class="md-padding">
                            <md-input-container class="md-block col-md-12" flex-gt-sm>
                                <label>Question secrète</label>
                                <md-select ng-model="loggedUser.security_question_id">
                                    <md-option ng-value="$index" ng-repeat="question in securityQuestions">{{ question }}</md-option>
                                </md-select>
                            </md-input-container>

                            <md-input-container class="md-block col-md-12" flex-gt-sm>
                                <label>Reponse</label>
                                <input required name="response" ng-model="loggedUser.security_question_answer" />
                                <div ng-messages="securityQuestionForm.response.$error" ng-if="!showHints">
                                    <div ng-message="required">Une réponse de sécurité est nécessaire..</div>
                                </div>
                            </md-input-container>

                            <div class="pull-right">
                                <input type="submit" class="btn btn-primary" value="Changer" />
                            </div>
                        </md-content>

                    </form>
                </md-content>  
                
                
            </md-tab>
        </md-content>
    </md-tabs>
    
   
</div>
 

