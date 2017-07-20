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

<script>
	$(document).ready(function()
  	{
            var rootScope = angular.element($("html")).scope();
            rootScope.$apply(function()
            {
                rootScope.menu = "shop";
            });
            
            var scope = angular.element($("#shop-container")).scope();

            scope.$apply(function()
            {
                scope.base_url = "<?php echo $base_url; ?>";
                scope.site_url = "<?php echo $site_url; ?>";
                scope.controller = "<?php echo $controller; ?>";
                scope.method = "<?php echo $method; ?>";
                scope.getProducts();
            });
  	});
</script>

<div id="shop-container" ng-controller="ShopController">
	
    <md-content layout="column" flex>

     	<md-card id="shopController" ng-include="'../assets/templates/shop-products-table.html'"></md-card>

    </md-content>
</div>
