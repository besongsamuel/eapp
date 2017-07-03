<div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Shopping Cart</h2>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Page title area -->

<div id="admin-container" class="container admin-container" ng-controller="CartController">
    
    <md-table-container>
        <table  md-table md-row-select multiple cellspacing="0" ng-model="selected"  md-progress="promise">
            <thead md-head md-order="query.order" md-on-reorder="getCart">
                <tr md-row>
                    <th md-column>&nbsp;</th>
                    <th md-column>Store</th>
                    <th md-column>Product</th>
                    <th md-column md-order-by="nameToLower">Product Description</th>
                    <th md-column md-numeric>Price (CAD)</th>
                    <th md-column md-numeric>Quantity</th>
                    <th md-column md-numeric>Total (CAD)</th>
                    <th md-column><i class="fa fa-heart"></i></th>
                    <th md-column>Coupon</th>
                </tr>
            </thead>
            <tbody>
                <tr  md-row md-select="item"  md-select-id="name" class="cart_item" ng-repeat="item in cart | orderBy: query.order">
                    <td md-cell>
                        <a title="Remove this item" class="remove" href ng-click="removeProductFromCart(item.store_product.id)">×</a> 
                    </td>

                    <td md-cell>
                        <a href="single-product.html"><img width="145" height="145" alt="poster_1_up" class="shop_thumbnail" ng-src="http://<?php echo base_url("assets/img/stores/"); ?>{{item.retailer.image}}" ></a>
                        <div ng-show="item.store_product.departmentStore">
                            <p>{{item.store_product.departmentStore.address}}</p>
                            <p>{{item.store_product.departmentStore.city}}, {{item.store_product.departmentStore.state}} , {{item.store_product.departmentStore.postcode }}</p>
                        </div>
                    </td>

                    <td md-cell>
                        <a href="single-product.html"><img width="145" height="145" alt="poster_1_up" class="shop_thumbnail" ng-src="http://<?php echo base_url("assets/img/products/"); ?>{{item.product.image}}" ></a>
                    </td>

                    <td md-cell>
                        <p><b><a href="single-product.html">{{item.product.name}}</a></b></p>
                        <p>Format : {{item.store_product.format}}</p>
                    </td>

                    <td md-cell>
                        <span class="amount">{{item.store_product.price | number: 2}}</span> 
                    </td>

                    <td md-cell>
                        <input type="number" size="4" class="input-text qty text" title="Qty" value="1" min="0" step="1" ng-model="item.quantity">
                    </td>

                    <td md-cell>
                        <span class="amount">{{item.store_product.price * item.quantity | number : 2}} </span> 
                    </td>

                    <td md-cell>
                        <md-checkbox ng-model="item.is_favorite" aria-label="Add to my list">
                        </md-checkbox>
                    </td>

                    <td md-cell>
                        <md-checkbox ng-model="item.apply_coupon" aria-label="Apply coupon">
                        </md-checkbox> 
                    </td>
                </tr>
            </tbody>
        </table>
        <md-content style="margin: 15px; padding:15px">
            <fieldset>
                <legend>Optimizations</legend>
                <md-radio-group ng-model="optimized_cart" ng-change="updateCartList()">
                        <md-radio-button class="radio-inline" value="0" class="md-primary">View Original List</md-radio-button>
                        <md-radio-button class="radio-inline" value="1"> View Optimized List </md-radio-button>
                    </md-radio-group>
                    <md-slider-container>
                        <span>Km</span>
                        <md-slider flex min="0" max="255" ng-model="distance" aria-label="red" id="red-slider">
                        </md-slider>
                        <md-input-container>
                            <input flex type="number" ng-model="distance" aria-label="red" aria-controls="red-slider">
                        </md-input-container>
                    </md-slider-container>
                    <md-button class="md-raised" ng-click="updateCartList()">Update</md-button>
            </fieldset>
        </md-content>
        <md-table-pagination md-limit="query.limit" md-limit-options="[25, 50, 100]" md-page="query.page" md-total="{{store_products_count}}" md-on-paginate="getCart" md-page-select></md-table-pagination>
    </md-table-container> 
    
    <div class="cart-collaterals">
                                
        <div class="cart_totals ">
            <h2>Optimization Details</h2>

            <table cellspacing="0">
                <tbody>
                    <tr class="cart-subtotal">
                        <th>Cart Total</th>
                        <td><span class="amount">£15.00</span></td>
                    </tr>

                    <tr class="optimized-subtotal">
                        <th>Optimized Cart Total</th>
                        <td><span class="amount">£10.00</span></td>
                    </tr>

                    <tr class="optimized-distance">
                        <th>Optimized Distance</th>
                        <td><span class="amount">4 KM</span></td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>
</div>


