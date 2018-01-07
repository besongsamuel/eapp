<!DOCTYPE html>

<div id="home-container">

    <md-divider></md-divider>
    <md-divider></md-divider>
    
    <h3 class="section-title md-otiprix-text">Économisez sur votre liste d'épicerie</h3>
    <h4 style="text-align: center; color : #666;">Comment ça marche?</h4>
    
    <div class="container" style="text-align: center;" ng-controller="HomeController">
            
        <div class="row">
            <div class="col-sm-12">
                <md-icon style="color: #666;"><i class="material-icons">perm_identity</i></md-icon>&nbsp;<span>Rechercher un article d'épicerie</span>&nbsp;&nbsp;&nbsp;
                <md-icon style="color: #1abc9c;"><i class="material-icons">lock_open</i></md-icon><span><b>OtiPrix trouve l'article avec le meilleur prix pour vous</b></span>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <md-icon style="color: #666;"><i class="material-icons">perm_identity</i></md-icon>&nbsp;<span>Créer une liste d'épicerie</span>&nbsp;&nbsp;&nbsp;
                <md-icon style="color: #1abc9c;"><i class="material-icons">lock_open</i></md-icon><span><b>Otiprix crée une liste d'épicerie parmi les articles aux meilleurs prix</b></span>
            </div>
        </div>
        
        <br>
        
        <div style="margin-top: 5px; margin-bottom: 30px;">
            <md-button class="md-raised md-primary" style="padding : 10px; font-size: 14px;" ng-click="gotoShop()">
                <md-icon><i class="material-icons">money_off</i></md-icon>
                <b>Commencez à économiser aujourd'hui</b>
            </md-button>
        </div>
        
    </div>
    
    <md-divider></md-divider>
    
    <div class="promo-area bgpatttern" ng-controller="HomeController" ng-hide="true">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-8">
                    <div class="single-promo">
                        <a href="<?php echo site_url("account/my_grocery_list"); ?>"><img class="img-circle" width="100px;" height="100px;" src="<?php echo base_url("/assets/img/grocerylist.png"); ?>"></a>
                        <h4 class="md-otiprix-text">Votre liste d'épicerie</h4>
<!--                        <i class="fa fa-heart"></i>-->
                        <p class="md-gray-text">Utilisez les circulaires pour créer votre panier d'épicerie et économisez sur les dépenses.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-8">
                    <div class="single-promo">
                        <a href="<?php echo site_url("shop/select_flyer_store"); ?>"><img class="img-circle" width="100px;" height="100px;" src="<?php echo base_url("/assets/img/flyers.jpg"); ?>"></a>
<!--                        <i class="fa fa-unlock"></i>-->
                        <h4 class="md-otiprix-text">Les circulaires</h4>
                        <p class="md-gray-text">Sélectionnez le magasin pour afficher le contenu de la circulaire. </p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-8">
                    <div class="single-promo">
                        <a href="<?php echo site_url("shop/categories"); ?>"><img class="img-circle" width="100px;" height="100px;" src="<?php echo base_url("/assets/img/categories.png"); ?>"></a>
                        <h4 class="md-otiprix-text">Les catégories de produits</h4>
<!--                        <i class="fa fa-calendar"></i>-->
                        <p class="md-gray-text">Utilisez les catégories de produits pour créer votre panier d'épicerie et économiser sur les dépenses.</p>
                    </div>
                </div>
            </div>
        </div>
        <md-divider></md-divider>
    </div> <!-- End promo area -->
    
    <div class="maincontent-area">
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
