<!DOCTYPE html>
    
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

<md-content id="cart-container" class="admin-container" ng-controller="CartController" ng-cloak>
    
    <div>
        <md-content class="eapp-container md-whiteframe-3dp">
            <fieldset>
                <md-subheader class="md-primary">Configurez votre optimization du panier</md-subheader>
                <md-radio-group class="col-md-6 col-sm-12" ng-model="viewing_cart_optimization.value" ng-change="optimization_preference_changed()">
                    <md-radio-button ng-value="true_value">Vue du panier</md-radio-button>
                    <md-radio-button ng-value="false_value">Vue par magasin</md-radio-button>
                </md-radio-group>
                <div  class="col-md-6 col-sm-12">
                    <md-radio-group ng-model="searchInMyList.value" ng-change="optimization_preference_changed()">
                        <md-radio-button ng-value="true_value" ng-disabled="!isUserLogged">Rechercher dans votre liste de magasins</md-radio-button>
                        <md-radio-button ng-value="false_value">Rechercher dans tout les magasins</md-radio-button>
                    </md-radio-group>
                </div>
                
                <div layout class="col-sm-12">
                    <div flex="15" layout layout-align="center center">
                      <span class="md-body-1">Distance : {{distance}} Km</span>
                    </div>
                    <md-slider flex class="md-primary" md-discrete ng-model="distance" step="1" min="1" max="100" aria-label="Distance">
                    </md-slider>
                    <md-button class="md-raised" ng-click="optimization_preference_changed()">Mettre à jour</md-button>
                </div>
                
            </fieldset>
            <md-progress-linear md-mode="indeterminate" ng-show="promise.$$state.status === 0"></md-progress-linear> 
    	</md-content>
    </div>

    <div id="cart-optimization-container" ng-show="viewing_cart_optimization.value">
        <!-- Cart Optimizations -->
        <md-content class="container" ng-repeat="departmentStore in departmenStores">
            
            <md-table-container>
                <table  md-table cellspacing="0" ng-model="selected"  md-progress="promise">
                    <thead md-head md-order="query.order" md-on-reorder="update_cart_list">
                        <tr md-row ng-show="$index === 0">
                        <th md-column>&nbsp;</th>
                        <th md-column>Product</th>
                        <th md-column>Description du produit</th>
                        <th md-column md-numeric>Quantité</th>
                        <th md-column md-numeric>Total ($ CAD)</th>
                        <th md-column  ng-show="isUserLogged"><i class="fa fa-heart"></i></th>
                        <th md-column ng-hide="true">Coupon</th>
                    </tr>
                </thead>
                    <tbody>
                    
                    <tr>
                        <td colspan="6">
                            <md-subheader class="" ng-show="departmentStore.fullName">
                                <img alt="{{ product.name }}" ng-src="{{departmentStore.image}}" style="height : 44px;" />
                                <b> <a href ng-click="InitMap($event, departmentStore)">{{departmentStore.fullName}}, {{departmentStore.distanceText}} en voiture (environs {{departmentStore.timeText}} )</a></b>
                            </md-subheader>
                            <md-subheader class="md-warn" ng-hide="departmentStore.distance">
                                <img alt="{{ product.name }}" ng-src="{{departmentStore.image}}" style="height : 44px;" />
                                <b> Le magasin n'est pas disponible près de chez vous.</b>
                            </md-subheader> 
                        </td>
                    </tr>
                    <tr  md-row md-select="item"  md-select-id="name" class="cart_item" ng-repeat="item in departmentStore.products">

                        <td md-cell>
                            <a title="Remove this item" class="remove" href ng-click="remove_product_from_cart(item.product.id)">×</a> 
                        </td>


                        <td md-cell width = "20%">
                            <a href><img alt="poster_1_up" class="admin-image" ng-src="{{item.store_product.product.image}}"></a>
                        </td>

                        <td md-cell width = "30%">
                            <p><b><a href="<?php echo site_url("cart/product/"); ?>{{item.store_product.product.id}}">{{item.store_product.name}}</a></b></p>
                            <p ng-show="item.different_store_products.length !== 0">{{item.store_product.retailer.name}} | <a href ng-click="productStoreChanged($event, item)">Changer Marchand</a></p>
                            <p ng-show="item.store_product.size">{{item.store_product.size}}</p>
                            <p ng-show="item.store_product.brand">{{item.store_product.brand.name}}</p>
                            <p>
                                {{item.store_product.format}} <span ng-show="item.store_product.unit"> {{item.store_product.unit.name}}</span>
                                <span ng-hide="item.different_format_products.length === 0">
                                   | <a href ng-click="productFormatChanged($event, item)">Changer Format</a>
                                </span>
                            </p>
                            
                            <p ng-show="item.store_product.state">Origine : {{item.store_product.state}}</p>
                            <p  ng-show="item.store_product.price > 0"><b><span class="amount">$ CAD {{item.store_product.price | number: 2}}</span> <span class="badge md-otiprix" ng-show="getCartItemRebate(item) > 0">Optimisé</span> </b></p>
                        </td>

                        <td md-cell width = "10%">
                            <md-input-container>
                                <label>Quantité</label>
                                <input aria-label="Qty" type="number" ng-model="item.quantity" ng-change="update_price_optimization()">
                            </md-input-container>
                        </td>

                        <td md-cell>
                            <span class="amount"><b>$ CAD {{item.store_product.price * item.quantity | number : 2}} </b></span> 
                        </td>

                        <td md-cell ng-show="isUserLogged">
                        <md-checkbox ng-model="item.store_product.product.in_user_grocery_list" aria-label="Add to my list" ng-init="item.store_product.product.in_user_grocery_list" ng-checked="item.store_product.product.in_user_grocery_list" ng-change="favoriteChanged(item.store_product.product)">
                            
                        </md-checkbox>
                        </td>

                        <td md-cell ng-hide="true">
                            <md-checkbox ng-model="item.apply_coupon" aria-label="Apply coupon">
                            </md-checkbox> 
                        </td>
                    </tr>
                </tbody>
                </table>
            </md-table-container>
        </md-content>
    </div>

    <div class="container" ng-cloak ng-hide="viewing_cart_optimization.value">
        <md-content>
          
            <md-tabs md-dynamic-height md-border-bottom>
              
                <md-tab label="{{store.name}} ({{store.store_products.length}} / {{cart.length}})" ng-repeat="store in stores.slice(0, 5)" md-on-select="storeTabSelected(store)">
                  <md-content class="md-padding">
                        <md-subheader class="md-primary"><a href  ng-click="InitMap($event, store.department_store)">{{store.department_store.fullName}}, {{store.department_store.distanceText}} en voiture (environs {{store.department_store.timeText}})</a></md-subheader>
                        <md-subheader>Produits disponibles <span class="badge">{{store.store_products.length}}</md-subheader>

                        <md-list-item ng-repeat="item in store.store_products" class="noright">
                            <img alt="{{ item.store_product.name }}" ng-src="{{ item.store_product.product.image }}" class="md-avatar" />
                            <div class="md-list-item-text" layout="column">
                                <a  href="<?php echo site_url("cart/product/"); ?>{{item.store_product.product.id}}">{{ item.store_product.product.name }}</a>
                                <p ng-show="item.store_product.format">{{item.store_product.format}}<span ng-show="item.store_product.unit"> {{item.store_product.unit.name}}</span></p>
                                <p ng-show="item.store_product.brand">{{item.store_product.brand.name}}</p>
                            </div>
                            
                            <md-input-container class="md-secondary">
                                <p><b>$ CAD {{item.store_product.price}}</b></p>
                            </md-input-container>
                        </md-list-item>
                      
                        <md-subheader class="md-warn"><a class="md-warn" href  data-toggle="collapse" data-target="#products_{{store.id}}">Voir produits indisponibles</a> <span class="badge">{{store.missing_products.length}}</md-subheader>
                        <div id="products_{{store.id}}" class="collapse">
                            <md-list-item ng-repeat="item in store.missing_products" class="noright">
                              <img alt="{{ item.store_product.name }}" ng-src="{{ item.store_product.product.image }}" class="md-avatar" />
                              <div class="md-list-item-text" layout="column">
                                <a  href="<?php echo site_url("cart/product/"); ?>{{item.store_product.product.id}}">{{ item.store_product.product.name }}</a>
                                <p ng-show="item.store_product.format">{{item.store_product.format}}<span ng-show="item.store_product.unit"> {{item.store_product.unit.name}}</span></p>
                                <p ng-show="item.store_product.brand">{{item.store_product.brand.name}}</p>
                              </div>
                              <img  alt="{{ product.name }}" ng-src="{{item.store_product.retailer.image }}" class="md-secondary md-avatar" />
                              <md-input-container class="md-secondary">
                                  <p><b>$ CAD {{item.store_product.price}} <span ng-show="item.store_product.unit"> / {{item.store_product.unit.name}}</span></b></p>
                              </md-input-container>
                          </md-list-item>
                        </div>
                      
                  </md-content>
              </md-tab>
            </md-tabs>
        </md-content>
    </div>
    
    <div>
        <div class="container" style="margin-bottom: 10px; margin-top: 10px;">

            <div class="cart_totals pull-right">
                <h2>Détails d'optimisation</h2>

                <table>
                    <tbody>
                        
                        <tr class="cart-subtotal">
                            <th>Total des produits disponibles</th>
                            <td><span class="amount md-warn"><b>$ CAD {{totalPriceAvailableProducts | number : 2}}</b></span></td>
                        </tr>
                        
                        <tr class="cart-subtotal">
                            <th>Total des produits non disponibles</th>
                            <td><span class="amount" style="color : gray;"><b>$ CAD {{totalPriceUnavailableProducts | number : 2}}</b></span></td>
                        </tr>
                        
                        <tr class="cart-subtotal">
                            <th>Total du panier</th>
                            <td><span class="amount"><b>$ CAD {{get_cart_total_price() | number : 2}}</b></span></td>
                        </tr>
			
                        <tr class="optimized-distance" ng-show="price_optimization > 0">
                            <th>Montant épargné</th>
                            <td><span class="amount"><b style="color : red"><b> $ CAD {{price_optimization | number : 4}} </b></span></td>
                        </tr>

                    </tbody>
                </table>
				
                <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>

                    <md-button class="md-fab md-warn"  ng-click="clearCart()" aria-label="Effacer votre panier">
                        <md-tooltip
                            md-direction="bottom">
                            Effacer votre panier
                        </md-tooltip>
                        <md-icon><i class="material-icons">clear_all</i></md-icon>
                    </md-button>
                    <md-button class="md-fab md-otiprix" aria-label="Impression" ng-click="printCart()">
                        <md-tooltip
                            md-direction="bottom">
                            Impression
                        </md-tooltip>
                        <md-icon><i class="material-icons">print</i></md-icon>
                    </md-button>

                    <md-button class="md-fab md-otiprix" ng-click="sendListAsSMS($event)" aria-label="Envoyer à votre téléphone">
                        <md-tooltip
                            md-direction="bottom">
                            Envoyer par sms
                        </md-tooltip>
                      <md-icon><i class="material-icons">send</i></md-icon>
                    </md-button>

                    <md-button class="md-fab md-otiprix" aria-label="Partager" ng-hide="true">
                        <md-tooltip
                            md-direction="bottom">
                            Partager
                        </md-tooltip>
                        <md-icon><i class="material-icons">share</i></md-icon>
                    </md-button>

                    <md-button class="md-fab md-otiprix" aria-label="Envoyer à votre courrier électronique" ng-click="sendListAsEmail($event)">
                        <md-tooltip
                            md-direction="bottom">
                            Envoyer à votre courrier électronique
                        </md-tooltip>
                        <md-icon><i class="material-icons">email</i></md-icon>
                    </md-button>

                </section>
				
            </div>
        </div>
    </div>
</md-content>



    
        
    




