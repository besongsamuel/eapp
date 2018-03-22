<!DOCTYPE html>

<!-- Main Script -->
<script src="<?php echo base_url("assets/js/admin-controller.js")?>"></script>

<div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Upload CSV File</h2>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Page title area -->


<div id="admin-container" class="admin-container" ng-controller="AdminController">
    
    <div class="container">
		
        <!-- Upload Chains Section -->
        <fieldset>
            <legend>Upload Chains</legend>
            <form id="upload_chains_form" ng-submit="upload('chains', $event)">

                <div class="form-group">
                    <label for="chains">Select File:</label>
                    <input id="chains" name="chains" type="file" class="filestyle" data-buttonName="btn-primary">
                </div>

                <div class="col-sm-12">
                    <md-button type="submit" class="md-raised md-primary pull-right">Upload</md-button>
                </div>    
            </form>
        </fieldset>

        <!-- Upload Chains Stores Section -->
        <fieldset>
            <legend>Upload Stores</legend>
            <form id="upload_stores_form" ng-submit="upload('stores', $event)">

            <div class="form-group">
                <label for="stores">Select File:</label>
                <input id="stores" name="stores" type="file" class="filestyle" data-buttonName="btn-primary">
            </div>

            <div class="col-sm-12">
                <md-button type="submit" class="md-raised md-primary pull-right">Upload</md-button>
            </div>
        </form>
        </fieldset>

        <!-- Upload Categories Section -->
        <fieldset>
            <legend>Upload Categories</legend>
            <form id="upload_categories_form" ng-submit="upload('categories', $event)">

            <div class="form-group">
                <label for="categories">Select File:</label>
                <input id="categories" name="categories" type="file" class="filestyle" data-buttonName="btn-primary">
            </div>

            <div class="col-sm-12">
                <md-button type="submit" class="md-raised md-primary pull-right">Upload</md-button>
            </div>
        </form>
        </fieldset>

        <!-- Upload Sub Categories Section -->
        <fieldset>
            <legend>Upload Sub Categories</legend>
            <form id="upload_subcategories_form" ng-submit="upload('subcategories', $event)">

            <div class="form-group">
                <label for="subcategories">Select File:</label>
                <input id="subcategories" name="subcategories" type="file" class="filestyle" data-buttonName="btn-primary">
            </div>

            <div class="col-sm-12">
                <md-button type="submit" class="md-raised md-primary pull-right">Upload</md-button>
            </div>
        </form>
        </fieldset>

        <!-- Upload Products Section -->
        <fieldset>
            <legend>Upload Products</legend>
            <form id="upload_products_form" ng-submit="upload('products', $event)">

            <div class="form-group">
                <label for="products">Select File:</label>
                <input id="products" name="products" type="file" class="filestyle" data-buttonName="btn-primary">
            </div>

            <div class="col-sm-12">
                <md-button type="submit" class="md-raised md-primary pull-right">Upload</md-button>
            </div>
        </form>
        </fieldset>

        <!-- Upload Units Section -->
        <fieldset>
            <legend>Upload Units</legend>

            <form id="upload_units_form" ng-submit="upload('units', $event)">
                <div class="form-group">
                    <label for="units">Select File:</label>
                    <input id="units" name="units" type="file" class="filestyle" data-buttonName="btn-primary">
                </div>

                <div class="col-sm-12">
                    <md-button type="submit" class="md-raised md-primary pull-right">Upload</md-button>
                </div>
            </form>

        </fieldset>

        <!-- Upload Units Section -->
        <fieldset>
            <legend>Upload Unit Conversions</legend>
                <form id="upload_unit_compareunit_form" ng-submit="upload('unit_compareunit', $event)">

                <div class="form-group">
                    <label for="unit_compareunit">Select File:</label>
                    <input id="unit_compareunit" name="unit_compareunit" type="file" class="filestyle" data-buttonName="btn-primary">
                </div>

                <div class="col-sm-12">
                    <md-button type="submit" class="md-raised md-primary pull-right">Upload</md-button>
                </div>
            </form>
        </fieldset>

        <!-- Upload Units Section -->
        <fieldset>
            <legend>Upload Product Unit Conversions</legend>
                <form id="upload_product_unit_compareunit_form" ng-submit="upload('product_unit_compareunit', $event)">

                <div class="form-group">
                    <label for="product_unit_compareunit">Select File:</label>
                    <input id="product_unit_compareunit" name="product_unit_compareunit" type="file" class="filestyle" data-buttonName="btn-primary">
                </div>

                <div class="col-sm-12">
                    <md-button type="submit" class="md-raised md-primary pull-right">Upload</md-button>
                </div>
            </form>
        </fieldset>
		
    </div>
	
</div>
