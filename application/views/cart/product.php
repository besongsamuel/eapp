

<script>

$(document).ready(function()
{
    var scope = angular.element($("#admin-container")).scope();
    
    scope.$apply(function()
    {
        scope.storeProduct = JSON.parse('<?php echo $store_product; ?>');
        scope.products = JSON.parse('<?php echo $products; ?>');
    });
});

</script>   
    
<div id="admin-container" class="single-product-area" ng-controller="CartController">
        <div class="zigzag-bottom"></div>
        <div class="container">
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
                        
                        <div class="related-products-wrapper" ng-show="relatedProductsAvailable()">
                            <h2 class="related-products-title">Related Products</h2>
                            <div class="related-products-carousel">
                                <?php foreach($relatedProducts as $product): ?>
                                <div class="single-product">
                                    <div class="product-f-image">   
                                        <img ng-src="http://<?php echo base_url("assets/img/products/").$product->image;?>" style="height: 100%;" alt="">
                                        <div class="product-hover">
                                            <a href ng-show="canAddToCart(<?php echo $product->id; ?>)" class="add-to-cart-link" ng-click="addProductToCart(<?php echo $product->id; ?>)"><i class="fa fa-shopping-cart"></i> Add to cart</a>
                                                <a href ng-hide="canAddToCart(<?php echo $product->id; ?>)" class="add-to-cart-link" ng-click="removeProductFromCart(<?php echo $product->id; ?>)"><i class="fa fa-shopping-cart"></i> Remove</a>
                                            <a href ng-click="viewProductDetails(<?php echo $product->id; ?>)" class="view-details-link"><i class="fa fa-link"></i> See details</a>
                                        </div>
                                    </div>

                                    <h2><a href ng-click="viewStoreDetails(<?php echo $product->id; ?>)"><?php echo $product->retailer_name; ?></a></h2>
                                    <div class="product-carousel-price">
                                        <ins>CAD <?php echo $product->price; ?></ins><del>CAD <?php echo $product->regular_price; ?></del>
                                    </div>
                                </div>
                            <?php endforeach; ?>                                  
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
   