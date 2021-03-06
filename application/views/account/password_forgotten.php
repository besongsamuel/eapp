

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<div class="container mainbox" ng-controller="PasswordForgottenController" ng-cloak>
    
    <div class="row" style="margin: 10px;">
        <a href="<?php echo site_url("home/goback"); ?>">Retour</a>
    </div>
    
    <div class="justify-content-center">
        
        <div class="card">

            <md-toolbar class="md-primary">
                <div>
                    <h2 class="md-toolbar-tools">Mot de passe oublié</h2>
                </div>
            </md-toolbar>

            <div class="card-body">

                <div class="alert alert-danger col-sm-12" ng-show="passwordForgottenErrorMessage">
                   <p>{{passwordForgottenErrorMessage}}</p>
                </div>

                <div class="alert alert-success col-sm-12" ng-show="passwordForgottenSuccessMessage">
                   <p>{{passwordForgottenSuccessMessage}}</p>
                </div>

                <form name="passwordForgottenForm" class="form-horizontal" role="form" ng-submit="sendPasswordReset()" novalidate>

                    <p style="text-align: center;">Veuillez entrer votre adresse email pour réinitialiser votre mot de passe. </p>

                    <md-input-container class="md-block col-md-12" flex-gt-sm>
                        <label>Email</label>
                        <md-icon class="md-primary"><i class="material-icons">email</i></md-icon>
                        <input required name="email" ng-model="email" />
                        <div ng-messages="passwordForgottenForm.email.$error">
                            <div ng-message="required">Veillez entrer votre addresse email.</div>
                        </div>
                    </md-input-container>

                    <div class="col-sm-12">
                      <md-button class="md-raised md-primary pull-right" type="submit">
                          Envoyer
                      </md-button>
                    </div>

                </form>
        </div>

       </div>
        
    </div>
    
    
</div>

