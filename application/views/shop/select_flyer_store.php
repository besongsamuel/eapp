<script src="<?php echo base_url("assets/js/selectstore-controller.js")?>"></script>

<!-- Main Script -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!-- Animate CSS -->
<link rel="stylesheet" href="<?php echo base_url("assets/css/shop.css")?>">

<div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Circulaires</h2>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Page title area -->

<div id="admin-container" class="container" ng-controller="SelectStoreController" ng-cloak>    
        
    
        <div layout="row" layout-sm="column" style="margin-top: 10px;" layout-align="space-around">
            <md-progress-circular ng-disabled="!loading"  class="md-hue-2" md-diameter="30px" md-mode="indeterminate" ng-show="loading"></md-progress-circular>
        </div>
    
        <div id="signupbox" style=" margin-top:50px" class="container">
            
            <div class="col-12">
                <p style="text-align: center;">Résultats dans un rayon de {{getDistance()}} km
                    <span> | <a href ng-click="changeDistance($event)">Changer</a></span>
                </p>
            </div>
           
	    <div class="panel panel-info">

                <md-toolbar style="background-color: #1abc9c;">
                    <div>
                        <h2 class="md-toolbar-tools">Sélectionnez le magasin pour afficher le contenu du circulaire</h2>
                    </div>
                </md-toolbar>
		  
		<md-content id="retailer-contents" style="padding : 10px;">
                    <div class="form-group-inline" ng-repeat="store in retailers">
                        <div class="col-md-3 col-sm-4" style="height: 160px;">
                            <a href><img  ng-click="select_retailer($event, store)" id="{{store.id}}" ng-src="{{store.image}}" alt="{{store.name}}" style="height: 80px; display: block; margin: 0 auto;" class="img-thumbnail img-check"></a>
                            <input type="checkbox" name="store_{{store.id}}" value="{{store.id}}" class="hidden" autocomplete="off">
                            <a href ng-click="select_retailer($event, store)" id="{{store.id}}">
                            <p class="md-otiprix-text" style="text-align: center;" ng-hide="true">{{store.department_store.address}}, {{store.department_store.city}}</p>
                            <p class="md-otiprix-text" style="text-align: center;" ng-hide="true"><span ng-show="store.department_store.state">{{store.department_store.state}},</span> <span ng-show="store.department_store.postcode">{{store.department_store.postcode}}</span></p>
                            </a>
                        </div>
                    </div>
                </md-content>
	    </div>
         </div> 
    </div>
    
   
        
    
    
