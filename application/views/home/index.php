<!DOCTYPE html>

<link href="<?php echo base_url("assets/css/home.css"); ?>" rel="stylesheet">

<style>
    .home-category-title
    {
        margin-top: 20px;
    }
    
    .home-category-title b
    {
        font-size: 24px;
    }
    
    .home-category-title a
    {
        margin-left: 15px;
        color: rgb(255,87,34);
        font-weight: 500;
        font-size: 16px;
    }
    
    .owl-theme .owl-dots .owl-dot.active span, .owl-theme .owl-dots .owl-dot:hover span 
    {
        background: #1abc9c;
    }
    
</style>

<div id="home-container" class="otiprix-section" ng-controller="ShopController as ctrl">

    <div>
        <h3 class="section-title md-otiprix-text">Économisez jusqu'a <strong>30%</strong> </br> sur votre facture</h3>
    </div>
    
    <md-divider></md-divider>
    
    <div class="maincontent-area" style="background-color: #edf0f2;">
        
        <div class="container">
                
            <?php foreach($categoryProducts as $category_products): ?>
            
            <p class="home-category-title"><b><?php echo $category_products["category"]->name; ?></b> <span><a href ng-click="ctrl.select_json_category($event, '<?php echo htmlspecialchars(json_encode($category_products["category"])); ?>')"> Voir toute les offres</a></span></p>

            <div class="product-carousel row">

                <?php foreach($category_products["products"] as $product): ?>

                <store-product store-product="<?php echo htmlspecialchars(json_encode($product)); ?>" ></store-product>

                <?php endforeach; ?>

            </div>
            
            <md-divider></md-divider>
            
            <?php endforeach; ?>
            
        </div>
        
    </div>
    
    <div  id="section02" class="layout-padding howitworks arrow section-div"  ng-controller="HomeController">
        
        
        <div class="container">
            
            <div class="row">
                <div style="margin-top: 5px; margin-bottom: 25px; text-align: center;">
                    <md-button class="md-raised md-warn" style="z-index : 10;" ng-click="gotoShop()">
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
    
    <div class="promo-area bgpatttern" ng-controller="HomeController" ng-hide="true" ng-cloak>
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
    
    
    
</div>
    

