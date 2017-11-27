<!DOCTYPE html>

<!-- Main Script -->
<script src="<?php echo base_url("assets/js/admin-controller.js")?>"></script>

<script>
    $(document).ready(function()
    {
        var scope = angular.element($("#admin-container")).scope();
        var rootScope = angular.element($("html")).scope();
        
        scope.$apply(function()
        {
            rootScope.menu  = 'admin_upload';
            scope.base_url = "<?php echo $base_url; ?>";
            scope.site_url = "<?php echo $site_url; ?>";
            scope.controller = "<?php echo $controller; ?>";
            scope.method = "<?php echo $method; ?>";
        });
    });
</script>


<div id="admin-container" class="admin-container" ng-controller="AdminController">
    
	<div class="container">
		
		<md-toolbar style="background-color: #1abc9c; margin-bottom : 10px;">
			<div>
				<h2 class="md-toolbar-tools">Upload CSV File</h2>
			</div>
		</md-toolbar>
		
		
		<!-- Upload Chains Section -->
    <fieldset>
        <legend>Upload Chains</legend>
        <form id="upload_chains_form" ng-submit="upload_chains()">
        
        <div class="form-group">
            <label for="chains">Select File:</label>
            <input id="chains" name="chains" type="file" class="filestyle" data-buttonName="btn-primary">
        </div>
        
        <div class="form-group eapp-create">
            <input type="submit" value="Upload">
        </div>
    </form>
    </fieldset>
    
    <!-- Upload Chains Stores Section -->
    <fieldset>
        <legend>Upload Stores</legend>
        <form id="upload_stores_form" ng-submit="upload_stores()">
        
        <div class="form-group">
            <label for="stores">Select File:</label>
            <input id="stores" name="stores" type="file" class="filestyle" data-buttonName="btn-primary">
        </div>
        
        <div class="form-group eapp-create">
            <input type="submit" value="Upload">
        </div>
    </form>
    </fieldset>
    
    <!-- Upload Categories Section -->
    <fieldset>
        <legend>Upload Categories</legend>
        <form id="upload_categories_form" ng-submit="upload_categories()">
        
        <div class="form-group">
            <label for="categories">Select File:</label>
            <input id="categories" name="categories" type="file" class="filestyle" data-buttonName="btn-primary">
        </div>
        
        <div class="form-group eapp-create">
            <input type="submit" value="Upload">
        </div>
    </form>
    </fieldset>
    
    <!-- Upload Sub Categories Section -->
    <fieldset>
        <legend>Upload Sub Categories</legend>
        <form id="upload_subcategories_form" ng-submit="upload_subcategories()">
        
        <div class="form-group">
            <label for="subcategories">Select File:</label>
            <input id="subcategories" name="subcategories" type="file" class="filestyle" data-buttonName="btn-primary">
        </div>
        
        <div class="form-group eapp-create">
            <input type="submit" value="Upload">
        </div>
    </form>
    </fieldset>
    
    <!-- Upload Products Section -->
    <fieldset>
        <legend>Upload Products</legend>
        <form id="upload_products_form" ng-submit="upload_products()">
        
        <div class="form-group">
            <label for="products">Select File:</label>
            <input id="products" name="products" type="file" class="filestyle" data-buttonName="btn-primary">
        </div>
        
        <div class="form-group eapp-create">
            <input type="submit" value="Upload">
        </div>
    </form>
    </fieldset>
    
    <!-- Upload Units Section -->
    <fieldset>
        <legend>Upload Units</legend>
            <form id="upload_units_form" ng-submit="upload_units()">

            <div class="form-group">
                <label for="units">Select File:</label>
                <input id="units" name="units" type="file" class="filestyle" data-buttonName="btn-primary">
            </div>

            <div class="form-group eapp-create">
                <input type="submit" value="Upload">
            </div>
        </form>
    </fieldset>
		
	</div>
	
</div>

<script>
$(document).ready(function()
{
    var scope = angular.element($("#admin-container")).scope();
    
    scope.$apply(function()
    {
    });
});
</script>
