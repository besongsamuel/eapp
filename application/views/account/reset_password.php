

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<div id="admin-container" class="container otiprix-section" ng-controller="ResetPasswordController" ng-cloak>
    
    
    <div class="row justify-content-center">
       
        <div class="card">
            
            <md-toolbar class="md-primary">
                <div>
                    <h2 class="md-toolbar-tools">Réinitialiser le mot de passe</h2>
                </div>
            </md-toolbar>
            
            <div class="card-body">
            
                <div class="alert alert-danger col-sm-12" ng-show="resetPasswordErrorMessage">
                   <p>{{resetPasswordErrorMessage}}</p>
                </div>

                <div class="alert alert-success col-sm-12" ng-show="resetPasswordSuccessMessage">
                   <p>{{resetPasswordSuccessMessage}}</p>
                </div>
            
                <form name="resetPasswordForm" class="form-horizontal" role="form" ng-submit="resetPassword()" novalidate>

                    <p style="text-align: center;">Veuillez entrer un nouveau mot de passe. </p>

                    <md-input-container class="md-block col-sm-12" flex-gt-sm>
                        <label>Mot de passe</label>
                        <md-icon class="md-primary"><i class="material-icons">lock</i></md-icon>
                        <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="password" equals="{{confirm_password}}" ng-pattern="/^(?=.*?[0-9])(?=.*?[a-z])(?=.*?[A-Z]).{8,}/" />
                        <div ng-messages="resetPasswordForm.password.$error">
                            <div ng-message="required">Un mot de passe est requis.</div>
                            <div ng-message="pattern">Le mot de passe n'est pas assez fort. Le mot de passe doit comporter au moins 8 caractères et doit contenir un nombre, un caractère et un caractère spécial.</div>
                            <div ng-message="equals">Les mots de passe ne correspondent pas.</div>
                        </div>
                    </md-input-container>

                    <md-input-container class="md-block col-sm-12" flex-gt-sm>
                        <label>Confirmer mot de passe</label>
                        <md-icon class="md-primary"><i class="material-icons">lock</i></md-icon>
                        <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="confirm_password" ng-model="confirm_password" equals="{{password}}" />
                        <div ng-messages="resetPasswordForm.confirm_password.$error">
                            <div ng-message="required">Vous devez confirmer votre mot de passe.</div>
                            <div ng-message="equals">Les mots de passe ne correspondent pas.</div>
                        </div>
                    </md-input-container>

                    <div class="col-sm-12">
                      <md-button class="md-raised md-primary pull-right" type="submit">
                          Changer
                      </md-button>
                    </div>

                </form>
        </div>
          
      </div>
   </div>
</div>

