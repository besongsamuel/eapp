<!DOCTYPE html>
<link rel="stylesheet" href="<?php echo base_url("assets/css/owl.carousel.css")?>">
<link rel="stylesheet" href="<?php echo base_url("assets/css/owl.theme.default.min.css")?>">
<link href="<?php echo base_url("assets/css/home.css"); ?>" rel="stylesheet">


<div ng-controller="HomeController as hmCtrl" ng-cloak>
    
    <div id="home-container" class="otiprix-section parallax-background">

        <div ng-cloak>
            <h3 class="otiprix-title" otiprix-title>Économisez jusqu'a <strong>30%</strong> </br> sur votre facture</h3>
        </div>

        <md-divider></md-divider>

        <div>

            <div style="background-color: #f5f7fa;">
                
                <div class="container">
                
                    <div layout="row" layout-sm="column" layout-align="space-around">
                        <md-progress-circular ng-disabled="!loadingProducts" class="md-hue-2" md-diameter="40px" md-mode="indeterminate" ng-show="loadingProducts"></md-progress-circular>
                    </div>
                    
                    <div ng-repeat="cp in categoryProducts">
                        
                        <p class="home-category-title"><b>{{cp.category.name}}</b> <span><a href ng-click="hmCtrl.selectCategory(cp.product)"> Voir toutes les offres</a></span></p>

                        <div class="product-carousel row" style="margin : auto;">

                            <store-product ng-repeat="sp in cp.products"  full-display="false" store-product="sp" ></store-product>

                        </div>

                        <md-divider></md-divider>
                        
                    </div>

                </div>
            </div>
            
            <div layout-padding ng-cloak>
                
                <h2 class="otiprix-title" otiprix-title>Autres Catégories</h2>
                
                <div class="container" ng-controller="CategoryController as ctrl" ng-cloak>    

                    <div layout="row" layout-sm="column" layout-align="space-around">
                        <md-progress-circular ng-disabled="!loading" class="md-hue-2" md-diameter="30px" md-mode="indeterminate" ng-show="loading"></md-progress-circular>
                    </div>

                    <div class="container" style="margin-bottom : 30px;" ng-init="ctrl.getHomeCategories()">

                        <div style="margin-top: 10px;">
                            <div class="row" style="padding : 10px;">

                                <box-item item='category' on-item-clicked='select_category($event, category)' ng-repeat="category in homePageCategories" ></box-item>

                            </div>
                        </div>

                    </div> 
                    
                    <p style="text-align : center;">
                        <a href="<?php echo site_url("shop/categories"); ?>">
                            <md-button class="md-raised md-warn" style="z-index : 10;">
                                <b>Voir Plus</b>
                            </md-button>
                        </a>
                    </p>
                    
                    
                </div>
              
            </div>
                
            <md-divider></md-divider>
            
            <div style="background-color: #f5f7fa;">
            
                <div layout="row" layout-sm="column" layout-align="space-around">
                    <md-progress-circular ng-disabled="!loadingProducts" class="md-hue-2" md-diameter="40px" md-mode="indeterminate" ng-show="loadingProducts"></md-progress-circular>
                </div>
                
                <div class="container">
                    
                    <div ng-repeat="cp in categoryProducts2">
                        
                        <p class="home-category-title"><b>{{cp.category.name}}</b> <span><a href ng-click="hmCtrl.selectCategory(cp.product)"> Voir toutes les offres</a></span></p>

                        <div class="product-carousel row" style="margin : auto;">

                            <store-product ng-repeat="sp in cp.products"  full-display="false" store-product="sp" ></store-product>

                        </div>

                        <md-divider></md-divider>
                        
                    </div>

                </div>
                
            </div>

        </div>

        <div class="layout-padding howitworks arrow section-div">

            <div class="container">

                <div class="row">
                    <div style="margin-top: 5px; margin-bottom: 25px; text-align: center;">
                        <md-button class="md-raised md-warn" style="z-index : 10;" ng-click="hmCtrl.gotoShop()">
                            <md-icon><i class="material-icons">money_off</i></md-icon>
                            <b>Commencez à économiser aujourd'hui</b>
                        </md-button>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-4 col-sm-6">
                        <otiprix-step index="Créez votre liste d’épicerie" image="<?php echo base_url("/assets/img/step-2.jpg"); ?>" caption="caption_01" display-border="yes"></otiprix-step>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <otiprix-step index="Otiprix vous donne les meilleurs prix" image="<?php echo base_url("/assets/img/step-3.jpg"); ?>" caption="caption_02" display-border="yes"></otiprix-step>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <otiprix-step index="Faites votre épicerie en économisant" image="<?php echo base_url("/assets/img/list-calculator.jpg"); ?>" caption="caption_03" display-border="no"></otiprix-step>
                    </div>

                </div>

            </div>

        </div>

        </div>
        
    <md-divider></md-divider>
    
</div>
