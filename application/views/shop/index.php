<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<!-- Main Script -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!-- Animate CSS -->
<link rel="stylesheet" href="<?php echo base_url("assets/css/shop.css")?>">

<div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Produits</h2>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Page title area -->

<md-content id="shop-container" class="white-container" ng-controller="ShopController">
	
    <div class="container" style="margin-top : 10px;" ng-show="category_id">
        <ul class="breadcrumb" style="text-align: center; background: white;">
            <li><a href="<?php echo site_url("shop/categories")?>">Categories</a></li>
            <li class="active">{{category_name}}</li>
        </ul>
    </div>
    
    <div class="container" style="margin-top : 10px;" ng-show="store_id">
        <ul class="breadcrumb" style="text-align: center; background: white;">
            <li><a href="<?php echo site_url("shop/select_flyer_store")?>">Circulaires</a></li>
            <li class="active">{{store_name}}</li>
        </ul>
    </div>
    
    <md-content class="container">
        <div class="row">
            <div class="col-md-2">
                <result-filter ng-if="productsReady" ready="productsReady" result-set="filterSettings" on-settings-changed="settingsChanged(item)" ></result-filter>
            </div>
            
            <div class="col-md-10">
                <div layout="column" style="white-container">

                    <md-card id="shopController" ng-include="'<?php echo base_url(); ?>/assets/templates/shop-products-table.html'"></md-card>

                </div>
            </div>
        </div>
    </md-content>
    
</md-content>
