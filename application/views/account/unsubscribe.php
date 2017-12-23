<script src="<?php echo base_url("assets/js/unsubscribe-controller.js")?>"></script>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<div id="admin-container" class="container" ng-controller="UnsubscribeController" ng-cloak>
    
    
   <div id="loginbox" class="mainbox col-md-8 col-md-offset-2 col-sm-12">
       
        <div class="panel panel-info" >
          
            <div class="panel-heading">
               <div class="panel-title">Se désabonner</div>
            </div>
            
            <div style="padding-top:30px; margin-top: " class="panel-body">
            
                <div class="alert alert-danger col-sm-12" ng-show="unsubscribeErrorMessage">
                   <p>{{unsubscribeErrorMessage}}</p>
                </div>

                <div class="alert alert-success col-sm-12" ng-show="subscribed">
                   <p>{{unsubscribeSuccessMessage}}</p>
                </div>
                
                <div ng-hide="subscribed">
                    <p class="md-otiprix-text" style="text-align: center; margin-bottom: 30px;">Êtes-vous sûr de vouloir vous désabonner {{userEmail}} de Otiprix?. </p>
                
                    <div class="col-sm-12 col-md-12">

                        <div class="row" style="margin-bottom: 30px;">

                            <div class="col-sm-12 col-md-6">
                                <div style="text-align:center;">
                                    <md-button class="md-raised md-warn" style="margin: 0 auto;" ng-click="gotoHome()">
                                        NON
                                    </md-button>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-6">
                                <div style="text-align:center;">
                                    <md-button class="md-raised md-otiprix" style="margin: 0 auto;" ng-click="unsubscribe()">
                                        OUI
                                    </md-button>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                
                

                    
            </div>
          
      </div>
   </div>
</div>