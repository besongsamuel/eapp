<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<script>
    $(document).ready(function()
    {
        var shopScope = angular.element($("#shop-container")).scope();
        
        shopScope.$apply(function()
        {
            shopScope.products = JSON.parse('<?php echo $products; ?>');
        });
    });
</script>
<div ng-controller="ShopController" id="shop-container">
	
	<md-toolbar>
  		<div class="md-toolbar-tools" layout-align="space-between">
        	<div class="md-title">Available Products</div>
      	</div>
    </md-toolbar>

    <md-content layout="column" flex>

     	<md-card ng-include="'templates/shop-products-table.html'" ng-controller="ShopController"></md-card>

    </md-content>
</div>
