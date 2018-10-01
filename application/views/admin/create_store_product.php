<!DOCTYPE html>

<!-- Main Script -->

<div class="mainbox">
    <div class="product-big-title-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="product-bit-title text-center">
                        <h2>Create Store Product</h2>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End Page title area -->

    <div id="admin-container" class="admin-container otiprix-section" ng-controller="AdminController" ng-cloak>

        <div class="container">

            <form id="create_store_product_form" name="create_store_product_form" ng-submit="create_store_product()">

            <input type="hidden" name="product[id]" value="<?php echo $id; ?>">
            <!--Section to select retailer-->
            <md-input-container class="col-sm-12">
                <label>Select Retailer</label>
                <md-select name="product[retailer_id]" ng-model="store_product.retailer_id">
                    <md-option ng-value="retailer.id" ng-repeat="retailer in retailers">{{ retailer.name }}</md-option>
                </md-select>
            </md-input-container> 

            <!-- Period Validity Section -->   
            <div class="form-group col-sm-6">
               <label>Period From</label>
               <input type="date" class="form-control" name="product[period_from]" ng-model="period_from" required>
            </div>

            <div class="form-group col-sm-6">
               <label>Period To</label>
               <input type="date" class="form-control" name="product[period_to]" ng-model="period_to" required>
            </div>

            <md-autocomplete class="col-sm-6"
                    md-selected-item="selectedProduct"
                    md-search-text="searchProductText"
                    md-selected-item-change="onProductSelected(item)"
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
                              md-selected-item="store_product.brand" 
                              md-search-text="searchText" 
                              md-selected-item-change="brand_selected(brand)"
                              md-items="brand in getBrandMatches(searchText)" 
                              md-item-text="brand.name" 
                              md-no-cache="true"
                              md-floating-label="Brand">
                <md-item-template>
                  <span md-highlight-text="searchText">{{brand.name}}</span>
                </md-item-template>
                <md-not-found>
                    No brands matching "{{searchText}}" were found.
                    <a ng-click="createNewBrand(searchText)">Create a new one!</a>
                </md-not-found>
            </md-autocomplete>

            <div class="row">
                <md-input-container  class="col-sm-12">
                    <!-- Section to upload product image-->
                    <lf-ng-md-file-input lf-files="product_image" lf-api="api" style="width:100%" preview>
                    </lf-ng-md-file-input>
                </md-input-container>
            </div>


            <div class="row">
                <!--Select the country and state origin of the product-->
                <country-state-select country="store_product.country" flag="flag" country-state="store_product.state"></country-state-select>
            </div>        

            <!-- Section to select if product is organic -->        
            <div class="row">
              <md-checkbox name="product[organic]" ng-model="store_product.organic" aria-label="Organic">
                Bio
              </md-checkbox>
            </div>

            <!-- Section to select if product is present in the flyer -->
            <div class="row">
              <md-checkbox name="product[in_flyer]" ng-model="store_product.in_flyer" aria-label="In Flyer">
                En Circulaire
              </md-checkbox>
            </div>

            <div class="row">

                <!-- Section to select Format -->
                <md-input-container  class="col-md-3 col-sm-12">
                    <label>Product Format</label>
                    <input id="format" name="product[format]" ng-change="updateQuantity()" ng-model="store_product.format" required>
                    <div ng-messages="create_store_product_form.product[format].$error"  ng-show="create_store_product_form.product[format].$dirty">
                        <div ng-message="required">This is required!</div>
                    </div>
                </md-input-container>

                <!-- Section to select size -->
                <md-input-container  class="col-md-3 col-sm-12">
                    <label>Size</label>
                    <input id="format" name="product[size]" ng-model="store_product.size">
                </md-input-container>

                <!-- Units when the selected product has unit set -->
                <md-input-container class="col-md-3 col-sm-12" ng-show="productHasUnit">
                    <label>Select Unit</label>
                    <md-select name="product[unit_id]"  ng-change="updateQuantity()" ng-model="store_product.unit_id">
                        <md-option ng-value="unit.id" ng-repeat="unit in units">{{ unit.name }}</md-option>
                    </md-select>
                </md-input-container>

                <!-- Section to select compare unit-->
                <md-input-container  class="col-md-3 col-sm-12" ng-show="productHasUnit">
                    <label>Select Compare Unit</label>
                    <md-select id="compare-unit" name="product[compareunit_id]" ng-change="updateUnits(store_product.compareunit_id)" ng-model="store_product.compareunit_id"  placeholder="Select compare unit">
                        <md-option ng-value="unit.id" ng-repeat="unit in compareUnits">{{ unit.name }}</md-option>
                    </md-select>
                </md-input-container>

                <!-- Units when the selected product has no unit set -->
                <!-- Section to select product unit-->
                <md-input-container class="col-md-3 col-sm-12" ng-hide="productHasUnit">
                    <label>Select Unit</label>
                    <md-select name="product[unit_id]"  ng-change="updateQuantity()" ng-model="store_product.unit_id">
                        <md-option ng-value="unit.id" ng-repeat="unit in units">{{ unit.name }}</md-option>
                    </md-select>
                </md-input-container>

                <!-- Section to select compare unit-->
                <md-input-container  class="col-md-3 col-sm-12" ng-hide="productHasUnit">
                    <label>Select Compare Unit</label>
                    <md-select id="compare-unit" name="product[compareunit_id]" ng-change="updateUnits(store_product.compareunit_id)" ng-model="store_product.compareunit_id"  placeholder="Select compare unit">
                        <md-option ng-value="unit.id" ng-repeat="unit in compareUnits">{{ unit.name }}</md-option>
                    </md-select>
                </md-input-container>

            </div>

            <div class="row">
                <!-- Section to display quantity -->
                <md-input-container  class="col-md-3 col-sm-12">
                    <label>Quantity</label>
                    <input id="quantity" name="product[quantity]" ng-model="store_product.quantity" readonly="true">
                </md-input-container>

                <!-- Price Section -->
                <md-input-container  class="col-md-3 col-sm-12">
                    <label>Regular Price</label>
                    <input type="number" step="0.01" id="regular_price" name="product[regular_price]" ng-model="store_product.regular_price">
                </md-input-container>

                <!-- Price Section -->
                <md-input-container  class="col-md-3 col-sm-12">
                    <label>Price</label>
                    <input type="number" step="0.01" id="price" name="product[price]" ng-model="store_product.price" ng-change="updateQuantity()" required>
                </md-input-container>

                <!-- Unit Price Section -->
                <md-input-container  class="col-md-3 col-sm-12">
                    <label>Unit Price</label>
                    <input type="number" id="price" name="product[unit_price]" ng-model="store_product.unit_price" readonly>
                </md-input-container>
            </div>

            <div class="form-group eapp-create col-md-12 col-sm-12">
                <md-button class="md-raised md-primary pull-right" type="submit" ng-click="continue = false">{{getSaveLabel()}}</md-button>
                <md-button class="md-raised md-primary pull-right" type="submit" ng-click="continue = true">{{getSaveLabel()}} and Continue</md-button>
            </div>

        </form>
        </div>
    </div>
</div>


