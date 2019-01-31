
<!-- Animate CSS -->
<link rel="stylesheet" href="<?php echo base_url("assets/css/shop.css")?>">

<div id="admin-container" class="otiprix-section" ng-controller="SelectStoreController" ng-cloak>    
        
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
    
    <div layout="row" layout-sm="column" style="margin-top: 10px;" layout-align="space-around">
        <md-progress-circular ng-disabled="!loading"  class="md-hue-2" md-diameter="30px" md-mode="indeterminate" ng-show="loading"></md-progress-circular>
    </div>
    
        <div class="container-fluid">
            
            <div class="col-12">
                <p style="text-align: center;">Résultats dans un rayon de {{getDistance()}} km
                    <span> | <a href ng-click="changeDistance($event)">Changer</a></span>
                </p>
            </div>
           
	    <div class="card m-5">

                <md-toolbar class="md-primary">
                    <div>
                        <h2 class="md-toolbar-tools text-truncate">Sélectionnez le magasin pour afficher le contenu de la circulaire</h2>
                    </div>
                </md-toolbar>
                
                <md-input-container class="md-block px-5">
                    <md-icon>
                        <i class="material-icons">search</i>
                    </md-icon>
                    <input ng-model="storeName" ng-change="search()">
                </md-input-container>
                
                
                
                <p ng-if="!storesAvailable" style="text-align: center;" class="md-warn-color"><b>Il n'y a pas de magasin disponible près de chez vous.</b></p>
		  
                    <div class="row p-2">
                        <box-item class="col-sm-6 col-md-4 col-lg-3 my-2" item='store' on-item-clicked='select_retailer($event, store)' ng-repeat="store in retailers" ></box-item>
                    </div>

	    </div>
         </div> 
    </div>

   
        
    
    
