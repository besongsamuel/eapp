<!DOCTYPE html>
   
<link href="<?php echo base_url("assets/css/cart.css"); ?>" rel="stylesheet">

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

<div id="cart-container" class="admin-container" ng-controller="CartController" ng-cloak>
    
    <div class="col-12">
        <p style="text-align: center;">Résultats dans un rayon de {{getDistance()}} km
            <span> | <a href ng-click="changeDistance($event)">Changer</a></span>
        </p>
    </div>
    
    <div>
        <div class="eapp-container md-whiteframe-3dp">
            <fieldset>
                <md-subheader class="md-primary">Configurez votre optimization du panier</md-subheader>
                
                <div  class="col-md-12 col-sm-12">
                    <md-radio-group ng-model="root.cartSettings.cartView" ng-change="optimization_preference_changed()" class="col-sm-12">
                        <div class="row">
                            <md-radio-button ng-value="true_value" class="col-sm-6">Vue du panier</md-radio-button>
                            <md-radio-button ng-value="false_value" class="col-sm-6">Vue par magasin</md-radio-button>
                        </div>
                    </md-radio-group>
                </div>
                
                
                <div  class="col-md-12 col-sm-12" ng-show="isUserLogged">
                    <md-radio-group ng-model="root.cartSettings.searchMyList" ng-change="optimization_preference_changed()" class="col-sm-12">
                        <div class="row">
                            <md-radio-button ng-value="false_value" class="col-sm-6">Rechercher dans tout les magasins</md-radio-button>
                            <md-radio-button ng-value="true_value" class="col-sm-6">Rechercher dans votre liste de magasins</md-radio-button>
                        </div>
                    </md-radio-group>
                </div>
                
                <div  class="col-md-12 col-sm-12">
                    <md-radio-group ng-model="root.cartSettings.optimizedCart" ng-change="listChanged()" class="col-sm-12" ng-init="false">
                        <div class="row">
                            <md-radio-button ng-value="true_value" class="col-sm-6">Voir la liste optimisée</md-radio-button>
                            <md-radio-button ng-value="false_value" class="col-sm-6">Voir la liste originale</md-radio-button>
                        </div>
                    </md-radio-group>
                </div>
                                
            </fieldset>
            <md-progress-linear md-mode="indeterminate" ng-show="promise.$$state.status === 0"></md-progress-linear> 
    	</div>
    </div>
    
    <p ng-hide="results_available" class="md-otiprix-text" style="text-align: center; margin-bottom: 50px; margin-top: 20px;"><b>Il n'y a aucun résultat</b></p>

    <div id="cart-optimization-container" ng-show="cartSettings.cartView">
        
        <div class="container" ng-repeat="departmentStore in departmenStores">
            
            <div class="row">
                <md-subheader class="" ng-show="departmentStore.fullName">
                    <img alt="{{ product.name }}" ng-src="{{departmentStore.image}}" style="height : 44px;" />
                    <b> <a href ng-click="InitMap($event, departmentStore)">{{departmentStore.fullName}}, {{departmentStore.distanceText}} en voiture (environs {{departmentStore.timeText}} )</a></b>
                </md-subheader>
                <md-subheader class="md-warn" ng-hide="departmentStore.distance">
                    <img alt="{{ product.name }}" ng-src="{{departmentStore.image}}" style="height : 44px;" />
                    <b> Le magasin n'est pas disponible près de chez vous.</b>
                </md-subheader>
            </div>
            
            <div class="container" id="my_cart">
                <div ng-repeat="category in departmentStore.categories">
                    
                    <div class="row">
                        <p class="category-name"><b> - {{category.name | uppercase}} - </b></p>
                    </div>
                    
                    
                    <div ng-repeat="item in category.products">
                        
                        <md-divider ng-if="$first"></md-divider>
                        
                        <cart-list-item iscartview='cartSettings.cartView' item='item' on-delete='removeFromCart(id)' on-update='productChanged(sp)' on-update-quantity='updateCartQuantity(quantity, id)'></cart-list-item>
                        
                        <md-divider></md-divider>
                        
                    </div>
                    
                    
                </div>
            </div>
        </div>
        
    </div>

    <div class="container" ng-cloak ng-hide="cartSettings.cartView">
        <div  ng-show="results_available">
            <md-tabs md-dynamic-height md-border-bottom>
                
                
                <md-tab label="{{store.name}} ({{store.store_products.length}} / {{cart.length}})" ng-repeat="store in stores.slice(0, 5)" md-on-select="storeTabSelected(store)">
                  <div class="md-padding">
                      <md-subheader class="md-primary"><a class="image-container" href><img alt="poster_1_up" ng-src="{{store.image}}"></a><a href  ng-click="InitMap($event, store.department_store)"><p style="text-align: center; margin-top: 10px;">{{store.department_store.fullName}}, {{store.department_store.distanceText}} en voiture (environs {{store.department_store.timeText}})</p></a></md-subheader>
                        <md-subheader>Produits disponibles <span class="badge">{{store.store_products.length}}</md-subheader>
                        
                        <div class="container" id="my_cart">
                            <div ng-repeat="category in productCategories">

                                <div class="row">
                                    <p class="category-name"><b> - {{category.name | uppercase}} - </b></p>
                                </div>

                                <div ng-repeat="item in category.products">
                                    
                                    
                                    <md-divider ng-if="$first"></md-divider>
                                    
                                        <cart-list-item iscartview='cartSettings.cartView' item='item' on-delete='removeFromCart(id)' on-update='productChanged(sp)' on-update-quantity='updateCartQuantity(quantity, id)'></cart-list-item>

                                    <md-divider></md-divider>

                                </div>


                            </div>
                        </div>
                        
                      
                        <md-subheader class="md-warn"><a class="md-warn" href  data-toggle="collapse" data-target="#products_{{store.id}}">Voir produits indisponibles</a> <span class="badge">{{store.missing_products.length}}</md-subheader>
                        <div id="products_{{store.id}}" class="collapse">
                            <div ng-repeat="missingItem in store.missing_products" class="noright">
                                <cart-list-item iscartview='cartSettings.cartView' view-retailer-image='true' item='missingItem' on-delete='removeFromCart(id)' on-update='productChanged(sp)' on-update-quantity='updateCartQuantity(quantity, id)'></cart-list-item>
                            </div>
                        </div>
                      
                  </div>
              </md-tab>
            </md-tabs>
        </div>
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
                            <td><span class="amount"><b>$ CAD {{totalPriceUnavailableProducts + totalPriceAvailableProducts | number : 2}}</b></span></td>
                        </tr>
			
                        <tr class="optimized-distance" ng-show="price_optimization > 0">
                            <th>Montant épargné</th>
                            <td><span class="amount"><b style="color : red"><b>$ CAD <span ng-show="show_min_price_optimization">{{min_price_optimization | number : 2}} - </span> {{price_optimization | number : 2}} </b></span></td>
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

                    <md-button class="md-fab md-otiprix" ng-click="sendListAsSMS($event)" aria-label="Envoyer à votre téléphone" ng-disabled="!isUserLogged">
                        <md-tooltip
                            md-direction="bottom">
                            Envoyer par sms
                        </md-tooltip>
                      <md-icon><i class="material-icons">smartphone</i></md-icon>
                    </md-button>

                    <md-button class="md-fab md-otiprix" aria-label="Partager" ng-hide="true">
                        <md-tooltip
                            md-direction="bottom">
                            Partager
                        </md-tooltip>
                        <md-icon><i class="material-icons">share</i></md-icon>
                    </md-button>

                    <md-button class="md-fab md-otiprix" aria-label="Envoyer à votre courrier électronique" ng-click="sendListAsEmail($event)"  ng-disabled="!isUserLogged">
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
    
    <div>
        <p class="cart-warning"><b>En cas de différence entre la circulaire du magasin et OTIPRIX, la circulaire a préséance</b></p>
    </div>
</div>





    
        
    




