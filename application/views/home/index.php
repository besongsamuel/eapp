<!DOCTYPE html>

<div id="home-container">

    <script src="<?php echo base_url("assets/js/home-controller.js")?>"></script>
    
    <div class="promo-area" ng-controller="HomeController">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-8">
                    <div class="single-promo">
                        <a href="<?php echo site_url("account/my_grocery_list"); ?>" class="category-block"><img class="img-circle" width="100px;" height="100px;" src="<?php echo base_url("/assets/img/grocerylist.png"); ?>"></a>
                        <h2 class="md-otiprix-text">Votre liste d'épicerie</h2>
<!--                        <i class="fa fa-heart"></i>-->
                        <p class="md-gray-text">Créez votre liste d'épicerie et économisez sur les dépenses.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-8">
                    <div class="single-promo">
                        <a href="<?php echo site_url("shop/select_flyer_store"); ?>" class="category-block"><img class="img-circle" width="100px;" height="100px;" src="<?php echo base_url("/assets/img/flyers.jpg"); ?>"></a>
<!--                        <i class="fa fa-unlock"></i>-->
                        <h2 class="md-otiprix-text">Les circulaires</h2>
                        <p class="md-gray-text">Utilisez notre circulaires pour créer votre panier d'épicerie et économisez sur les dépenses</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-8">
                    <div class="single-promo">
                        <a href="<?php echo site_url("shop/categories"); ?>" class="category-block"><img class="img-circle" width="100px;" height="100px;" src="<?php echo base_url("/assets/img/categories.png"); ?>"></a>
                        <h2 class="md-otiprix-text">Les catégories de produits</h2>
<!--                        <i class="fa fa-calendar"></i>-->
                        <p class="md-gray-text">Utilisez notre catégories de produits pour créer votre panier d'épicerie et économiser sur les dépenses.</p>
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
    
<script>
    $(document).ready(function()
    {

        var rootScope = angular.element($("html")).scope();

        rootScope.$apply(function()
        {
            rootScope.menu = "home";
        });
    });
</script>
