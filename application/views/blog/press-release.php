<!DOCTYPE html>

<div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Épicerie dans la presse</h2>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Page title area -->

    <!-- page builer -->
    <section id="blog-container" class="section-white clearfix blog" ng-controller="BlogController">
        <div class="container">
            <div class="row">
                <div class="pull-left col-md-8 col-sm-8 col-xs-12">
                    <div id="blog-page" class="row clearfix">
                        <div class="col-md-12 col-sm-12 col-xs-12 wow fadeIn" ng-class="$index % 2 === 0 ? 'first' : 'last'" ng-repeat="post in recentPosts">
                            <div class="blog-item">
                                <div class="ImageWrapper" ng-hide="post.type == 2">
                                    <img ng-src="<?php echo base_url("assets/blog/"); ?>img/posts/{{post.image}}" alt="" class="img-responsive">
                                    <div class="ImageOverlayLi"></div>
                                    <div class="Buttons StyleH">
                                        <a ng-show="canLike(post.id) && userLogged" href ng-click="like(post.id)" title="Aimer"><span class="bubble border-radius"><i class="fa fa-heart-o"></i> {{post.likes}}</span></a>
                                        <a ng-show="!canLike(post.id) && userLogged" href ng-click="like(post.id)" title="Pas aimer"><span class="bubble border-radius"><i class="fa fa-thumbs-down"></i></span></a>
                                        <a href="<?php echo base_url("blog/comments/")?>{{post.id}}" title="Voir Commentaires"><span class="bubble border-radius"><i class="fa fa-comment-o"></i> {{post.comments.length}}</span></a>
                                    </div>
                                </div>
                                <div class="ImageWrapper" ng-show="post.type == 2">
                                    <iframe width="560" height="315" ng-src="{{getiFrameSrc(post.image)}}" frameborder="0" allowfullscreen></iframe>
                                </div>
                                <div class="meta">
                                    <span ng-hide="post.type == 2"><a href="<?php echo site_url("blog/read/")?>{{post.id}}" >INFOS</a> | {{post.date_modified | date}}</span>
                                    <span ng-show="post.type == 2"><a href="<?php echo site_url("blog/view/")?>{{post.id}}" >INFOS</a> | {{post.date_modified | date}}</span>
                                </div><!-- end meta -->
                                <div class="blog-title">
                                    <h3><a href="<?php echo base_url("blog/view/")?>{{post.id}}" title="">{{post.title}}</a></h3>
                                </div><!-- end title -->
                                <div class="blog-desc">
                                    <p>{{post.description}}</p>
                                </div><!-- end desc -->
                                <div  ng-hide="post.type == 2" class="blog-button">
                                    <a href="<?php echo site_url("blog/read/")?>{{post.id}}" title="" class="btn btn-primary border-radius">Lire</a>
                                </div><!-- end button -->
                                <div  ng-show="post.type == 2" class="blog-button">
                                    <a href="<?php echo site_url("blog/view/")?>{{post.id}}" title="" class="btn btn-primary border-radius">Voir details</a>
                                </div><!-- end button -->
                            </div><!-- end blog -->
                        </div><!-- end col -->
                    </div><!-- end blog -->
                    
                    <nav class="text-center" ng-show="page_list.length > 1"> 
                        <ul class="pagination">
                            <li><a href ng-click="previousPage()" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
                            
                            <li ng-click="gotoPage(item)" ng-repeat="item in page_list"><a href ng-style="selected_page_value == item.value && {'background-color':'#17a78b'}">{{item.value}}</a></li>
                            
                            <li>
                                <a href ng-click="nextPage()"aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    
                    <div class="text-center" ng-show="recentPosts.length === 0">
                        <p>Aucun article de presse n'est actuellement disponible.</p>
                    </div>
                </div><!-- end pull-right -->
                {recent_posts}

        </div><!-- end row -->
    </div><!-- end container -->
</section><!-- end section white -->

<script>
    
    $(document).ready(function()
    {
        var scope = angular.element($("#blog-container")).scope();
	scope.$apply(function()
 	{
            scope.baseurl = scope.base_url;
            
            scope.postType = parseInt('<?php echo $post_type; ?>');
            
            scope.gotoPage({ value : 1});
	});
        
    });
	
</script>

