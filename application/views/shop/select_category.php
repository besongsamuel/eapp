

<!-- Animate CSS -->
<link rel="stylesheet" href="<?php echo base_url("assets/css/shop.css")?>">


<md-content class="otiprix-section">
    
    <div class="product-big-title-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="product-bit-title text-center">
                        <h2>Categories</h2>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End Page title area -->

    <div id="admin-container" class="container" ng-controller="CategoryController" ng-cloak>    

        <div layout="row" layout-sm="column" style="margin-top: 10px;" layout-align="space-around">
            <md-progress-circular ng-disabled="!loading" class="md-hue-2" md-diameter="30px" md-mode="indeterminate" ng-show="loading"></md-progress-circular>
        </div>

        <div style=" margin-top:50px" class="container-fluid">
            
            <div class="card m-5">

                <md-toolbar class="md-primary">
                    <div>
                        <h2 class="md-toolbar-tools text-truncate">Sélectionnez une categorie pour voir son contenu</h2>
                    </div>
                </md-toolbar>
                
                <div class="row p-2" style="padding : 10px;">

                    <box-item  class="col-sm-6 col-md-4 col-lg-3 my-2" hover-effect="true" item='category' on-item-clicked='select_category($event, category)' ng-repeat="category in categories" ></box-item>

                </div>
                
             </div> 
        </div> 
    </div>
    
</md-content>
