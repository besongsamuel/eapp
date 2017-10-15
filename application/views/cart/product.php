<script src="<?php echo base_url("assets/js/product-controller.js")?>"></script>

<script>

$(document).ready(function()
{
    var scope = angular.element($("#admin-container")).scope();
    
    scope.$apply(function()
    {
        scope.storeProduct = JSON.parse('<?php echo $store_product; ?>');
    });
    
    var rootScope = angular.element($("html")).scope();
    rootScope.$apply(function()
    {
        rootScope.menu = "cart";
    });
});

</script>   
    
<div id="admin-container" class="" ng-controller="ProductController" ng-cloak>
    
    <div class="container" style="margin-top : 10px;">
        <ul class="breadcrumb" style="text-align: center; background: white;">
            <li><a href="<?php echo site_url("home/goback"); ?>">Retour</a></li>
            <li class="active">{{storeProduct.product.name}}</li>
        </ul>
    </div>
    
    <div class="container">
        
        <!-- Product Area -->
        <div class="row">
            <!-- Product Image Area -->
            <div class="col-sm-12 col-md-4">
                <img  ng-src="{{storeProduct.product.image}}" alt="{{storeProduct.product.name}}" style="margin : auto; display : block;">
            </div>
            
            <!-- Product Description Area -->
            <div class="col-sm-12 col-md-8">
                <h4><a href>{{storeProduct.product.name}}</a></h4>
                <md-divider></md-divider>
                <div style="font-style: italic;">
                    
                    <img  ng-src="{{storeProduct.retailer.image}}" alt="{{storeProduct.retailer.name}}" width="100px" style="margin-bottom: 10px; margin-top: 20px;">
                    <ul class="breadcrumb" style="background-color: white; text-align: left;">
                        <li><a href ng-click="select_category($event)" id="{{storeProduct.product.category.id}}">{{storeProduct.product.category.name}}</a></li>
                        <li class="active">{{storeProduct.product.subcategory.name}}</li>
                    </ul>
                    <p>Prix : <b> $ CAD {{storeProduct.price}} <span ng-show="storeProduct.unit"> / {{storeProduct.unit.name}}</span></b></p>
                    <p ng-show="storeProduct.format">Format : {{storeProduct.format}}</p>
                    <p ng-show="storeProduct.size">{{storeProduct.size}}</p>
                    <p ng-show="storeProduct.brand">Marque : {{storeProduct.brand.name}}</p>
                    <p ng-show="storeProduct.state">Origine : {{storeProduct.state}}</p>
                    <p>Valide du <b>{{storeProduct.period_from}}</b> au <b>{{storeProduct.period_to}}</b></p>
                    
                    <md-button class="md-fab md-warn pull-right" aria-label="Retirer" ng-hide="can_add_to_cart(storeProduct.product.id)" ng-click="remove_product_from_cart(storeProduct.product.id)">
                        <md-icon ><i class="material-icons">remove_shopping_cart</i></md-icon>
                    </md-button>
                    
                    <md-button class="md-fab md-otiprix pull-right" aria-label="Ajouter" ng-show="can_add_to_cart(storeProduct.product.id)"  ng-click="add_product_to_cart(storeProduct.product.id, storeProduct.id)">
                        <md-icon><i class="material-icons">add_shopping_cart</i></md-icon>
                    </md-button>
                </div>
                
            </div>
        </div>
        
        <!-- Related Products Image Area -->
        <div class="col-sm-12 col-md-12 container">
            <md-list flex>
                <md-subheader class="md-no-sticky">Produits similaires</md-subheader>
                <md-list-item class="md-3-line" ng-repeat="sp in storeProduct.similar_products" ng-click="selectProduct(sp)">
                  <img ng-src="{{sp.retailer.image}}" class="md-avatar" alt="{{sp.retailer.name}}" />
                  <div class="md-list-item-text" layout="column">
                    <p>Prix : <b> $ CAD {{sp.price}} <span ng-show="sp.unit"> / {{sp.unit.name}}</span></b></p>
                    <p>Marchand : <b><a href>{{sp.retailer.name}}</a></b></p>
                    <p ng-show="sp.format">Format : {{storeProduct.format}}</p>
                    <p ng-show="sp.size">{{sp.size}}</p>
                    <p ng-show="sp.brand">Marque : {{sp.brand.name}}</p>
                    <p ng-show="sp.state">Origine : {{sp.state}}</p>
                    <p>Valide du <b>{{sp.period_from}}</b> au <b>{{sp.period_to}}</b></p>
                    
                    <md-button class="md-fab md-warn md-secondary" aria-label="Retirer" ng-hide="can_add_to_cart(sp.product.id)" ng-click="remove_product_from_cart(sp.product.id)">
                        <md-icon ><i class="material-icons">remove_shopping_cart</i></md-icon>
                    </md-button>
                    
                    <md-button class="md-fab md-otiprix md-secondary" aria-label="Ajouter" ng-show="can_add_to_cart(sp.product.id)"  ng-click="add_product_to_cart(sp.product.id, sp.id)">
                        <md-icon><i class="material-icons">add_shopping_cart</i></md-icon>
                    </md-button>
                  </div>
                  <md-divider ng-if="!$last"></md-divider>
                </md-list-item>
            </md-list>
        </div>
    </div>

</div>
   
