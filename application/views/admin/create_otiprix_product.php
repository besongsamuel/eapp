

<script src="<?php echo base_url("assets/js/admin-controller.js")?>"></script>

<md-content class="otiprix-section layout-padding" ng-controller="EditProductController">
    
    <div class="row">
        <md-button class="md-raised md-otiprix pull-left" ng-click="gotoViewProducts()">
            Goto Products
        </md-button>
    </div>
    
    <form name="editProduct" ng-submit="submit($event)" novalidate>
        
        <div class="container">
            
            <div class="row layout-padding">
                <md-input-container class="col-sm-12">
                    <label>Product Name</label>
                    <input ng-model="product.name">
                </md-input-container>
            </div>
            
            <div class="row layout-padding">
                <label>Tags</label>
                <md-chips ng-model="product.tagsArray" md-removable="true"></md-chips>
            </div>
                        
            <div class="row layout-padding">
                <md-input-container class="col-sm-12">
                    <label>Sub category</label>
                    <md-select ng-model="product.subcategory_id">
                        <md-option ng-value="subcategory.id" ng-repeat="subcategory in subcategories">{{ subcategory.name }}</md-option>
                    </md-select>
                </md-input-container>
            </div>
            
            <div class="row layout-padding">
                <md-input-container class="col-sm-12">
                    <label>Unit</label>
                    <md-select ng-model="product.unit_id">
                        <md-option ng-value="unit.id" ng-repeat="unit in units">{{ unit.name }}</md-option>
                    </md-select>
                </md-input-container>
            </div>
            
            <div class="row layout-padding">
                <lf-ng-md-file-input lf-files="product_image" lf-api="api" style="width:100%" preview>
                </lf-ng-md-file-input>
            </div>
            
            <div class="row layout-padding">
                <md-button type="submit" class="md-raised md-primary pull-right">
                    Create
                </md-button>
            </div>
            
        </div>
        
    </form>
    
</md-content>