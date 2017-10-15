<!DOCTYPE html>

<div class="container">
    
    <a href="<?php echo site_url("home/goback"); ?>">Retour</a>
    
    <h2>Choisissez l'option de localisation</h2>
    
    <div class="alert alert-success" ng-show="successMessage">
        <strong>Succès!</strong> Votre adresse actuelle a été modifiée.
    </div>
    
    <div class="row">
        <div class="col-md-3 col-sm-6 round-border">
            <h4>Geolocation</h4>
            <p>La géolocalisation nous permet d'obtenir votre position géographique. Dans ce cas, nous identifions votre ville pour diffuser les promotions disponibles dans ce domaine.</p>
            <md-button class="md-raised md-otiprix" ng-click="getUserCoordinates()">
                Me géolocaliser
            </md-button>
            
        </div>
        <div class="col-sm-1">
            <h2 style="text-align: center; margin: auto; margin-top: 80px;">OU</h2>
        </div>
        
        
        <div class="col-md-3 col-sm-6 round-border">
            <md-input-container>
                <label>Code Postal</label>
                <input ng-model="postcode" />
            </md-input-container>
            <md-button class="md-raised md-otiprix" ng-click="getUserCoordinatesFromPostcode()">
                Changer
            </md-button>
        </div>
        
    </div>
</div>
