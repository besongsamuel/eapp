<script>
    $(document).ready(function()
    {
        var scope = angular.element($("#admin-container")).scope();
    
        scope.$apply(function()
        {
           scope.load_icons(); 
           
           scope.getUserProductList();
        });
    });
</script>

<div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Ma liste d’épicerie</h2>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Page title area -->

<div id="admin-container" class="container" ng-controller="AccountController">
    
    <md-content class="container">
        
        <div id="row my-list-info" class="row my-list-info container" style="margin : 10px; margin: auto; padding: 20px;">
            <div class="col-sm-4"><p style="text-align : center;"><a href>Produits <span class="badge">{{my_list_count()}}</span></a><br></p></div>
            <div class="col-sm-4"><p style="text-align : center;"><a href>Offres des circulaires <span class="badge">{{flyers_count()}}</span></a><br></p></div>
            <div class="col-sm-4"><p style="text-align : center;"><a href>Coupons <span class="badge">{{coupons_count()}}</span></a><br></p></div>
        </div>
        
        <div class="row search-products">
        
            
                <md-icon md-svg-src="{{icons.search | trustUrl}}"></md-icon>
                <md-autocomplete class="col-sm-11" style="display : inline-block;"
                md-search-text="searchProductText"
                md-selected-item-change="product_selected(item)"
                md-items="item in querySearch(searchProductText)"
                md-item-text="item.name"
                md-min-length="2"
                md-floating-label="Recherche de produits"
                >
                <md-item-template>
                    <span md-highlight-text="searchProductText" md-highlight-flags="^i">{{item.name}}</span>
                </md-item-template>
                <md-not-found>
                        Aucun produit correspondant à "{{searchProductText}}" n'a été trouvé.
                </md-not-found>
                </md-autocomplete>
            
            
            <md-button ng-click="AddItemToList()" class="md-fab md-mini md-primary" style="background-color : #1abc9c;" aria-label="" ng-click="removeProduct(product.id, $event)">
                <md-icon md-svg-src="{{icons.add | trustUrl}}"></md-icon>
            </md-button>
        </div>
        
        <p>Ma liste d’épicerie</p>
        <md-divider></md-divider>
        
        <div class="row my-grocery-list md-whiteframe-3dp" style="margin : 10px; min-height: 100px;" >
            <div ng-repeat="category in myCategories">
                <md-subheader class="md-no-sticky">{{category.name}}</md-subheader>
                    <md-list-item ng-repeat="product in category.products" ng-click="viewProductDetails(product.id, $event)" class="noright">
                        <img alt="{{ product.name }}" ng-src="http://{{base_url}}/assets/img/products/{{ product.image }}" class="md-avatar" />
                        <p>{{ product.name }}</p>
                        <md-input-container class="md-secondary">
                            <input style="width: 40px;" aria-label="Qty" type="number" ng-model="product.quantity">
                        </md-input-container>
                        <md-button class="md-fab md-mini md-secondary" aria-label="" ng-click="removeProduct(product.id, $event)">
                            <md-icon md-svg-src="{{icons.delete | trustUrl}}"></md-icon>
                        </md-button>
                    </md-list-item>
                <md-divider></md-divider>
            </div>
            <h4 layout layout-align="center" ng-show="myCategories.length === 0" layout-padding>Votre liste est vide. </h4>
        </div>
        
        <div class="row actions-section">
            <div class="col-sm-6">
                <md-button ng-click="OptimizeMyList()" class="md-raised md-primary" style="background-color : #1abc9c;">Optimiser ma liste</md-button>
            </div>
            <div class="col-sm-6">
                <md-button ng-click="saveMyList()" class="md-raised md-primary pull-right">Sauver ma liste</md-button>
                <md-button ng-click="clearMyList()" class="md-raised md-warn pull-right">Effacer ma liste</md-button>
            </div>
        </div>
        
    </md-content>
</div>


