<!DOCTYPE html>

<div id="admin-container" class="container admin-container" ng-controller="AdminController">
    <form id="create_store_product_form" name="create_store_product_form" ng-submit="create_store_product()">
        
        <!-- Select Product-->
        <div class="form-group">
            <label>Select Product</label>
            <select 
                data-style="btn-primary"
                data-dropupAuto="true" 
                data-live-search="true" 
                data-live-search-normalize="true"
                data-live-search-placeholder = "Type product name" 
                class="form-control selectpicker" 
                id="product" 
                name="product[product_id]"
                ng-init="store_product.product_id = products[0].id"
                ng-model="store_product.product_id"
                ng-change="product_selected()"
                required
            >
                <option ng-repeat="product in products" value="{{product.id}}">{{product.name}}</option>
            </select>
            <div ng-messages="create_store_product_form.product[product_id].$error"  ng-show="create_store_product_form.product[product_id].$dirty">
                <div ng-message="required">This is required!</div>
            </div>
        </div>
        
        <!-- Section to upload product image-->
        <div class="form-group">
            <lf-ng-md-file-input lf-files="files" lf-api="api" preview></lf-ng-md-file-input>
        </div>
        
        <!--Section to select retailer-->
        <div class="form-group">
            <label>Select Retailer</label>
            <select 
            data-style="btn-info"
            data-dropupAuto="true" 
            data-header="Search Merchand" 
            data-live-search="true" 
            data-live-search-normalize="true"
            data-live-search-placeholder = "Type here" 
            data-actions-box="true"
            class="form-control selectpicker" 
            id="merchand" 
            name="product[retailer_id]"
            ng-model="store_product.retailer_id"
            >
                <option ng-repeat="retailer in retailers" value="{{retailer.id}}">{{retailer.name}}</option>
        </select>
        </div>
        
        <!--Section to enter the brand of the product-->
        <md-autocomplete  style="width: 100%;" 
                          md-input-name="product[brand]" 
                          md-selected-item="store_product.brand" 
                          md-search-text="searchText" 
                          md-items="brand in getBrandMatches(searchText)" 
                          md-item-text="brand.name" 
                          md-floating-label="Brand">
            <md-item-template>
              <span md-highlight-text="searchText">{{brand.name}}</span>
            </md-item-template>
            <md-not-found>
                No brands matching "{{searchText}}" were found.
                <a ng-click="createNewBrand(searchText)">Create a new one!</a>
            </md-not-found>
        </md-autocomplete>
       
        
        <!--Select the country and state origin of the product-->
        <country-state-select country-label="Country of origin" state-label="State of origin" country="store_product.country" state="store_product.state"></country-state-select>
        
        <!-- Section to select if product is organic -->
        <md-radio-group name="product[organic]" ng-model="store_product.is_organic">
            <label>Is Organic</label>
            <md-radio-button   ng-repeat="value in organic_select" ng-value="value.id" aria-label="{{ value.name }}">
                {{ value.name }}
            </md-radio-button>
        </md-radio-group>

        <!-- Section to select Format -->
        <md-input-container style="width: 100%;">
            <label>Product Format</label>
            <input type="text" id="format" name="product[format]" ng-change="updateQuantity()" ng-model="store_product.format" required>
            <div ng-messages="create_store_product_form.product[format].$error"  ng-show="create_store_product_form.product[format].$dirty">
                <div ng-message="required">This is required!</div>
            </div>
        </md-input-container>
            
        <!-- Section to select product unit-->
        <md-input-container style="width: 100%;">
            <label>Select Unit</label>
            <md-select name="product[unit_id]" ng-change="updateUnitPrice()" ng-model="store_product.unit_id">
                <md-option ng-value="unit.id" ng-repeat="unit in units">{{ unit.name }}</md-option>
            </md-select>
        </md-input-container>
        
        <!-- Section to select compare unit-->
        <md-input-container style="width: 100%;">
            <label>Select Compare Unit</label>
            <md-select name="product[compareunit_id]" ng-change="updateUnitPrice()" ng-model="store_product.compareunit_id"  placeholder="Select compare unit">
                <md-option ng-value="unit.id" ng-repeat="unit in compareunits">{{ unit.name }}</md-option>
            </md-select>
        </md-input-container>
        
        <!-- Section to display quantity -->
        <md-input-container style="width: 100%;">
            <label>Quantity</label>
            <input type="text" id="quantity" name="product[quantity]" ng-model="store_product.quantity" ng-change="updateUnitPrice()" readonly="true">
        </md-input-container>

        <!-- Price Section -->
        <md-input-container style="width: 100%;">
            <label>Price</label>
            <input type="number" step="0.01" id="price" name="product[price]" ng-model="store_product.price" ng-change="updateUnitPrice()" required>
        </md-input-container>
            
        <!-- Unit Price Section -->
        <md-input-container style="width: 100%;">
            <lable>Unit Price</label>
             <input type="number" step="0.0001" name="product[unit_price]" ng-model="store_product.unit_price" readonly>
        </md-input-container>
             
         <!-- Period Validity Section -->   
         <md-input-container style="width: 100%;">
            <label>Period From</label>
            <md-datepicker name="product[period_from]" ng-model="store_product.period_from" required></md-datepicker>
         </md-input-container>
            
         <md-input-container style="width: 100%;">
            <label>Period To</label>
            <md-datepicker md-min-date="store_product.period_from" name="product[period_to]" ng-model="store_product.period_to" required mindate></md-datepicker>>
         </md-input-container>
            
        <div class="form-group eapp-create">
            <input type="submit" value="Create" ng-click="continue = false">
            <input type="submit" value="Create and Continue" ng-click="continue = true">
        </div>
    </form>
</div>

<script>
$(document).ready(function()
{
    
    var scope = angular.element($("#admin-container")).scope();
    
    scope.$apply(function()
    {
        scope.products = JSON.parse('<?php echo $products; ?>');
        scope.store_product.product_id = scope.products[1].id;
        scope.retailers = JSON.parse('<?php echo $retailers; ?>');
        scope.store_product.retailer_id = scope.retailers[1].id;
        scope.units = JSON.parse('<?php echo $units; ?>');
        scope.store_product.unit_id = scope.units[1].id;
        scope.compareunits = JSON.parse('<?php echo $compareunits; ?>');
        scope.store_product.compareunit_id = scope.compareunits[1].id;
        scope.brands = JSON.parse('<?php echo $brands; ?>');
        
        
        scope.product_selected();
        scope.updateQuantity();
        scope.updateUnitPrice();
    });
});
</script>
