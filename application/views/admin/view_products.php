
<style>
    .product-image
    {
        margin: 2px;
        padding: 2px;
    }
    
    .product-image img
    {
        width: 120px;
        height: 120px;
        display: block;
        margin : auto;
        
    }
    
    md-content 
    {
        background-color: #f5f5f5 !important;
        -moz-osx-font-smoothing: grayscale;
    }

    
    
</style>

<link rel="stylesheet" href="<?php echo base_url("assets/css/admin.css")?>">

<md-content class="otiprix-section layout-padding" ng-controller="ViewProductsController" ng-cloak>
    
    <div class="row">
        <md-button class="md-raised md-primary pull-left" ng-click="gotoCreateNewProduct()">
            Create New Product
        </md-button>
    </div>
    
    <md-toolbar class="md-table-toolbar md-default" ng-hide="selected.length || filter.show">
        <div class="md-toolbar-tools">
        <div flex></div>
        <md-button class="md-icon-button" ng-click="filter.show = true">
            <md-icon>filter_list</md-icon>
        </md-button>
      </div>
    </md-toolbar>
    
    

    <md-toolbar class="md-table-toolbar md-default" ng-show="filter.show && !selected.length">
        <div class="md-toolbar-tools">
            <md-icon>search</md-icon>
            <form flex name="filter.form">
                <input type="text" ng-model="query.filter" ng-model-options="filter.options" placeholder="search">
            </form>
            <md-button class="md-icon-button" ng-click="removeFilter()">
                <md-icon>close</md-icon>
            </md-button>
        </div>
    </md-toolbar>
    
    <div class="w-100 d-flex flex-row justify-content-center my-3">
        <md-checkbox ng-model="query.is_new" ng-change="getProducts()" aria-label="View new products">
            Voir les nouveaux produits
        </md-checkbox>
        <md-checkbox ng-model="query.no_tags" ng-change="getProducts()" aria-label="View products with no tags">
            Voir les produits sans tags
        </md-checkbox>
    </div>
    
    
    <md-table-container>
        <table  md-table ng-model="selected" md-progress="promise">
            <thead md-head>
              <tr md-row>
                <th md-column><span>Product Name</span></th>
                <th md-column><span>Tags</span></th>
                <th md-column><span>Hot Item</span></th>
                <th md-column>Image</th>
                <th md-column>Subcategory</th>
                <th md-column>Unit</th>
                <th md-column>Actions</th>
              </tr>
            </thead>
            
            <tbody md-body>
                <tr md-row md-select="product" md-select-id="id" ng-repeat="product in products track by $index">

                    <td md-cell width="200px">
                        <md-input-container>
                            <label>Name</label>
                            <input ng-model="product.name">
                        </md-input-container>
                    </td>

                    <td md-cell>
                        <md-chips  ng-model="product.tags_array" md-removable="true"></md-chips>
                    </td>
                    
                    <td md-cell>
                        <md-checkbox 
                            ng-model="product.popular" 
                            ng-init="product.popular = product.is_popular == 1"
                            ng-change="directEdit($event, product)"
                            aria-label="Is hot item">
                        </md-checkbox>
                    </td>

                    <td md-cell>
                        <image-upload 
                                image="product.image" 
                                caption="" 
                                data="product"
                                on-file-removed="imageRemoved(data)" 
                                on-file-selected="imageChanged(file, data)"></image-upload>
                    </td>

                    <td md-cell>
                        
                        <md-input-container>
                            <label>Sub category</label>
                            <md-select ng-model="product.subcategory_id">
                                <md-option ng-value="subcategory.id" ng-repeat="subcategory in subcategories track by $index">{{ subcategory.name }}</md-option>
                            </md-select>
                        </md-input-container>

                    </td>

                    <td md-cell>
                        
                        <md-input-container>
                            <label>Unit</label>
                            <md-select ng-model="product.unit_id">
                                <md-option ng-value="unit.id" ng-repeat="unit in units track by $index">{{ unit.name }}</md-option>
                            </md-select>
                        </md-input-container>

                    </td>

                    <td md-cell>
                        <md-button class="md-raised md-primary" ng-click="edit_product(product.id)">
                            Edit
                        </md-button>
                        <md-button class="md-raised md-primary" ng-click="directEdit($event, product)">
                            Update
                        </md-button>
                        <md-button class="md-raised md-warn" ng-click="deleteProduct($event, product.id)">
                            Delete
                        </md-button>
                    </td>

                </tr>
            </tbody>
        </table>
    </md-table-container>

    <md-table-pagination md-limit="query.limit" md-limit-options="[20, 40, 100]" md-page="query.page" md-total="{{count}}" md-on-paginate="getProducts" md-page-select></md-table-pagination>


    
</md-content>
