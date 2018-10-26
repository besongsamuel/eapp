<!DOCTYPE html>
<html lang="en" ng-app="eappApp">
  <head>
    <base href="/" />
    <link rel="icon" type="image/x-icon" href="<?php echo base_url("assets/img/favicon/")?>favicon.ico" sizes="32x32" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{title}</title>
    
    {css}
    
    <script src="<?php echo base_url("node_modules/jquery/dist/jquery.min.js")?>"></script>
    	  
  </head>
  
    <body class="md-body-1">
     
        <div class="otiprix-header" ng-controller="MenuController">

            <div class="top-header" ng-cloak>

              <div class="container">

                  <div class="row">

                      <div class="col-sm-6">
                          <div class="pull-left header-social">
                              <a href="https://www.facebook.com/otiprix.otiprix.1" target="_blank"><i otiprix-text class="fa fa-facebook"></i></a>
                              <a href="https://twitter.com/otiprix" target="_blank"><i otiprix-text class="fa fa-twitter"></i></a>
                              <a href="https://www.youtube.com/channel/UCbwxS8s1WKYgGCRzd9vIl5A" target="_blank"><i otiprix-text class="fa fa-youtube"></i></a>
                              <a ng-hide="true" href="https://www.instagram.com/otiprix/" target="_blank"><i otiprix-text class="fa fa-instagram"></i></a>
                              <a ng-hide="true" href="https://plus.google.com/u/0/117638375580963001925" target="_blank"><i class="md-primary" class="fa fa-google-plus"></i></a>
                          </div>
                      </div>

                      <div class="col-sm-6">

                          <div>
                              <div class="pull-right">
                                  <span>
                                      <a href="<?php echo site_url("cart"); ?>" class="md-icon-button" aria-label="Cart">
                                          Voir votre liste
                                          <md-icon><i otiprix-text class="material-icons">shopping_cart</i></md-icon>
                                          <span class="badge" ng-show="getTotalItemsInCart() > 0">{{getTotalItemsInCart()}} | {{getCartPrice() | number : 2}} C $</span>
                                      </a>
                                  </span>
                              </div>
                          </div>

                      </div>

                  </div>

              </div>

          </div>

            <div id="mainmenu-area" class="mainmenu-area" class="navbar-wrapper" ng-cloak>
                      <div>
                          <nav otiprix-background class="navbar navbar-fixed-top" style="top : 50px;">
                              <div class="navbar-padding">
                                  <div class="navbar-header">
                                      <button type="button" class="navbar-toggle collapsed pull-right" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                                      <span class="sr-only">Toggle navigation</span>
                                      <span class="icon-bar"></span>
                                      <span class="icon-bar"></span>
                                      <span class="icon-bar"></span>
                                      </button>
                                      <a class="navbar-brand" href="<?php echo site_url("home"); ?>"><img src="<?php echo base_url("assets/img/logo.png"); ?>" class="eapp-logo" /></a>
                                  </div>
                                  <div id="navbar" class="navbar-collapse collapse pull-right">

                                      <ul class="menu nav navbar-nav"  ng-controller="MenuController">
                                          <li ng-class="{active : selectedMenu == 100}" class=" dropdown" ng-show="loggedUser.subscription == 2">
                                              <a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin<span class="caret"></span></a>
                                              <ul class="dropdown-menu" otiprix-background>
                                                  <li ng-show="loggedUser.subscription == 2"><a  href="<?php echo addslashes(site_url("admin/uploads")); ?>">Uploads</a></li>
                                                  <li><a href="<?php echo addslashes(site_url("admin/create_store_product")); ?>">Create Product</a></li>
                                                  <li><a href="<?php echo addslashes(site_url("admin/store_products")); ?>">View Store Products</a></li>
                                                  <li><a href="<?php echo addslashes(site_url("admin/view_products")); ?>">View Otiprix Products</a></li>
                                                  <li><a href="<?php echo addslashes(site_url("admin/view_subcategories")); ?>">View Sub Categories</a></li>
                                              </ul>
                                          </li>

                                          <li ng-class="{active : selectedMenu == 0}" class="main-menu-list-item"><a href="<?php echo site_url("home"); ?>" class=""><md-icon style="color : white"><i class="material-icons">home</i> </md-icon></a></li>
                                          <li class="main-menu-list-item" class=" dropdown" ng-class="{active : selectedMenu == 1}">
                                              <a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Réduisez vos dépenses<span class="caret"></span></a>
                                              <ul class="dropdown-menu" otiprix-background>
                                                  <li><a href="<?php echo site_url("account/my_grocery_list"); ?>">Votre liste d'épicerie</a></li>
                                                  <li><a href="<?php echo site_url("shop/select_flyer_store"); ?>">Les circulaires des magasins</a></li>
                                                  <li><a href="<?php echo site_url("shop/categories"); ?>">Les catégories de produits</a></li>
                                              </ul>
                                          </li>
                                          <li ng-class="{active : selectedMenu == 2}" class="main-menu-list-item"><a href ng-click="gotoShop()">Trouvez un produit</a></li>
                                              <ul class="dropdown-menu" otiprix-background>
                                                  <li><a href="<?php echo site_url("blog/press_release"); ?>">Épicerie dans la presse</a></li>
                                                  <li><a href="<?php echo site_url("blog/stats"); ?>">STAT</a></li>
                                                  <li><a href="<?php echo site_url("blog/videos"); ?>">Vidéo</a></li>
                                                  <!--<li><a href="<?php echo site_url("home/store_policy"); ?>">Politiques des magasins</a></li>-->
                                                  <li><a href="<?php echo site_url("home/about_us"); ?>">À propos</a></li>
                                              </ul>
                                          </li>
                                          <li ng-class="{active : selectedMenu == 3}" class="main-menu-list-item"><a  href="<?php echo site_url("home/contact"); ?>">Contact</a></li>
                                          <li ng-class="{active : selectedMenu == 4}" class="main-menu-list-item"><a  href="<?php echo site_url("home/about"); ?>">À propos</a></li>
                                      </ul>

                                      <ul class="menu nav navbar-nav pull-right"  ng-controller="AccountController">
                                          <li ng-class="{active : selectedMenu == 5}" class="main-menu-list-item" ng-hide="isUserLogged"><a href="<?php echo site_url("account/login"); ?>"><i class="fa fa-user"></i>    S'identifier</a></li>
                                          <li ng-class="{active : selectedMenu == 6}" class="main-menu-list-item" ng-hide="isUserLogged"><a href="<?php echo site_url("account/register"); ?>"><i class="fa fa-user"></i>    Créer un compte</a></li>
                                          <li ng-class="{active : selectedMenu == 5}" class="main-menu-list-item" ng-show="isUserLogged" class=" dropdown">
                                              <a href="#" class="dropdown-toggle active" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                                  Bonjour 
                                                  <span ng-if="!loggedUser.company"><span ng-show="loggedUser.profile.firstname">{{loggedUser.profile.firstname}},</span> {{loggedUser.profile.lastname}}</span>
                                                  <span ng-if="loggedUser.company">{{loggedUser.company.name}}</span>
                                                  <span class="caret"></span>
                                              </a>
                                              <ul class="dropdown-menu" otiprix-background>
                                                  <li><a  href="<?php echo site_url("account"); ?>"><i class="fa fa-user"></i> Mon compte</a></li>
                                                  <li ng-if="isRegularUser"><a href="<?php echo site_url("account/my_grocery_list"); ?>"><i class="fa fa-heart"></i> Ma liste d'épicerie</a></li>
                                                  <li><a href ng-click="logout()">Logout</a></li>
                                              </ul>
                                          </li>
                                      </ul>

                                  </div>
                              </div>
                          </nav>
                  </div>
              </div>

        </div>   
  
        <!-- End mainmenu area -->
        <div id="main-body">	
            {body}
        </div>
    
        <div id="eapp-footer" class="footer-top-area" ng-controller="FooterController" ng-cloak>
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="footer-about-us">
                            <a href="<?php echo site_url("home"); ?>"><img src="<?php echo base_url("assets/img/logo.png"); ?>" class="eapp-logo-footer" /></a>
                            <p style="margin-top : 10px;">  
                                En un seul clic, otiPrix réduit le coût de votre panier d’épicerie en identifiant les meilleurs et les vrais rabais dans les magasins proches de vous. Avec OTIPRIX, consulter en un seul et même endroit l’ensemble des produits alimentaires en rabais dans les grandes surfaces, mais aussi dans tous les petits magasins situés à proximité de votre lieu de résidence.
                            </p>
                            <div class="footer-social">
                                <a href="https://www.facebook.com/otiprix.otiprix.1" target="_blank"><i class="fa fa-facebook"></i></a>
                                <a href="https://twitter.com/otiprix" target="_blank"><i class="fa fa-twitter"></i></a>
                                <a href="https://www.youtube.com/channel/UCbwxS8s1WKYgGCRzd9vIl5A" target="_blank"><i class="fa fa-youtube"></i></a>
                                <a href="https://www.instagram.com/otiprix/" target="_blank"><i class="fa fa-instagram"></i></a>
                                <a href="https://plus.google.com/u/0/117638375580963001925" target="_blank"><i class="fa fa-google-plus"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="footer-menu">
                            <h2 class="md-title">Navigation de l'utilisateur</h2>
                            <ul>
                                <li><a href="<?php echo site_url("account"); ?>">Mon compte</a></li>
                                <li ng-if="isRegularUser"><a href="<?php echo site_url("account/my_grocery_list"); ?>">Ma liste d'epicerie</a></li>
                                <li><a href="<?php echo site_url("blog/press_release"); ?>">Presse</a></li>
                                <li><a href="#">Contacter nous</a></li>
                                <li><a href  onclick="window.open('<?php echo base_url("/assets/files/terms_and_conditions.pdf")?>', '_blank', 'fullscreen=yes'); return false;">Terme et conditions</a></li>
                            </ul>                        
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="footer-menu">
                            <h2 class="md-title">Categories</h2>
                            <ul>
                                <li ng-click="select_category($event, category)" id="{{category.id}}"  ng-repeat="category in categories"><a href>{{category.name}}</a></li>
                            </ul>                        
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="footer-newsletter">
                            <h2 class="md-title white-color">Bulletin</h2>
                            <p>Inscrivez-vous à notre Infolettre et soyez les premiers informés sur :</p>
                            <p> - l’évolution des prix des denrées alimentaires;</p>
                            <p> - toutes les opportunités  pour réduire le coût de votre épicerie.</p>
                            <div class="newsletter-form" ng-submit="subscribe($event)" >
                                <form novalidate>
                                    <input type="email" placeholder="Entrez votre email" ng-model="subscribe_email">
                                    <div class="col-sm-12">
                                        <md-button type="submit" class="md-raised md-primary pull-right" >S’INSCRIRE</md-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="copyright">
                            <p>&copy; 2017 OtiPrix. Tous droits réservés.</p>
                        </div>
                    </div>

                    <div class="col-md-4" ng-show="false">
                        <div class="footer-card-icon">
                            <i class="fa fa-cc-discover"></i>
                            <i class="fa fa-cc-mastercard"></i>
                            <i class="fa fa-cc-paypal"></i>
                            <i class="fa fa-cc-visa"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- End footer top area -->
    
    </body>
  
  {scripts}
  {script}
  
   <!-- Initialize angular root scope -->
    <script>
        $(document).ready(function()
        {
            var rootScope = angular.element($("html")).scope();
            rootScope.$apply(function()
            {
                rootScope.base_url = "<?php echo $base_url; ?>";
                rootScope.site_url = "<?php echo $site_url; ?>";
                rootScope.controller = "<?php echo $controller; ?>";
                rootScope.method = "<?php echo $method; ?>";
                rootScope.longitude = 0;
                rootScope.latitude = 0;
                rootScope.redirectToLogin = JSON.parse("<?php echo $redirectToLogin; ?>");
                var user = '<?php echo $user; ?>';
                if(user === "" || user == "null")
                {
                        rootScope.loggedUser = null;
                }
                else
                {
                        rootScope.loggedUser = JSON.parse(user);
                }
                rootScope.hideSearchArea = 
                        (rootScope.controller == "account" && (rootScope.method == "login" || rootScope.method == "register")) 
                        || (rootScope.method == "contact" || (rootScope.method == "about" && rootScope.controller == "home"));
						
			
                rootScope.isUserLogged = rootScope.loggedUser !== null;
                
                rootScope.isRegularUser = rootScope.isUserLogged && parseInt(rootScope.loggedUser.subscription) <= 2;
            });
            
            var footerScope = angular.element($("#eapp-footer")).scope();
            
            footerScope.$apply(function()
            {
                footerScope.categories = JSON.parse('<?php echo $mostviewed_categories; ?>');
            });
        });
    </script>
	  
    <!-- Rootscope Script -->
    <script src="<?php echo base_url("assets/js/angular-modules/root-scope.js")?>"></script> 
    <script src="<?php echo base_url("assets/js/angular-services/application-service.js")?>"></script> 
    <script src="<?php echo base_url("assets/js/angular-services/cart-service.js")?>"></script> 

  
</html>