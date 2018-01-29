<!DOCTYPE html>

<link href="<?php echo base_url("assets/css/home.css"); ?>" rel="stylesheet">

<div id="home-container">

    
    <section class="arrow main-image-area">
        
        <div class="otiprix-intro layout-padding">
            <h2>OTIPRIX</h2>
            <h3>En quelques clics, économisez sur vos listes d'épicerie</h3>
            
        </div>
        
        <div style="width: 100%; position: absolute; bottom: 60px; z-index: 5;">
            <a href="#section02"><span></span></a>
        </div>
        
    </section>
    
    <div class="search-area" ng-controller="ShopController">
        
        <div class="container layout-padding">
            <form ng-submit="searchProducts(searchText)">
                <md-input-container class="md-icon-float md-icon-right md-block">
                    <label>Rechercher produits</label>
                    <input style="color: white; text-align: center;" name="searchText" ng-model="searchText" aria-label="Rechercher" />
                    <md-icon style="color : white;"><i class="material-icons">search</i></md-icon>

                </md-input-container>
            </form>
        </div>
        
    </div>
    
    <div id="section02" class="layout-padding howitworks arrow section-div"  ng-controller="HomeController">
        
        <h3 class="section-title md-otiprix-text">Économisez sur votre liste d'épicerie</h3>
        <div class="container">
            <div class="row">
                
                <div class="col-md-3 col-sm-6">
                    <otiprix-step index="1" image="<?php echo base_url("/assets/img/step-1.jpg"); ?>" caption="Recherche un article d'épicerie" display-border="yes"></otiprix-step>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <otiprix-step index="2" image="<?php echo base_url("/assets/img/step-2.jpg"); ?>" caption="Créer une liste d'épicerie" display-border="yes"></otiprix-step>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <otiprix-step index="3" image="<?php echo base_url("/assets/img/step-3.jpg"); ?>" caption="OtiPrix trouve l'article avec le meilleur prix pour vous" display-border="yes"></otiprix-step>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <otiprix-step index="4" image="<?php echo base_url("/assets/img/step-4.jpg"); ?>" caption="OtiPrix crée une liste d'épicerie parmi les articles aux meilleurs prix" display-border="no"></otiprix-step>
                </div>
                
            </div>
            
            <div class="row">
                <div style="margin-top: 5px; margin-bottom: 30px; text-align: center;">
                    <md-button class="md-raised action-button" style="z-index : 10;" ng-click="gotoShop()">
                        <md-icon><i class="material-icons">money_off</i></md-icon>
                        <b>Commencez à économiser aujourd'hui</b>
                    </md-button>
                </div>
            </div>
                
        </div>
        
        <div style="width: 100%; position: absolute; bottom: 60px; z-index: 5;">
            <a href="#section03"><span></span></a>
        </div>
        
    </div>
    
    </div>
        
    <md-divider></md-divider>
    
    <div id="section03" class="maincontent-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="latest-product" ng-controller="CartController">
                        <h2 class="section-title md-otiprix-text">Produits en vedette</h2>
                        
                        <div class="product-carousel row">
                            <?php foreach($latestProducts as $product): ?>
                                <div class="single-product col-md-12 col-sm-12">
                                    <div class="product-f-image">
                                        <img ng-src="<?php echo $product->product->image;?>" style="height: 100%;" alt="">
                                        <div class="product-hover">
                                            <a href ng-hide="productInCart(<?php echo $product->product_id; ?>)" class="add-to-cart-link" ng-click="add_product_to_cart(<?php echo $product->product_id; ?>)"><i class="fa fa-shopping-cart"></i>Ajouter</a>
                                            <a href ng-show="productInCart(<?php echo $product->product_id; ?>)" class="add-to-cart-link" ng-click="remove_product_from_cart(<?php echo $product->product_id; ?>)"><i class="fa fa-shopping-cart"></i>Retirer</a>
                                            <a href ng-click="viewProduct(<?php echo $product->id; ?>)" class="view-details-link"><i class="fa fa-link"></i>Détails</a>
                                        </div>
                                    </div>

                                    <h2 style="font-size: 14px; text-align: center;"><a href ng-click="viewProduct(<?php echo $product->id; ?>)"><?php echo $product->product->name; ?></a></h2>

                                    <div class="product-carousel-price" ng-hide="true">
                                        <ins>CAD <?php echo $product->price; ?></ins><del>CAD <?php echo $product->regular_price; ?></del>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End main content area -->
    
</div>
    
<script src="<?php echo base_url("assets/js/home-controller.js")?>"></script>
