<!DOCTYPE html>

<!-- Main Script -->
<script src="http://<?php echo addslashes(base_url("assets/js/admin-controller.js")); ?>"></script>

<script>
$(document).ready(function()
{
    
    var scope = angular.element($("#admin-container")).scope();
    var rootScope = angular.element($("html")).scope();
    
    scope.$apply(function()
    {
        rootScope.menu = "admin_create_product";
        scope.store_product = JSON.parse('<?php echo $store_product; ?>');
	scope.retailers = JSON.parse('<?php echo $retailers; ?>');
	scope.units = JSON.parse('<?php echo $units; ?>');
	scope.compareunits = JSON.parse('<?php echo $compareunits; ?>');
	scope.brands = JSON.parse('<?php echo $brands; ?>');
        scope.base_url = "<?php echo $base_url; ?>";
        scope.site_url = "<?php echo $site_url; ?>";
        scope.controller = "<?php echo $controller; ?>";
        scope.method = "<?php echo $method; ?>";
        
        if(sessionStorage.getItem("retailer_id"))
        {
	    scope.store_product.retailer_id = scope.retailers[parseInt(sessionStorage.getItem("retailer_id"))].id;
            sessionStorage.removeItem("retailer_id");
        }
	else
	{
	    scope.store_product.retailer_id = scope.retailers[parseInt(scope.store_product.retailer_id)].id; 
	}
	    
        if(sessionStorage.getItem("period_from"))
        {
            scope.store_product.period_from = new Date(sessionStorage.getItem("period_from").toString().replace("-", "/"));
            sessionStorage.removeItem("period_from");
        }
	else
	{
	    scope.store_product.period_from = new Date(scope.store_product.period_from.toString().replace("-", "/"));
	}
	    
        if(sessionStorage.getItem("period_to"))
        {
            scope.store_product.period_to = new Date(sessionStorage.getItem("period_to").toString().replace("-", "/"));
            sessionStorage.removeItem("period_to");
        }
        else
	{
	    scope.store_product.period_to = new Date(scope.store_product.period_to.toString().replace("-", "/"));
	}
        
        scope.store_product.price = parseFloat(scope.store_product.price);
        scope.store_product.unit_price = parseFloat(scope.store_product.unit_price);
        scope.store_product.regular_price = parseFloat(scope.store_product.regular_price);
        scope.store_product.organic = parseInt(scope.store_product.organic) === 0 ? false : true;
        scope.store_product.in_flyer = parseInt(scope.store_product.in_flyer) === 0 ? false : true;
        scope.products = JSON.parse('<?php echo $products; ?>');
                
        if(typeof scope.store_product.product_id !== "undefined")
        {
            scope.store_product.product_id = scope.products[parseInt(scope.store_product.product_id)].id;  
            scope.selectedProduct = scope.products[parseInt(scope.store_product.product_id)];
        }
        
        if(typeof scope.store_product.unit_id !== "undefined")
        {
            scope.store_product.unit_id = scope.units[parseInt(scope.store_product.unit_id)].id;  
        }
	    
	if(typeof scope.store_product.compareunit_id !== "undefined")
        {
            scope.store_product.compareunit_id = scope.compareunits[parseInt(scope.store_product.compareunit_id)].id;  
        }
        
        scope.product_selected();
        scope.updateQuantity();
        scope.updateUnitPrice();
    });
});
</script>

    <!-- Begin mainmenu area -->
    <div class="mainmenu-area" ng-controller="MenuController">
        <div class="container">
            <div class="row">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div> 
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a  href="http://<?php echo addslashes(site_url("admin/uploads")); ?>">Uploads</a></li>
                        <li class="active"><a href="http://<?php echo addslashes(site_url("admin/create_store_product")); ?>">Create Product</a></li>
                        <li><a href="http://<?php addslashes(echo site_url("admin/store_products")); ?>">View Products</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div> 
    <!-- End mainmenu area -->

<div id="admin-container" class="container admin-container" ng-controller="AdminController">
    <md-content layout-padding>
        <form id="create_store_product_form" name="create_store_product_form" ng-submit="create_store_product()">
        
        <input type="hidden" name="product[id]" value="<?php echo $id; ?>">
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
        
         <!-- Period Validity Section -->   
         <div class="form-group col-sm-6">
            <label>Period From</label>
            <input type="date" class="form-control" name="product[period_from]" ng-model="store_product.period_from" required>
         </div>
            
         <div class="form-group col-sm-6">
            <label>Period To</label>
            <input type="date" class="form-control" name="product[period_to]" ng-model="store_product.period_to" required>
         </div>
        	
	<md-autocomplete class="col-sm-6"
          	md-selected-item="selectedProduct"
          	md-search-text="searchProductText"
          	md-selected-item-change="product_selected(item)"
          	md-items="item in querySearch(searchProductText)"
          	md-item-text="item.name"
          	md-min-length="2"
          	md-floating-label="Search products"
          	placeholder="Type in the name of the product">
	        <md-item-template>
	          	<span md-highlight-text="searchProductText" md-highlight-flags="^i">{{item.name}}</span>
	        </md-item-template>
	        <md-not-found>
                	No poducts matching "{{searchProductText}}" were found.
                	<a ng-click="createNewProduct(searchProductText)">Create a new one!</a>
            	</md-not-found>
      	</md-autocomplete>
           
        <!--Section to enter the brand of the product-->
        <md-autocomplete class="col-sm-6"
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
        
        <!-- Section to upload product image-->
        <lf-ng-md-file-input lf-files="files" lf-api="api" style="width:100%" preview>
        </lf-ng-md-file-input>
        
       
        <!--Select the country and state origin of the product-->
        <md-country-select cs-priorities="CA, US, GB" class="col-sm-6" country="store_product.country"></md-country-select>
        
        <!-- Section to select if product is organic -->        
        <div flex-gt-sm="50">
          <md-checkbox name="product[organic]" ng-model="store_product.organic" aria-label="Organic">
            Organic
          </md-checkbox>
        </div>
        
        <!-- Section to select if product is present in the flyer -->
        <div flex-gt-sm="50">
          <md-checkbox name="product[in_flyer]" ng-model="store_product.in_flyer" aria-label="In Flyer">
            In Flyer
          </md-checkbox>
        </div>

        <!-- Section to select Format -->
        <md-input-container  class="col-sm-6">
            <label>Product Format</label>
            <input id="format" name="product[format]" ng-change="updateQuantity()" ng-model="store_product.format" required>
            <div ng-messages="create_store_product_form.product[format].$error"  ng-show="create_store_product_form.product[format].$dirty">
                <div ng-message="required">This is required!</div>
            </div>
        </md-input-container>
        
        <!-- Section to select size -->
        <md-input-container  class="col-sm-6">
            <label>Size</label>
            <input id="format" name="product[size]" ng-model="store_product.size">
        </md-input-container>
            
        <!-- Section to select product unit-->
        <md-input-container class="col-sm-6">
            <label>Select Unit</label>
            <md-select name="product[unit_id]" ng-change="updateUnitPrice()" ng-model="store_product.unit_id">
                <md-option ng-value="unit.id" ng-repeat="unit in units">{{ unit.name }}</md-option>
            </md-select>
        </md-input-container>
        
        <!-- Section to select compare unit-->
        <md-input-container  class="col-sm-6">
            <label>Select Compare Unit</label>
            <md-select name="product[compareunit_id]" ng-change="updateUnitPrice()" ng-model="store_product.compareunit_id"  placeholder="Select compare unit">
                <md-option ng-value="unit.id" ng-repeat="unit in compareunits">{{ unit.name }}</md-option>
            </md-select>
        </md-input-container>
        
        <!-- Section to display quantity -->
        <md-input-container  class="col-sm-12">
            <label>Quantity</label>
            <input id="quantity" name="product[quantity]" ng-model="store_product.quantity" ng-change="updateUnitPrice()" readonly="true">
        </md-input-container>
        
        <!-- Price Section -->
        <md-input-container  class="col-sm-4">
            <label>Regular Price</label>
            <input type="number" step="0.01" id="regular_price" name="product[regular_price]" ng-model="store_product.regular_price">
        </md-input-container>
        
        <!-- Price Section -->
        <md-input-container  class="col-sm-4">
            <label>Price</label>
            <input type="number" step="0.01" id="price" name="product[price]" ng-model="store_product.price" ng-change="updateUnitPrice()" required>
        </md-input-container>
        
        <!-- Unit Price Section -->
        <md-input-container  class="col-sm-4">
            <label>Unit Price</label>
            <input type="number" step="0.01" id="price" name="product[unit_price]" ng-model="store_product.unit_price" readonly>
        </md-input-container>
            
        <div class="form-group eapp-create">
            <input type="submit" value="{{getSaveLabel()}}" ng-click="continue = false">
            <input type="submit" value="{{getSaveLabel()}} and Continue" ng-click="continue = true">
        </div>
    </form>
    </md-content>
</div>
