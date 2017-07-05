<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<!-- Main Script -->
<script src="http://<?php echo base_url("assets/js/shop-controller.js")?>"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!-- Animate CSS -->
<link rel="stylesheet" href="http://<?php echo base_url("assets/css/shop.css")?>">

<div id="shop-container">
	
    <md-content layout="column" flex>

     	<md-card id="shopController" ng-include="'../assets/templates/shop-products-table.html'" ng-controller="ShopController"></md-card>

    </md-content>
</div>
