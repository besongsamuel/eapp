<!DOCTYPE html>

<link rel="stylesheet" href="<?php echo base_url("assets/css/change-location.css")?>">

<div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Choisissez l'option de localisation</h2>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Page title area -->

<div class="container change-location-container" ng-controller="ChangeLocationController as ctrl" ng-cloak>
    
    <a href="<?php echo site_url("home/goback"); ?>">Retour</a>
    
    <div class="alert alert-success" ng-show="message">
        <strong>Succès!</strong> Votre adresse actuelle a été modifiée.
    </div>
    
    <div class="row col-md-offset-1">
        
        <div class="col-md-4 col-sm-6 round-border">
            <h4>Geolocation</h4>
            <p>La géolocalisation nous permet d'obtenir votre position géographique. Dans ce cas, nous identifions votre ville pour diffuser les promotions disponibles dans ce domaine.</p>
            
            <div class="col-sm-12">
                <md-button class="md-raised md-primary pull-right" ng-click="ctrl.getUserCoordinates()">
                    Me géolocaliser
                </md-button>
            </div>
            
            
        </div>
        <div class="col-sm-2 col-md-2">
            <h2 style="text-align: center; margin: auto; margin-top: 80px;">OU</h2>
        </div>
        
        
        <div class="col-md-4 col-sm-6 round-border">
            
            <md-input-container class="col-sm-12">
                <label>Code Postal</label>
                <input ng-model="postcode" />
            </md-input-container>
            
            <div class="col-sm-12">
                <md-button class="md-raised md-primary pull-right" ng-click="ctrl.getUserCoordinatesFromPostcode()">
                    Changer
                </md-button>
            </div>
            
        </div>
        
    </div>
</div>
