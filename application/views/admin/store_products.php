<!DOCTYPE html>

<!-- Main Script -->
<script src="http://<?php echo base_url("assets/js/admin-controller.js")?>"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="http://<?php echo base_url("assets/css/shop.css")?>">

<script>
    $(document).ready(function()
    {
        var scope = angular.element($("#admin-container")).scope();
        
        scope.$apply(function()
        {
            scope.base_url = "<?php echo $base_url; ?>";
            scope.site_url = "<?php echo $site_url; ?>";
            scope.controller = "<?php echo $controller; ?>";
            scope.method = "<?php echo $method; ?>";
        });
    });
</script>

<div id="admin-container" class="container admin-container" ng-controller="ProductsTableController">
    
    <md-toolbar class="md-table-toolbar md-default" ng-hide="selected.length || filter.show">
        <div class="md-toolbar-tools">
        <h2 class="md-title">Products</h2>
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

    <md-toolbar class="md-table-toolbar alternate" ng-show="selected.length">
      <div class="md-toolbar-tools" layout-align="space-between">
        <div>{{selected.length}} {{selected.length > 1 ? 'items' : 'item'}} selected</div>
        <md-button class="md-icon-button" ng-click="add_to_cart($event)">
            <md-icon>add_shopping_cart</md-icon>
        </md-button>
      </div>
    </md-toolbar>
    
    <md-table-container>
        <table  md-table md-row-select multiple cellspacing="0" ng-model="selected"  md-progress="promise">
            <thead md-head md-order="query.order" md-on-reorder="getProducts">
                <tr md-row>
                    <th md-column>&nbsp;</th>
                    <th md-column>Store Logo</th>
                    <th md-column>Image</th>
                    <th md-column md-order-by="name">Product</th>
                    <th md-column md-numeric>Price</th>
                    <th md-column md-numeric>Quantity</th>
                    <th md-column md-numeric>Unit Price</th>
                    <th md-column md-order-by="date">Validity Period</th>
                    <th md-column>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr  md-row md-select="store_product"  md-select-id="name" md-auto-select class="cart_item" ng-repeat="store_product in query_products">
                    <td md-cell>
                        <a title="Remove this item" class="remove" href ng-click="delete_store_product(store_product.id)">×</a> 
                    </td>

                    <td md-cell>
                        <div class="admin-image"><a href=""><img alt="" ng-src="http://<?php echo base_url("assets/img/stores/"); ?>{{retailers[store_product.retailer_id].image}}" ></a></div>
                    </td>

                    <td md-cell>
                        <div class="admin-image"><a href=""><img alt="" ng-src="http://<?php echo base_url("assets/img/products/"); ?>{{products[store_product.product_id].image}}" ></a></div>
                    </td>

                    <td md-cell width="100%">
                        <p><b><a href="single-product.html">{{products[store_product.product_id].name}}</a></b></p>
                        <p>Format : {{store_product.format}}</p>
                        <p>Unit : {{units[store_product.unit_id].name}}</p>
                    </td>

                    <td md-cell>
                        <span class="amount">CAD {{store_product.price}}</span> 
                    </td>

                    <td md-cell>
                        {{store_product.quantity}}
                    </td>

                    <td md-cell>
                        <span class="amount">CAD {{store_product.unit_price}}</span> 
                    </td>

                    <td md-cell>
                        {{store_product.period_from}} to {{store_product.period_to}} 
                    </td>

                    <td md-cell>
                        <a ng-href="http://<?php echo site_url("admin/create_store_product"); ?>/{{store_product.id}}">Edit</a> 
                    </td>
                </tr>
            </tbody>
        </table>
        <md-table-pagination md-limit="query.limit" md-limit-options="[10, 25, 50]" md-page="query.page" md-total="{{count}}" md-on-paginate="getProducts" md-page-select></md-table-pagination>
    </md-table-container>                        
</div>

<script>
$(document).ready(function()
{
    var scope = angular.element($("#admin-container")).scope();
    
    scope.$apply(function()
    {
        scope.store_products = JSON.parse('<?php echo $store_products; ?>');
        scope.products = JSON.parse('<?php echo $products; ?>');
        scope.retailers = JSON.parse('<?php echo $retailers; ?>');
        scope.units = JSON.parse('<?php echo $units; ?>');
        
        scope.store_products_count = Object.keys(scope.store_products).length;
        
        scope.getProducts();
        
    });
});
</script>
