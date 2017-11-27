<script src="<?php echo base_url("assets/js/account-created-controller.js")?>"></script>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<div id="admin-container" class="container" ng-controller="AccountCreatedController" ng-cloak>
    
    <div class="mainbox col-md-8 col-md-offset-2 col-sm-12" ng-show="visible">
       
        <div class="panel panel-info" >
          
            <div class="panel-heading">
               <div class="panel-title">Compte créé</div>
            </div>
            
            <div style="padding-top:30px" class="panel-body">
            
            <div class="alert alert-success col-sm-12" ng-show="message">
                <p style="text-align: center;">{{message}}</p>
            </div>
            <p style="text-align: center;">Cliquez sur le bouton ci-dessous pour configurer vos magasins préférés.</p>
            <div style="text-align: center;" class="col-sm-12">
                <md-input-container>
                    <md-button class="md-raised md-otiprix" ng-click="gotoAccount()">
                        Configurer
                    </md-button>
                </md-input-container>
            </div>
            <br>
            <h3 style="text-align: center;">Ou</h3>
            <div style="text-align: center;" class="col-sm-12">
                <md-input-container>
                    <md-button class="md-raised md-otiprix" ng-click="gotoHome()">
                        Commencez a reduire votre epicerie
                    </md-button>
                </md-input-container>
            </div>

        </div>
          
      </div>
   </div>
    
    <div class="mainbox col-md-8 col-md-offset-2 col-sm-12" ng-show="invalid">
       
        <div class="panel panel-info" >
          
            <div class="panel-heading">
               <div class="panel-title">Erreur</div>
            </div>
            
            <div style="padding-top:30px" class="panel-body">
            
            <div class="alert alert-danger col-sm-12" ng-show="message">
                <p style="text-align: center;">{{message}}</p>
            </div>

        </div>
          
      </div>
   </div>
</div>