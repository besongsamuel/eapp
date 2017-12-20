<!DOCTYPE html>
<html lang="en" ng-app="eappApp">
  <head>
    <base href="/" />
    <link rel="icon" type="image/png" href="<?php echo base_url("assets/img/")?>favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="<?php echo base_url("assets/img/")?>favicon-16x16.png" sizes="16x16" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{title}</title>
    
     <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBdUBJq3Y93iEd29Q6GAK5SHQJniqZiHu0"></script> 
     <!-- Angular Material style sheet -->
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.4/angular-material.min.css">
    <link rel="stylesheet" href="<?php echo base_url("assets/css/lf-ng-md-file-input.css")?>">
      
    <!-- Google Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,700,600' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,700,300' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,100' rel='stylesheet' type='text/css'>
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap-slider.css")?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/owl.carousel.css")?>">
    <link rel="stylesheet" href="<?php echo base_url("assets/css/style.css")?>">
    <link rel="stylesheet" href="<?php echo base_url("assets/css/responsive.css")?>">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/admin.css")?>">
    <!-- International Phone numbers CSS CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/intlTelInput.css")?>">
    
    <!-- Animate CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/animate.css")?>">
    <!-- ngNotificationsBar CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/ngNotificationsBar.min.css")?>">
    <!-- Bootstrap Select CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
    <!-- MD Table CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/css/md-data-table.css")?>">
    
    {css}
    
    <!-- JS Scripts -->
	 
	<!-- Latest jQuery form server -->
    <script src="https://code.jquery.com/jquery.min.js"></script>
	  
    <!-- Angular Material requires Angular.js Libraries -->
    <script src="<?php echo base_url("assets/js/angular-1.6.6/angular.min.js")?>"></script>
    <script src="<?php echo base_url("assets/js/angular-1.6.6/angular-animate.min.js")?>"></script>
    <script src="<?php echo base_url("assets/js/angular-1.6.6/angular-aria.min.js")?>"></script>
    <script src="<?php echo base_url("assets/js/angular-1.6.6/angular-messages.min.js")?>"></script>
    <script src="<?php echo base_url("assets/js/angular-1.6.6/angular-sanitize.min.js")?>"></script>
    <script src="<?php echo base_url("assets/js/angular-1.6.6/angular-route.min.js")?>"></script>
        

    <script src="<?php echo base_url("assets/js/lf-ng-md-file-input.js")?>"></script>

    <!-- Angular Material Library -->
    <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.4/angular-material.min.js"></script>  
	          
    <!-- Bootstrap JS form CDN -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="<?php echo base_url("assets/js/bootstrap-slider.min.js")?>"></script>
    
    <!-- jQuery sticky menu -->
    <script src="<?php echo base_url("assets/js/owl.carousel.min.js")?>"></script>
    <script src="<?php echo base_url("assets/js/jquery.sticky.js")?>"></script>
    
    <!-- jQuery easing -->
    <script src="<?php echo base_url("assets/js/jquery.easing.1.3.min.js")?>"></script>
    
    <!-- Angular JS Country/State Select -->
    <script src="<?php echo base_url("assets/js/md-country-select.js")?>"></script>
    
    <!-- Angular JS Country/State Select -->
    <script src="<?php echo base_url("assets/js/angular-country-state.js")?>"></script>
        
    <!-- Main Script -->
    <script src="<?php echo base_url("assets/js/main-controller.js")?>"></script>
    
    <script src="<?php echo base_url("assets/js/account-controller.js")?>"></script> 
    
    <!-- Admin Script -->
    <script src="<?php echo base_url("assets/js/admin.js")?>"></script>
    
    <!-- Menu Controller Script -->
    <script src="<?php echo base_url("assets/js/menu-controller.js")?>"></script>
    
    <!-- Cart Controller Script -->
    <script src="<?php echo base_url("assets/js/cart-controller.js")?>"></script>
    
    <!-- Shop Controller Script -->
    <script src="<?php echo base_url("assets/js/shop-controller.js")?>"></script>
	  
    <!-- Blog Controller Script -->
    <script src="<?php echo base_url("assets/js/blog-controller.js")?>"></script> 
    
    <script src="<?php echo base_url("assets/js/footer-controller.js")?>"></script> 
    
    <!-- ngNotificationsBar Script -->
    <script src="<?php echo base_url("assets/js/ngNotificationsBar.min.js")?>"></script>
    
    <!-- File Styles Script -->
    <script src="<?php echo base_url("assets/js/bootstrap-filestyle.js")?>"></script>
	
	<!-- Html2Canvas -->
    <script src="<?php echo base_url("assets/js/html2canvas.js")?>"></script>
    
    <!-- Bootstrap Select Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    
    <!-- International Phone Number Angular Module -->
    <script src="<?php echo base_url("assets/js/utils.js")?>"></script>
    <script src="<?php echo base_url("assets/js/intlTelInput.js")?>"></script>
    <script src="<?php echo base_url("assets/js/md-data-table.js")?>"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
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


            });
            
            var footerScope = angular.element($("#eapp-footer")).scope();
            
            footerScope.$apply(function()
            {
                footerScope.categories = JSON.parse('<?php echo $mostviewed_categories; ?>');
            });
        });
    </script>
	  
  <!-- Rootscope Script -->
  <script src="<?php echo base_url("assets/js/rootscope.js")?>"></script> 
	  
  </head>
  <body>
            
  <notifications-bar class="notifications"></notifications-bar>

    <div class="container search-box" id="search-box" ng-controller="ShopController" ng-hide="hideSearchArea" style="margin-top: 100px;" ng-cloak>
        
    <div class="row">
        <div ng-class="{'col-sm-12 col-md-12' : isUserLogged, 'col-sm-12 col-md-6' : !isUserLogged}">
            <form ng-submit="searchProducts(searchText)">
                <md-input-container class="md-icon-float md-icon-right md-block">
                    <label>Rechercher produits</label>
                    <input name="searchText" ng-model="searchText" aria-label="Rechercher" />
                    <md-icon ng-hide="true"><i class="material-icons">search</i></md-icon>

                </md-input-container>
            </form>
        </div>
        <div class="col-sm-12 col-md-6">
            <p ng-hide="isUserLogged">Résultats optimisés pour {{currentAddress}} | <a href="<?php echo site_url("/home/change_location"); ?>">Changer</a></p>
        </div>
    </div>
        
    </div>
    
    <div id="mainmenu-area" class="mainmenu-area" class="navbar-wrapper" ng-cloak>
        <div class="container-fluid">
            <nav class="navbar navbar-fixed-top">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#"><img src="<?php echo base_url("assets/img/logo.png"); ?>" class="eapp-logo" /></a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav"  ng-controller="MenuController">
                            <li class=" dropdown" ng-show="loggedUser.subscription > 0">
                                <a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li ng-show="loggedUser.subscription == 2"><a  href="<?php echo addslashes(site_url("admin/uploads")); ?>">Uploads</a></li>
                                    <li><a href="<?php echo addslashes(site_url("admin/create_store_product")); ?>">Create Product</a></li>
                                    <li><a href="<?php echo addslashes(site_url("admin/store_products")); ?>">View Products</a></li>
                                </ul>
                            </li>
                            
                            <li ng-class="{active : isHome}"><a href="<?php echo site_url("home"); ?>" class="">Accueil</a></li>
                            
                            <li class=" dropdown" ng-class="{active : isMainMenu}">
                                <a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Réduisez vos dépenses<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo site_url("account/my_grocery_list"); ?>">Votre liste d'épicerie</a></li>
                                    <li><a href="<?php echo site_url("shop/select_flyer_store"); ?>">Les circulaires des magasins</a></li>
                                    <li><a href="<?php echo site_url("shop/categories"); ?>">Les catégories de produits</a></li>
                                </ul>
                            </li>
                            <li ng-class="{active : isSearch}"><a href ng-click="gotoShop()">Trouvez un produit</a></li>
                            <li ng-hide="true" ng-class="{active : isCart}"><a href="<?php echo site_url("cart"); ?>">Votre panier</a></li>
                            <li ng-hide="true" class=" dropdown"><a href="#" class="dropdown-toggle " data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Blogue<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo site_url("blog/press_release"); ?>">Épicerie dans la presse</a></li>
                                    <li><a href="<?php echo site_url("blog/stats"); ?>">STAT</a></li>
                                    <li><a href="<?php echo site_url("blog/videos"); ?>">Vidéo</a></li>
                                    <!--<li><a href="<?php echo site_url("home/store_policy"); ?>">Politiques des magasins</a></li>-->
                                    <li><a href="<?php echo site_url("home/about_us"); ?>">À propos</a></li>
                                </ul>
                            </li>
                            <li><a  href="<?php echo site_url("home/contact"); ?>">Contact</a></li>
							<li><a  href="<?php echo site_url("home/about"); ?>">À propos</a></li>
                        </ul>
                        <ul class="nav navbar-nav pull-right"  ng-controller="AccountController">
                            <li ng-hide="isUserLogged"><a href="<?php echo site_url("account/login"); ?>"><i class="fa fa-user"></i>    S'identifier</a></li>
                            <li ng-hide="isUserLogged"><a href="<?php echo site_url("account/register"); ?>"><i class="fa fa-user"></i>    Créer un compte</a></li>
                            <li ng-show="isUserLogged" class=" dropdown"><a href="#" class="dropdown-toggle active" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Bonjour <span ng-show="loggedUser.profile.firstname">{{loggedUser.profile.firstname}},</span> {{loggedUser.profile.lastname}}  <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a  href="<?php echo site_url("account"); ?>"><i class="fa fa-user"></i> Mon compte</a></li>
                                    <li><a href="<?php echo site_url("account/my_grocery_list"); ?>"><i class="fa fa-heart"></i> Ma liste d'épicerie</a></li>
                                    <li><a href ng-click="logout()">Logout</a></li>
                                </ul>
                            </li>
                            <li ng-class="{active : isCart}">
                                <a href="<?php echo site_url("cart"); ?>" class="md-icon-button" aria-label="Cart">
                                    <md-icon><i class="material-icons">shopping_cart</i> </md-icon>
                                    <span class="badge" ng-show="get_cart_item_total() > 0">{{get_cart_item_total()}} | CAD {{get_cart_total_price() | number : 2}}</span>
                                </a>
                                
                            </li>
                        </ul>
                    </div>
            </div>
        </nav>
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
                        <h2>oti<span>Prix</span></h2>
                        <p>  
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
                        <h2 class="footer-wid-title">Navigation de l'utilisateur</h2>
                        <ul>
                            <li><a href="<?php echo site_url("account"); ?>">Mon compte</a></li>
                            <li><a href="<?php echo site_url("account/my_grocery_list"); ?>">Ma liste d'epicerie</a></li>
                            <li><a href="<?php echo site_url("blog/press_release"); ?>">Presse</a></li>
                            <li><a href="#">Contacter nous</a></li>
                            <li><a href  onclick="window.open('<?php echo base_url("/assets/files/terms_and_conditions.pdf")?>', '_blank', 'fullscreen=yes'); return false;">Terme et conditions</a></li>
                        </ul>                        
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="footer-menu">
                        <h2 class="footer-wid-title">Categories</h2>
                        <ul>
                            <li ng-click="select_category($event, category)" id="{{category.id}}"  ng-repeat="category in categories"><a href>{{category.name}}</a></li>
                        </ul>                        
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="footer-newsletter">
                        <h2 class="footer-wid-title">Bulletin</h2>
		    	<p>Inscrivez-vous à notre Infolettre et soyez les premiers informés sur :</p>
		     	<p> - l’évolution des prix des denrées alimentaires;</p>
		    	<p> - toutes les opportunités  pour réduire le coût de votre épicerie.</p>
                        <div class="newsletter-form" ng-hide="true">
                            <form action="#">
                                <input type="email" placeholder="Entrez votre email">
                                <div class="col-sm-12">
                                    <md-button type="submit" class="md-raised md-otiprix pull-right" >S’INSCRIRE</md-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="copyright">
                        <p>&copy; 2017 otiPrix. All Rights Reserved.</p>
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
    
    
   {scripts}
  </body>
</html>
