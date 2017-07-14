
<script>
    
    $(document).ready(function()
    {
        var rootScope = angular.element($("html")).scope();
        rootScope.$apply(function()
        {
            rootScope.updateCartList();
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
                        <li><a href="http://<?php echo site_url("home"); ?>">Accueil</a></li>
                        <li><a href="http://<?php echo site_url("shop"); ?>">Magasin</a></li>
                        <li><a href="http://<?php echo site_url("shop"); ?>">Trouver produit</a></li>
                        <li class="active"><a href="http://<?php echo site_url("cart"); ?>">Panier</a></li>
                        <li><a href="#">Catégories</a></li>
                        <li><a href="#">Dépliants</a></li>
                        <li><a href="#">Contactez nous</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div> 
    <!-- End mainmenu area -->

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

<div id="admin-container" class="container admin-container" ng-controller="CartController" ng-show="viewing_cart_optimization">
    
    <!-- Cart Optimizations -->
    <div ng-show="viewing_cart_optimization">
        <md-content layout-padding >
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
                    <tr  md-row md-select="item"  md-select-id="name" class="cart_item" ng-repeat="item in optimized_cart">
                        
                        <td md-cell>
                            <a title="Remove this item" class="remove" href ng-click="removeProductFromCart(item.store_product.id)">×</a> 
                        </td>

                        <td md-cell>
                            <a href><img alt="poster_1_up" class="admin-image" ng-src="http://{{base_url}}/assets/img/stores/{{item.store_product.retailer.image}}" ></a>
                            <div ng-show="item.store_product.departmentStore">
                                <p>{{item.store_product.departmentStore.address}}</p>
                                <p>{{item.store_product.departmentStore.city}}, {{item.store_product.departmentStore.state}} , {{item.store_product.departmentStore.postcode }}</p>
                            </div>
                            <div ng-hide="item.store_product.departmentStore">
                                <p>Pas disponible pres de vous</p>
                            </div>
                        </td>

                        <td md-cell>
                            <a href><img alt="poster_1_up" class="admin-image" ng-src="http://{{base_url}}/assets/img/products/{{item.store_product.product.image}}"></a>
                        </td>

                        <td md-cell>
                            <p><b><a href="single-product.html">{{item.store_product.product.name}}</a></b></p>
                            <p>Format : {{item.store_product.format}}</p>
                        </td>

                        <td md-cell>
                            <span class="amount">{{item.store_product.price | number: 2}}</span> 
                        </td>

                        <td md-cell>
                            <md-input-container>
                                <input type="number" ng-model="item.quantity">
                            </md-input-container>
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
                <md-table-pagination md-limit="query.limit" md-limit-options="[25, 50, 100]" md-page="query.page" md-total="{{store_products_count}}" md-on-paginate="getCart" md-page-select></md-table-pagination>
            </md-table-container>
        </md-content>
    </div>
        
</div>

<div class="container" ng-controller="CartController" ng-hide="viewing_cart_optimization">
  <div>
        <!-- Store Optimizations -->
        <md-content layout-padding >
            <table class="table table-condensed" >
                <md-progress-linear md-mode="indeterminate" ng-disabled="!loading_store_products"></md-progress-linear>
                <thead>
                    <tr>
                        <th>Commerçant</th>
                        <th ng-repeat="store in close_stores">
                            <p>{{store.store.chain.name}}</p>
                        </th>
                    </tr>
                    <tr>
                        <th>Addresse</th>
                        <th ng-repeat="store in close_stores">
                            <p>{{store.store.address}}</p>
                        </th>
                    </tr>
                    <tr>
                        <th>Distance</th>
                        <th ng-repeat="store in close_stores">
                            <p> > {{store.distance}} Km</p>
                        </th>
                    </tr>

                </thead>
                <tbody id="store-cart-tbody">
                    <tr ng-repeat="product in store_products">
            <td>
                <img class="admin-image" ng-src="http://{{base_url}}/assets/img/products/{{product.product.image}}" />
                <p style="width : auto;">{{product.product.name}}</p>
            </td>
            <td ng-repeat="store_product in product.store_products">{{store_product.price}}</td>
        </tr>
                    <tr>
            <td class="store-total-caption"><b>Total</b></td>
            <td class="store-total-value" ng-repeat="store in close_stores">{{store.store_items_cost}}</td>
        </tr>
                    <tr>
            <td class="store-total-caption"><b>Sélectionner</b></td>
            <td class="store-total-checkbox" ng-repeat="store in close_stores">
                <md-checkbox ng-model="store.selected" aria-label="Finished?">
                </md-checkbox>
            </td>
        </tr>
                </tbody>
            </table>
        </md-content>
    </div>
    
  
</div>

<div class="admin-container" ng-controller="CartController">
    <md-content style="margin: 15px; padding:15px">
        <fieldset>
            <legend>Optimizations</legend>
            
                <md-content>
                    <md-button ng-click="updateCartList()" class="md-primary">Optimisation du panier</md-button>
                    <md-button ng-click="update_product_list_by_store()" class="md-primary">Optimisation par magasin</md-button>
                </md-content>
            
                <md-slider-container>
                    <span>Km</span>
                    <md-slider min="0" max="255" ng-model="distance_from_home" ng-change="distance_updated(distance_from_home)" aria-label="red" id="red-slider">
                    </md-slider>
                    <md-input-container>
                        <input type="number" ng-model="distance_from_home" aria-label="red" aria-controls="red-slider">
                    </md-input-container>
                </md-slider-container>
                <md-button class="md-raised" ng-click="updateCartList()">Update</md-button>
        </fieldset>
    </md-content>
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

    
        
    




