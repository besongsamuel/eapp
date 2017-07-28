<div class="container admin-container" ng-controller="AccountController">
    <md-content>
    
        <md-toolbar class="md-hue-2">
          <div class="md-toolbar-tools">
            <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
                <md-icon md-svg-icon="{{icons.person | trustUrl}}"></md-icon>
            </md-button>
            <h2 flex md-truncate>Ma liste d’épicerie</h2>
          </div>
        </md-toolbar>
        
        <div class="row">
            <div class="col-sm-4"><p><a href>Produits <span class="badge">5</span></a><br></p></div>
            <div class="col-sm-4"><p><a href>Offres des circulaires <span class="badge">5</span></a><br></p></div>
            <div class="col-sm-4"><p><a href>Coupons <span class="badge">5</span></a><br></p></div>
        </div>
        
        <div class="row search-products">
        
            <md-autocomplete class="col-sm-10"
                md-selected-item="selectedProduct"
                md-search-text="searchProductText"
                md-selected-item-change="product_selected(item)"
                md-items="item in querySearch(searchProductText)"
                md-item-text="item.name"
                md-min-length="2"
                md-floating-label="Recherche de produits"
                placeholder="Type in the name of the product">
                <md-item-template>
                    <span md-highlight-text="searchProductText" md-highlight-flags="^i">{{item.name}}</span>
                </md-item-template>
                <md-not-found>
                        Aucun produit correspondant à "{{searchProductText}}" n'a été trouvé.
                </md-not-found>
            </md-autocomplete>
            <md-button ng-click="AddItemToList()" class="md-raised md-primary col-sm-2">Ajouter</md-button>
        </div>
        
        <md-divider md-inset></md-divider>
        
        <div class="row my-grocery-list">
            
            <div ng-repeat="category in myCategories">
                <md-subheader class="md-no-sticky">category.name</md-subheader>
                      <md-list-item ng-repeat="product in category.items" ng-click="viewProductDetails(product.id, $event)" class="noright">
                        <img alt="{{ product.name }}" ng-src="http://{{base_url.concat("/assets/products/")}}{{ product.image }}" class="md-avatar" />
                        <p>{{ product.name }}</p>
                        <md-input-container class="md-secondary">
                            <input style="width: 40px;" aria-label="Qty" type="number" ng-model="product.quantity   ">
                        </md-input-container>
                        <md-icon md-svg-icon="{{icons.delete | trustUrl}}"  ng-click="removeProduct(product.id, $event)" aria-label="Supprimer le produit" class="md-secondary md-hue-3" ></md-icon>
                      </md-list-item>
                <md-divider></md-divider>
            <div>
        </div>
        
        <div class="row actions-section">
            <div class="col-sm-6">
                <md-button ng-click="OptimizeMyList()" class="md-raised md-primary>Optimiser ma liste</md-button>
            </div>
            <div class="col-sm-6">
                <md-button ng-click="clearMyList()" class="md-raised md-warn>Effacer ma liste</md-button>
            </div>
        </div>
        
    </md-content>
</div>

<script>
    $(document).ready(function()
    {
        var scope = angular.element($("#admin-container")).scope();
        
        scope.$apply(function()
        {
            
        });
    });
</script>
