<div id="side-bar-widget" class="pull-right col-md-4 col-sm-4 col-xs-12" ng-controller="BlogController">
	<div id="sidebar" class="clearfix">
            <div class="">
                <md-input-container class="">
                    <label>Rechercher articles</label>
                    <input name="searchText" ng-model="searchPostsText" aria-label="Search" ng-change="searchPosts(searchPostsText)" />
                    <md-icon><i class="material-icons">search</i></icon>
                </md-input-container>
            </div><!-- end widget -->
            <div class="widget wow fadeIn">
                <div class="widget-title">
                    <h3>Posts récents</h3>
                </div><!-- end widget title -->
                <div class="featured-widget" ng-show="otherPosts.length > 0">
                    <ul ng-repeat="post in otherPosts">
                        <li>
                            <img ng-src="<?php echo base_url("assets/blog/img/article.png")?>" alt="{{post.title}}" class="alignleft" style="height : 93px; width: 93px;">
                            <h3> <a href="#">{{post.title}}</a></h3>
                            <span class="metabox">
                                <span ng-hide="post.type == 2"><a href="<?php echo site_url("blog/read/")?>{{post.id}}" >INFOS</a> | {{post.date_modified | date}}</span>
                                <span ng-show="post.type == 2"><a href="<?php echo site_url("blog/view/")?>{{post.id}}" >INFOS</a> | {{post.date_modified | date}}</span>
                            </span>
                        </li>
                    </ul>
                </div><!-- end featured-widget -->
                <div ng-show="recentPosts.length > 0">
                        <p>Aucun article disponible.</p>
                </div>
            </div><!-- end widget -->
	</div><!-- end col -->
</div><!-- end sidebar -->
