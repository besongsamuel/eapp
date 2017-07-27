
<script>
    
    $(document).ready(function()
    {
        var scope = angular.element($("#cart-optimization-container")).scope();
        scope.$apply(function()
        {
            scope.update_cart_list();
        });
    });
    
</script>
    
<div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Mon Panier</h2>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Page title area -->

<md-content class="admin-container" ng-controller="CartController">
    
    <div>
        <md-content style="margin: 15px; padding:15px">
			<fieldset>
				<legend>Optimizations</legend>
				<md-radio-group ng-model="viewing_cart_optimization.value" ng-change="optimization_preference_changed()">
					<md-radio-button ng-value="true_value">Optimisation du panier</md-radio-button>
					<md-radio-button ng-value="false_value">Optimisation par magasin</md-radio-button>
				</md-radio-group>
				
				<h2 class="search-preference">Rechercher dans ...</h2>
				<md-radio-group ng-model="searchInMyList" ng-change="optimization_preference_changed()">
					<md-radio-button ng-value="true_value">Votre liste prefere</md-radio-button>
					<md-radio-button ng-value="false_value">Tout les magasins</md-radio-button>
				</md-radio-group>

				<md-slider-container>
					<span>Km</span>
					<md-slider min="0" max="255" ng-model="distance" aria-label="red" id="red-slider">
					</md-slider>
					<md-input-container>
						<input type="number" ng-model="distance" aria-label="red" aria-controls="red-slider">
					</md-input-container>
				</md-slider-container>
				<md-button class="md-raised" ng-click="optimization_preference_changed()">Mettre à jour</md-button>
			</fieldset>
    	</md-content>
    </div>

    <div id="cart-optimization-container" class="" ng-show="viewing_cart_optimization.value">
        <!-- Cart Optimizations -->
        <md-content>
            <md-table-container>
                <table  md-table md-row-select multiple cellspacing="0" ng-model="selected"  md-progress="promise">
                    <thead md-head md-order="query.order" md-on-reorder="update_cart_list">
                    <tr md-row>
                        <th md-column>&nbsp;</th>
                        <th md-column>Store</th>
                        <th md-column>Address</th>
                        <th md-column>Product</th>
                        <th md-column>Product Description</th>
                        <th md-column md-numeric>Quantity</th>
                        <th md-column md-numeric>Total (CAD)</th>
                        <th md-column><i class="fa fa-heart"></i></th>
                        <th md-column>Coupon</th>
                    </tr>
                </thead>
                    <tbody>
                    <tr  md-row md-select="item"  md-select-id="name" class="cart_item" ng-repeat="item in cart">

                        <td md-cell>
                            <a title="Remove this item" class="remove" href ng-click="remove_product_from_cart(item.product.id)">×</a> 
                        </td>

                        <td md-cell>
                            <a href><img alt="poster_1_up" class="admin-image" ng-src="http://{{base_url}}/assets/img/stores/{{item.store_product.retailer.image}}" ></a>
                        </td>

                        <td md-cell>
                            <div ng-show="item.store_product.department_store">
                                <p>{{item.store_product.department_store.address}}</p>
                                <p>{{item.store_product.department_store.city}}, {{item.store_product.department_store.state}} , {{item.store_product.department_store.postcode }}</p>
                                <p> < {{item.store_product.department_store.distance}} Km</p>
                            </div>
                            <div ng-hide="item.store_product.department_store">
                                <p>Pas disponible pres de vous</p>
                            </div>
                        </td>

                        <td md-cell>
                            <a href><img alt="poster_1_up" class="admin-image" ng-src="http://{{base_url}}/assets/img/products/{{item.product.image}}"></a>
                        </td>

                        <td md-cell>
                            <p><b><a href="single-product.html">{{item.store_product.product.name}}</a></b></p>
                            <p>Format : {{item.store_product.format}}</p>
                            <p><span class="amount">CAD {{item.store_product.price | number: 2}}</span> </p>
                        </td>

                        <td md-cell>
                            <md-input-container>
                                <input aria-label="Qty" style="width : 50px;" type="number" ng-model="item.quantity">
                            </md-input-container>
                        </td>

                        <td md-cell>
                            <span class="amount">CAD {{item.store_product.price * item.quantity | number : 2}} </span> 
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
            </md-table-container>
        </md-content>
    </div>

    <div id="store-optimization-container" class="container" ng-hide="viewing_cart_optimization.value">
        <!-- Store Optimizations -->
        <md-content layout-padding >
            <table class="table table-condensed" style="table-layout: fixed;">
                <md-progress-linear md-mode="indeterminate" ng-disabled="!loading_store_products"></md-progress-linear>
                <thead>
                    <tr>
                        <th><p>Commerçant</p></th>
                        <th ng-repeat="store in close_stores">
                            <p>{{store.store.chain.name}}</p>
                        </th>
                    </tr>
                    <tr>
                        <th></th>
                        <th ng-repeat="store in close_stores">
                            <img class="admin-image" ng-src="http://{{base_url}}/assets/img/stores/{{store.store.chain.image}}" />
                        </th>
                    </tr>
                    <tr>
                        <th><p>Addresse</p></th>
                        <th ng-repeat="store in close_stores">
                            <p>{{store.store.address}}</p>
                        </th>
                    </tr>
                    <tr>
                        <th><p>Distance</p></th>
                        <th ng-repeat="store in close_stores">
                            <p> > {{store.distance}} Km</p>
                        </th>
                    </tr>

                </thead>
                <tbody id="store-cart-tbody">
                    <tr ng-repeat="product in cart">
                        <td>
                            <img class="admin-image" ng-src="http://{{base_url}}/assets/img/products/{{product.product.image}}" />
                            <p style="width : auto;">{{product.product.name}}</p>
                            <md-input-container class='col-sm-6'>
                                <label>Quantity</label>
                                <input aria-label="Qty" type="number" ng-model="product.quantity">
                            </md-input-container>
                        </td>
                        <td ng-repeat="store_product in product.store_products">{{store_product.price == 0 ? 'Item pas disponible' : 'CAD ' + store_product.price * product.quantity}}</td>
                    </tr>
                    <tr>
                        <td class="store-total-caption"><b>Total</b></td>
                        <td class="store-total-value" ng-repeat="store in close_stores">CAD {{get_store_total($index) | number : 2}} </td>
                    </tr>
                    <tr>
                        <td class="store-total-caption"><b>Total d'items</b></td>
                        <td class="store-total-value" ng-repeat="store in close_stores"><b>{{store.num_items}}</b></td>
                    </tr>
                    <tr>
                        <td class="store-total-caption"><b>Sélectionner</b></td>
                        <td class="store-total-checkbox" ng-repeat="store in close_stores">
                            <md-checkbox ng-model="store.selected" ng-change="store_selected($index)"  aria-label="Select Store">
                            </md-checkbox>
                        </td>
                    </tr>
                </tbody>
            </table>
        </md-content>
    </div>

    <div>
        <div class="container">

            <div class="cart_totals ">
                <h2>Détails d'optimisation</h2>

                <table>
                    <tbody>
                        <tr class="cart-subtotal">
                            <th>Total du panier</th>
                            <td><span class="amount">CAD {{get_cart_total_price() | number : 2}}</span></td>
                        </tr>

                        <tr class="optimized-distance">
                            <th>Distance de voyage</th>
                            <td><span class="amount"> < {{travel_distance}} Km</span></td>
                        </tr>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</md-content>



    
        
    




