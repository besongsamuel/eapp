

<script>

$(document).ready(function()
{
    var scope = angular.element($("#admin-container")).scope();
    
    scope.$apply(function()
    {
        scope.storeProduct = JSON.parse('<?php echo $store_product; ?>');
        scope.products = JSON.parse('<?php echo $products; ?>');
    });
    
    var rootScope = angular.element($("html")).scope();
    rootScope.$apply(function()
    {
        rootScope.menu = "cart";
    });
});

</script>   

<div class="md-menu-demo" ng-controller="MenuController" ng-cloak>

  <div class="menu-demo-container" layout-align="center center" layout="column">
    <h2 class="md-title">Simple dropdown menu</h2>
    <p>Applying the <code>md-menu-origin</code> and <code>md-menu-align-target</code> attributes ensure that the menu elements align.
    Note: If you select the Redial menu option, then a modal dialog will zoom out of the phone icon button.</p>
    <md-menu>
      <md-button aria-label="Open phone interactions menu" class="md-icon-button" ng-click="openMenu($mdMenu, $event)">
        <md-icon md-menu-origin md-svg-icon="call:phone"></md-icon>
      </md-button>
      <md-menu-content width="4">
        <md-menu-item>
          <md-button ng-click="redial($event)">
            <md-icon md-svg-icon="call:dialpad" md-menu-align-target></md-icon>
            Redial
          </md-button>
        </md-menu-item>
        <md-menu-item>
          <md-button disabled="disabled" ng-click="ctrl.checkVoicemail()">
            <md-icon md-svg-icon="call:voicemail"></md-icon>
            Check voicemail
          </md-button>
        </md-menu-item>
        <md-menu-divider></md-menu-divider>
        <md-menu-item>
          <md-button ng-click="ctrl.toggleNotifications()">
            <md-icon md-svg-icon="social:notifications-{{ctrl.notificationsEnabled ? 'off' : 'on'}}"></md-icon>
            {{ctrl.notificationsEnabled ? 'Disable' : 'Enable' }} notifications
          </md-button>
        </md-menu-item>
      </md-menu-content>
    </md-menu>
  </div>
</div>

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
                        <li class="active"><a href="http://<?php echo site_url("cart"); ?>">Cart</a></li>
                        <li><a href="#">Catégories</a></li>
                        <li><a href="#">Dépliants</a></li>
                        <li><a href="#">Contactez nous</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div> 
    <!-- End mainmenu area -->
    
<div id="admin-container" class="single-product-area" ng-controller="CartController">
    <md-content>
            <div class="row">
                <div class="col-md-12">
                    <div class="product-content-right">
                        <div class="product-breadcroumb">
                            <a href="">Home</a>
                            <a href="">{{storeProduct.category.name}}</a>
                            <a href="">{{storeProduct.subcategory.name}}</a>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="product-images">
                                    <div class="product-main-img">
                                        <img  ng-src="http://<?php echo base_url("assets/img/products/"); ?>{{storeProduct.product.image}}" alt="">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="product-inner">
                                    <h2 class="product-name">{{storeProduct.product.name}}</h2>
                                    <div class="product-inner-price">
                                        <ins>CAD {{storeProduct.price}}</ins> <del>CAD {{storeProduct.regular_price}}</del>
                                    </div>    
                                    
                                    <form action="" class="cart">
                                        <div class="quantity">
                                            <input type="number" size="4" class="input-text qty text" title="Qty" value="1" name="quantity" min="1" step="1">
                                        </div>
                                        <button class="add_to_cart_button" type="submit">Add to cart</button>
                                    </form>   
                                    
                                    <div class="product-inner-category">
                                        <p>Category: <a href="">{{storeProduct.category.name}}</a>.
                                    </div> 
                                    
                                    <div role="tabpanel">
                                        <ul class="product-tab" role="tablist">
                                            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Description</a></li>
                                            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Reviews</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div role="tabpanel" class="tab-pane fade in active" id="home">
                                                <h2>Product Description</h2>  
                                                <h4>Available at {{storeProduct.retailer.name}}</h4>
                                                <p>Brand: {{storeProduct.brand}}</p>
                                                <p>Format: {{storeProduct.format}}</p>
                                                <p>Origin: {{storeProduct.country}}</p>
                                                <p>Available from {{storeProduct.period_from}} to {{storeProduct.period_to}}</p>
                                            </div>
                                            <div role="tabpanel" class="tab-pane fade" id="profile">
                                                <h2>Reviews</h2>
                                                <div class="submit-review">
                                                    <p><label for="name">Name</label> <input name="name" type="text"></p>
                                                    <p><label for="email">Email</label> <input name="email" type="email"></p>
                                                    <div class="rating-chooser">
                                                        <p>Your rating</p>

                                                        <div class="rating-wrap-post">
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                            <i class="fa fa-star"></i>
                                                        </div>
                                                    </div>
                                                    <p><label for="review">Your review</label> <textarea name="review" id="" cols="30" rows="10"></textarea></p>
                                                    <p><input type="submit" value="Submit"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </md-content>
</div>
   