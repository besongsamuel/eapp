<script src="<?php echo base_url("assets/js/error-controller.js")?>"></script>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<div id="admin-container" class="container" ng-controller="ErrorController" ng-cloak>
    
    
   <div class="mainbox col-md-8 col-md-offset-2 col-sm-12">
       
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