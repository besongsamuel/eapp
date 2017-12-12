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
                        <div class="col-md-2 col-md-4" style="padding-top:10px;">
                            <label  class="btn item-block">
                                <img  ng-click="select_retailer($event, store)" id="{{store.id}}" ng-src="{{store.image}}" alt="{{store.name}}" class="store-block img-check">
                                <input type="checkbox" name="store_{{store.id}}" value="{{store.id}}" class="hidden" autocomplete="off">
                            </label>
                        </div>
                    </div>
                    
                </md-content>
	    </div>
         </div> 
    </div>
    
   
        
    
    
