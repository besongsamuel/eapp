

<link href="<?php echo base_url("assets/css/login.css"); ?>" rel="stylesheet">


<div id="admin-container" class="container mainbox" ng-controller="AccountController" ng-cloak>
   <div id="loginbox" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
      <div class="panel panel-info" >
        
        <md-toolbar style="background-color: #1abc9c;">
            <div>
                <h2 class="md-toolbar-tools">Se connecter</h2>
            </div>
        </md-toolbar>  
          
        <md-content class="login-box">
            
            <div id="login-alert" class="alert alert-danger col-sm-12" ng-show="message">
               <p>{{message}}</p>
            </div>
            
            <form name="loginForm" class="form-horizontal" role="form" ng-submit="login()" novalidate>

                <md-input-container class="md-block col-md-12" flex-gt-sm>
                    <label>Email</label>
                    <md-icon style="color: #1abc9c;"><i class="material-icons">email</i></md-icon>
                    <input required name="email" ng-model="user.email" />
                    <div ng-messages="loginForm.email.$error">
                        <div ng-message="required">Veillez entrer votre addresse email.</div>
                    </div>
                </md-input-container>

                <md-input-container class="md-block col-md-12" flex-gt-sm md-no-float>
                    <label class="md-no-float">Mot de passe</label>
                    <md-icon style="color: #1abc9c;"><i class="material-icons">lock</i></md-icon>
                    <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="user.password" />
                    <div ng-messages="loginForm.password.$error">
                        <div ng-message="required">Veillez entrer un mot de passe.</div>
                    </div>
                </md-input-container>
                
                <div class="col-sm-12">
                    <md-checkbox class="pull-left" ng-model="user.rememberme" aria-label="Rester connecté">
                        Rester connecté
                    </md-checkbox>
                </div>
                
                <div class="col-sm-12">
                  <md-button class="md-raised md-primary pull-right" type="submit">
                      Se connecter
                  </md-button>
                </div>
                
                <p style="text-align: center;"><a href="<?php echo site_url("/account/password_forgotten") ?>">Mot de passe oublié?</a></p>
                <p style="text-align: center;">
                    Vous n'avez pas encore de compte? 
                    <a href="<?php echo site_url("/account/register") ?>" >Créer un compte</a>
                </p>
                
                
          </form>
       </md-content>
      </div>
   </div>
   </form>
</div>
