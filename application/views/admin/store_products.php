<!DOCTYPE html>

<style>
    
    .image-container img
    {
        border-radius: 0px;
        width: 160px;
        height: 130px;
    }
</style>

<!-- Main Script -->
<script src="<?php echo base_url("assets/js/admin-controller.js")?>"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div id="admin-container" class="otiprix-section" ng-controller="ProductsTableController" ng-cloak>
    
    <md-toolbar class="md-primary" style="margin-bottom : 10px;">
        <div>
            <h2 class="md-toolbar-tools">Products</h2>
        </div>
    </md-toolbar>
    
    <md-table-container class="layout-padding">
        
	    
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


        <table  md-table cellspacing="0" ng-model="selected"  md-progress="promise">
            <thead md-head md-order="query.order" md-on-reorder="getProducts">
                <tr md-row>
                    <th md-column>&nbsp;</th>
                    <th md-column></th>
                    <th md-column></th>
                    <th md-column>Description</th>
                    <th md-column>Price</th>
                    <th md-column md-order-by="period_from">Validity Period</th>
                    <th md-column>Actions</th>
                </tr>
            </thead>
            <tbody>
                
                <tr  md-row md-select="store_product"  md-select-id="name" class="cart_item" ng-repeat="store_product in query_products">
                    <td md-cell>
                        <a title="Remove this item" class="remove" href ng-click="delete_store_product(store_product.id)">×</a> 
                    </td>

                    <td md-cell>
                        <div class="image-container"><a href=""><img alt="" ng-src="{{store_product.retailer.image}}" ></a></div>
                    </td>

                    <td md-cell>
                        <div class="image-container"><a href=""><img alt="" ng-src="{{store_product.product.image}}" ></a></div>
                    </td>

                    <td md-cell width="30%">
                        <p><b><a ng-href="<?php echo site_url("admin/create_store_product?product"); ?>={{store_product.id}}">{{store_product.product.name}}</a></b></p>
                        <p>Size : {{store_product.size}}</p>
                        <p>{{store_product.format}} {{store_product.unit.name}}</p>
                    </td>

                    <td md-cell width="30%">
                        <p><span class="amount">CAD {{store_product.price}}</span></p> 
                        <p>Unit Price : <span class="amount">CAD {{store_product.unit_price}}</span></p>
                    </td>

                    <td md-cell>
                        {{store_product.period_from}} to {{store_product.period_to}} 
                    </td>

                    <td md-cell>
                        <a ng-href="<?php echo site_url("admin/create_store_product?product"); ?>={{store_product.id}}">Edit</a> 
                    </td>
                </tr>
            </tbody>
        </table>
        <md-table-pagination md-limit="query.limit" md-limit-options="[10, 25, 50]" md-page="query.page" md-total="{{count}}" md-on-paginate="getProducts" md-page-select></md-table-pagination>
    </md-table-container>                        
</div>
