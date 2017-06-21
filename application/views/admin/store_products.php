<!DOCTYPE html>

<md-toolbar class="md-table-toolbar md-default">
  <div class="md-toolbar-tools">
    <span>Store Products</span>
  </div>
</md-toolbar>

<div id="admin-container" class="container admin-container" ng-controller="ProductsTableController">
    
    <md-table-container>
        <table  md-table md-row-select multiple cellspacing="0" ng-model="selected"  md-progress="promise">
            <thead md-head md-order="query.order" md-on-reorder="getProducts">
                <tr md-row>
                    <th md-column>&nbsp;</th>
                    <th md-column>Store Logo</th>
                    <th md-column>Image</th>
                    <th md-column md-order-by="nameToLower">Product</th>
                    <th md-column md-numeric>Price</th>
                    <th md-column md-numeric>Quantity</th>
                    <th md-column md-numeric>Unit Price</th>
                    <th md-column>Validity Period</th>
                    <th md-column>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr  md-row md-select="store_product"  md-select-id="name" md-auto-select class="cart_item" ng-repeat="store_product in query_products">
                    <td md-cell>
                        <a title="Remove this item" class="remove" href="#">×</a> 
                    </td>

                    <td md-cell>
                        <a href="single-product.html"><img width="145" height="145" alt="poster_1_up" class="shop_thumbnail" ng-src="http://<?php echo base_url("assets/img/stores/"); ?>{{retailers[store_product.retailer_id].image}}" ></a>
                    </td>

                    <td md-cell>
                        <a href="single-product.html"><img width="145" height="145" alt="poster_1_up" class="shop_thumbnail" ng-src="http://<?php echo base_url("assets/img/products/"); ?>{{products[store_product.product_id].image}}" ></a>
                    </td>

                    <td md-cell>
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
        <md-table-pagination md-limit="query.limit" md-limit-options="[10, 25, 50]" md-page="query.page" md-total="{{store_products_count}}" md-on-paginate="getProducts" md-page-select></md-table-pagination>
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