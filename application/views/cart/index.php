<!DOCTYPE html>
   
<link href="<?php echo base_url("assets/css/cart.css"); ?>" rel="stylesheet">



<md-content class="otiprix-section" id="cart-container" ng-controller="CartController" ng-cloak>
          
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
    
    <md-content class="layout-padding">
        
        <current-address></current-address>
        
        <div class="row">
            <div class="col-md-2">
                <result-filter 
                    type="CART" 
                    ng-if="ready" 
                    distance="default_distance" 
                    on-distance-changed="changeCartDistance(distance)" 
                    view-config="root.cartSettings" on-refresh="refresh(viewConfig)" 
                    ready="ready" 
                    is-user-logged="isUserLogged"
                    result-set="cartFilterSettings" 
                    on-settings-changed="settingsChanged(item)" >
                        
                </result-filter>
            </div>
            
            <div class="col-md-10 white-background">
                
                <md-card>
                    
                    <md-progress-linear md-mode="indeterminate" ng-show="promise.$$state.status === 0"></md-progress-linear>
                    
                    <p ng-hide="results_available" class="md-otiprix-text" style="text-align: center; margin-bottom: 50px; margin-top: 20px;"><b>Votre panier est vide.</b></p>
                    
                    <div id="cart-optimization-container" class="layout-padding" ng-show="cartSettings.cartView">
                        <div ng-repeat="departmentStore in departmenStores">

                            <div class="row layout-padding">

                                <md-divider></md-divider>

                                <div class="" ng-show="departmentStore.fullName">
                                    <img alt="{{ product.name }}" ng-src="{{departmentStore.image}}" style="height : 44px;" />
                                    <b> <a href ng-click="InitMap($event, departmentStore)">{{departmentStore.fullName}}, {{departmentStore.distanceText}} en voiture (environs {{departmentStore.timeText}} )</a></b>
                                </div>
                                <div class="md-warn" ng-hide="departmentStore.distance">
                                    <img alt="{{ product.name }}" ng-src="{{departmentStore.image}}" style="height : 44px;" />
                                    <b> Le magasin n'est pas disponible près de chez vous.</b>
                                </div>

                                <md-divider></md-divider>

                            </div>

                            <div class="row layout-padding" id="my_cart">

                                <div ng-repeat="category in departmentStore.categories">

                                    <div class="row">

                                        <p class="category-name"><b> - {{category.name | uppercase}} - </b></p>

                                    </div>

                                    <div ng-repeat="item in category.products">

                                        <md-divider ng-if="$first"></md-divider>

                                        <cart-list-item iscartview='cartSettings.cartView' item='item' on-delete='removeFromCart(id)' on-update='productChanged(sp)' on-update-quantity='updateCartQuantity(quantity, id)'></cart-list-item>

                                        <md-divider ng-if="!$last"></md-divider>

                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="layout-padding" ng-hide="cartSettings.cartView">
                        
                        <div ng-show="results_available">
                            
                            <md-tabs md-dynamic-height md-border-bottom>

                                <md-tab label="{{store.name}} ({{store.store_products.length}} / {{cart.length}})" ng-repeat="store in stores.slice(0, 5)" md-on-select="storeTabSelected(store)">
                                    
                                    <div class="layout-padding">

                                        <div class="md-primary"><a class="image-container-square" href><img alt="poster_1_up" ng-src="{{store.image}}"></a><a href  ng-click="InitMap($event, store.department_store)"><p style="text-align: center; margin-top: 10px;">{{store.department_store.fullName}}, {{store.department_store.distanceText}} en voiture (environs {{store.department_store.timeText}})</p></a></div>

                                          <div>Produits disponibles <span class="badge">{{store.store_products.length}}</div>

                                          <div id="my_cart">
                                              <div ng-repeat="category in productCategories">

                                                  <div class="row">
                                                      <p class="category-name"><b> - {{category.name | uppercase}} - </b></p>
                                                  </div>

                                                  <div ng-repeat="item in category.products">


                                                      <md-divider ng-if="$first"></md-divider>

                                                          <cart-list-item iscartview='cartSettings.cartView' item='item' on-delete='removeFromCart(id)' on-update='productChanged(sp)' on-update-quantity='updateCartQuantity(quantity, id)'></cart-list-item>

                                                      <md-divider ng-if="!$last"></md-divider>

                                                  </div>


                                              </div>
                                          </div>


                                          <div class="md-warn"><a class="md-warn" href  data-toggle="collapse" data-target="#products_{{store.id}}">Voir produits indisponibles</a> <span class="badge">{{store.missing_products.length}}</div>
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
                    
                </md-card>
               
                <div class="" style="margin-bottom: 10px; margin-top: 10px;">

                    <div class="cart_totals layout-padding pull-right">

                        <h2>Détails d'optimisation</h2>

                        <table>
                            <tbody>

                                <tr class="cart-subtotal">
                                    <th>Total des produits disponibles</th>
                                    <td><span class="amount md-warn"><b>{{totalPriceAvailableProducts | number : 2}} C $</b></span></td>
                                </tr>

                                <tr class="cart-subtotal">
                                    <th>Total des produits non disponibles</th>
                                    <td><span class="amount" style="color : gray;"><b>{{totalPriceUnavailableProducts | number : 2}} C $</b></span></td>
                                </tr>

                                <tr class="cart-subtotal">
                                    <th>Total du panier</th>
                                    <td><span class="amount"><b>{{totalPriceUnavailableProducts + totalPriceAvailableProducts | number : 2}} C $</b></span></td>
                                </tr>

                                <tr class="optimized-distance" ng-show="price_optimization > 0">
                                    <th>Montant économisé</th>
                                    <td><span class="amount"><b style="color : red"><b><span ng-show="show_min_price_optimization">{{min_price_optimization | number : 2}} - </span> {{price_optimization | number : 2}} C $</b></span></td>
                                </tr>

                            </tbody>
                        </table>

                    </div>

                    <div class="row pull-right">

                        <md-button class="md-warn md-raised md-rounded-corners"  ng-click="clearCart()" aria-label="Effacer votre panier">
                            <md-tooltip
                                md-direction="bottom">
                                Effacer votre panier
                            </md-tooltip>
                            <md-icon><i class="material-icons">clear_all</i></md-icon>
                            Effacer Liste
                        </md-button>

                        <md-button class="md-raised md-primary md-rounded-corners" aria-label="Impression" ng-click="printCart()">
                            <md-tooltip
                                md-direction="bottom">
                                Impression
                            </md-tooltip>
                            <md-icon><i class="material-icons">print</i></md-icon>
                            Imprimer
                        </md-button>

                        <md-button class="md-raised md-primary md-rounded-corners" ng-click="sendListAsSMS($event)" aria-label="Envoyer à votre téléphone" ng-disabled="!isUserLogged">
                            <md-tooltip
                                md-direction="bottom">
                                Envoyer par sms
                            </md-tooltip>
                          <md-icon><i class="material-icons">smartphone</i></md-icon>
                          Envoyer par SMS
                        </md-button>

                        <md-button class="md-raised md-primary md-rounded-corners" aria-label="Partager" ng-hide="true">
                            <md-tooltip
                                md-direction="bottom">
                                Partager
                            </md-tooltip>
                            <md-icon><i class="material-icons">share</i></md-icon>
                            Partager
                        </md-button>

                        <md-button class="md-raised md-primary md-rounded-corners" aria-label="Envoyer à votre courrier électronique" ng-click="sendListAsEmail($event)"  ng-disabled="!isUserLogged">
                            <md-tooltip
                                md-direction="bottom">
                                Envoyer à votre courrier électronique
                            </md-tooltip>
                            <md-icon><i class="material-icons">email</i></md-icon>
                            Envoyer par Email
                        </md-button>

                    </div>

                </div>
                    
            </div>
        </div>
        
    </md-content>

    <div>
        <p class="cart-warning"><b>En cas de différence entre la circulaire du magasin et OTIPRIX, la circulaire a préséance</b></p>
    </div>
</md-content>





    
        
    




