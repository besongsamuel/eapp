<!DOCTYPE html>
<html lang="en" ng-app="eappApp">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{title}</title>
     <!-- Angular Material style sheet -->
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.4/angular-material.min.css">
    <link rel="stylesheet" href="http://<?php echo base_url("assets/css/lf-ng-md-file-input.css")?>">
      <!-- Angular Material requires Angular.js Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-animate.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-aria.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-messages.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-sanitize.min.js"></script>
    <script src="http://<?php echo base_url("assets/js/lf-ng-md-file-input.js")?>"></script>

    <!-- Angular Material Library -->
    <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.4/angular-material.min.js"></script>
    
    <!-- Google Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,700,600' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,700,300' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Raleway:400,100' rel='stylesheet' type='text/css'>
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="http://<?php echo base_url("assets/css/bootstrap-slider.css")?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="http://<?php echo base_url("assets/css/owl.carousel.css")?>">
    <link rel="stylesheet" href="http://<?php echo base_url("assets/css/style.css")?>">
    <link rel="stylesheet" href="http://<?php echo base_url("assets/css/responsive.css")?>">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="http://<?php echo base_url("assets/css/admin.css")?>">
    <!-- International Phone numbers CSS CSS -->
    <link rel="stylesheet" href="http://<?php echo base_url("assets/css/intlTelInput.css")?>">
    
    <!-- Animate CSS -->
    <link rel="stylesheet" href="http://<?php echo base_url("assets/css/animate.css")?>">
    <!-- ngNotificationsBar CSS -->
    <link rel="stylesheet" href="http://<?php echo base_url("assets/css/ngNotificationsBar.min.css")?>">
    <!-- Bootstrap Select CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
    <!-- MD Table CSS -->
    <link rel="stylesheet" href="http://<?php echo base_url("assets/css/md-data-table.css")?>">
    
    
    
    <!-- JS Scripts -->
        <!-- Latest jQuery form server -->
    <script src="https://code.jquery.com/jquery.min.js"></script>
    
    <!-- Bootstrap JS form CDN -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="http://<?php echo base_url("assets/js/bootstrap-slider.min.js")?>"></script>
    
    <!-- jQuery sticky menu -->
    <script src="http://<?php echo base_url("assets/js/owl.carousel.min.js")?>"></script>
    <script src="http://<?php echo base_url("assets/js/jquery.sticky.js")?>"></script>
    
    <!-- jQuery easing -->
    <script src="http://<?php echo base_url("assets/js/jquery.easing.1.3.min.js")?>"></script>
    
    <!-- Angular JS Country/State Select -->
    <script src="http://<?php echo base_url("assets/js/md-country-select.js")?>"></script>
    
    <!-- Angular JS Country/State Select -->
    <script src="http://<?php echo base_url("assets/js/angular-country-state.js")?>"></script>
    
    <!-- Main Script -->
    <script src="http://<?php echo base_url("assets/js/main.js")?>"></script>
    
    <!-- Admin Script -->
    <script src="http://<?php echo base_url("assets/js/admin.js")?>"></script>
    
    <!-- Menu Controller Script -->
    <script src="http://<?php echo base_url("assets/js/menu-controller.js")?>"></script>
    
    <!-- Menu Controller Script -->
    <script src="http://<?php echo base_url("assets/js/cart-controller.js")?>"></script>
    
    <!-- ngNotificationsBar Script -->
    <script src="http://<?php echo base_url("assets/js/ngNotificationsBar.min.js")?>"></script>
    
    <!-- File Styles Script -->
    <script src="http://<?php echo base_url("assets/js/bootstrap-filestyle.js")?>"></script>
    
    <!-- Bootstrap Select Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    
    <!-- International Phone Number Angular Module -->
    <script src="http://<?php echo base_url("assets/js/utils.js")?>"></script>
    <script src="http://<?php echo base_url("assets/js/intlTelInput.js")?>"></script>
    <script src="http://<?php echo base_url("assets/js/md-data-table.js")?>"></script>

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
            rootScope.cart = [];
            // Get the current geo location only if it's not yet the case
            if ("geolocation" in navigator && !window.sessionStorage.getItem("longitude") && !window.sessionStorage.getItem("latitude")) 
            {
                navigator.geolocation.getCurrentPosition(function(position) 
                {
                    rootScope.longitude = position.coords.longitude;
                    rootScope.latitude = position.coords.latitude;
                    window.sessionStorage.setItem("longitude", rootScope.longitude);
                    window.sessionStorage.setItem("latitude", rootScope.latitude);
                    
                    $.ajax(
                    {
                        type : 'POST',
                        url : "http://" + rootScope.site_url.concat("/cart/get_cart_contents"),
                        data : { longitude : rootScope.longitude, latitude : rootScope.latitude},
                        success : function(data)
                        {
                            if(data)
                            {
                                rootScope.cart = JSON.parse(data);
                            }
                        },
                        async : false
                    });
                    
                });
            }
            else
            {
                rootScope.longitude = parseFloat(window.sessionStorage.getItem("longitude"));
                rootScope.latitude = parseFloat(window.sessionStorage.getItem("latitude"));
                
                // get cart contents
                $.ajax(
                {
                    type : 'POST',
                    url : "http://" + rootScope.site_url.concat("/cart/get_cart_contents"),
                    data : { longitude : rootScope.longitude, latitude : rootScope.latitude},
                    success : function(data)
                    {
                        if(data)
                        {
                            rootScope.cart = JSON.parse(data);
                        }
                    },
                    async : false
                });
            }
            rootScope.is_loading = false;
            rootScope.valid = true;
            rootScope.success_message = "";
            rootScope.error_message = "";
            var user = '<?php echo $user; ?>';
            if(user === "" || user == "null")
            {
                rootScope.loggedUser = null;
            }
            else
            {
                rootScope.loggedUser = JSON.parse(user);
            }
            
            rootScope.hideSearchArea = (rootScope.controller == "account" && (rootScope.method == "login" || rootScope.method == "register"));
            
            rootScope.isUserLogged = rootScope.loggedUser !== null;
        });
        
    });
    </script>
  </head>
  <body>
    <notifications-bar class="notifications"></notifications-bar>
    <!-- Begin Header Section -->   
    <div class="header-area" ng-controller="AccountController">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="user-menu">
                        <ul>
                            <li ng-show="isUserLogged"><a href="#"><i class="fa fa-user"></i>Mon compte</a></li>
                            <li ng-show="isUserLogged"><a href="#"><i class="fa fa-heart"></i>Ma liste d'épicerie</a></li>
                            <li><a href="http://<?php echo site_url("cart"); ?>"><i class="fa fa-user"></i>Mon panier</a></li>
                            <li ng-hide="isUserLogged"><a href="http://<?php echo site_url("account/login"); ?>"><i class="fa fa-user"></i>s'identifier</a></li>
                            <li ng-show="isUserLogged">
                            <md-menu>
				<a href md-menu-origin  ng-click="$mdMenu.open($event)" class="main-menu-item">Bonjour, {{loggedUser.profile.firstname}}</a>
				<md-menu-content>
                                    <md-menu-item><a href ng-click="logout()">Logout</a></md-menu-item>
                                    <md-menu-item><a href="http://<?php echo site_url("account/account"); ?>">Mon Compte</a></md-menu-item>
				</md-menu-content>
			    </md-menu>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="header-right">
                        <ul class="list-unstyled list-inline">
                            <li class="dropdown dropdown-small">
                                <a data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" href="#"><span class="key">Devise :</span><span class="value">USD </span><b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">USD</a></li>
                                    <li><a href="#">INR</a></li>
                                    <li><a href="#">GBP</a></li>
                                </ul>
                            </li>

                            <li class="dropdown dropdown-small">
                                <a data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" href="#"><span class="key">Langue :</span><span class="value">Anglais </span><b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Anglais</a></li>
                                    <li><a href="#">Français</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    <!-- End header area -->
    
    <!-- Begin Site Branding Section -->
    <div class="site-branding-area" ng-controller="CartController" ng-hide="hideSearchArea">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class="logo">
                        <h1><a href="index.html">Epicery<span></span></a></h1>
                    </div>
                </div>
                
                <div class="col-sm-6">
                    <div class="shopping-item">
                        <a href="http://<?php echo site_url("cart"); ?>">Cart - <span class="cart-amunt">CAD {{get_cart_total_price() | number : 2}}</span> <i class="fa fa-shopping-cart"></i> <span class="product-count">{{get_cart_item_total()}}</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    <!-- End site branding area -->
    
    <div class="container search-box" ng-controller="CartController" ng-hide="hideSearchArea">
        <div class="row">
            <form action="#">
                <div class="col-md-11 single-sidebar">
                    <input type="text" placeholder="Search products...">
                </div>
                <div class="col-md-1">
                    <input type="submit" value="Search">
                </div>
            </form>
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
			<li style="padding : 20px;">
			    <md-menu>
				<a href md-menu-origin  ng-click="$mdMenu.open($event)" class="main-menu-item">Réduisez vos dépenses</a>
				<md-menu-content>
					<md-menu-item><a href="http://<?php echo site_url("account/my_list"); ?>">Votre liste d'épicerie</a></md-menu-item>
					<md-menu-item><a href="http://<?php echo site_url("shop/select_flyer_store"); ?>">Les circulaires des magasins</a></md-menu-item>
					<md-menu-item><a href="http://<?php echo site_url("shop/categories"); ?>">Les catégories de produits</a></md-menu-item>
				</md-menu-content>
			    </md-menu>
		  	</li>
                        <li><a href ng-click="gotoShop()">Trouvez un produit</a></li>
			<li><a href="http://<?php echo site_url("cart"); ?>">Votre panier</a></li>
                        <li style="padding : 20px;">
				<md-menu>
					<a href md-menu-origin  ng-click="$mdMenu.open($event)" class="main-menu-item">Blogue</a>
					<md-menu-content>
					  	<md-menu-item><a href="http://<?php echo site_url("home/grocery_press"); ?>">Épicerie dans la presse</a></md-menu-item>
					  	<md-menu-item><a href="http://<?php echo site_url("home/stats"); ?>">STAT</a></md-menu-item>
					  	<md-menu-item><a href="http://<?php echo site_url("home/video"); ?>">Vidéo</a></md-menu-item>
					  	<md-menu-item><a href="http://<?php echo site_url("home/store_policy"); ?>">Politiques des magasins</a></md-menu-item>
					  	<md-menu-item><a href="http://<?php echo site_url("home/about_us"); ?>">À propos</a></md-menu-item>
					</md-menu-content>
				</md-menu>
			 </li>
			 <li style="padding : 20px;">
				<md-menu>
					<a href md-menu-origin ng-click="$mdMenu.open($event)" class="main-menu-item">Contact</a>
					<md-menu-content>
						<md-menu-item><a href="http://<?php echo site_url("home/contact"); ?>">Formulaire</a></md-menu-item>
					</md-menu-content>
				</md-menu>
			 </li>

                    </ul>
                </div>
            </div>
        </div>
    </div> 
	  
    <!-- End mainmenu area -->
    <div id="main-body">	
    	{body}
    </div>

    <div class="footer-top-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="footer-about-us">
                        <h2>e<span>Electronics</span></h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Perferendis sunt id doloribus vero quam laborum quas alias dolores blanditiis iusto consequatur, modi aliquid eveniet eligendi iure eaque ipsam iste, pariatur omnis sint! Suscipit, debitis, quisquam. Laborum commodi veritatis magni at?</p>
                        <div class="footer-social">
                            <a href="#" target="_blank"><i class="fa fa-facebook"></i></a>
                            <a href="#" target="_blank"><i class="fa fa-twitter"></i></a>
                            <a href="#" target="_blank"><i class="fa fa-youtube"></i></a>
                            <a href="#" target="_blank"><i class="fa fa-linkedin"></i></a>
                            <a href="#" target="_blank"><i class="fa fa-pinterest"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="footer-menu">
                        <h2 class="footer-wid-title">User Navigation </h2>
                        <ul>
                            <li><a href="#">My account</a></li>
                            <li><a href="#">Order history</a></li>
                            <li><a href="#">Wishlist</a></li>
                            <li><a href="#">Vendor contact</a></li>
                            <li><a href="#">Front page</a></li>
                        </ul>                        
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="footer-menu">
                        <h2 class="footer-wid-title">Categories</h2>
                        <ul>
                            <li><a href="#">Mobile Phone</a></li>
                            <li><a href="#">Home accesseries</a></li>
                            <li><a href="#">LED TV</a></li>
                            <li><a href="#">Computer</a></li>
                            <li><a href="#">Gadets</a></li>
                        </ul>                        
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="footer-newsletter">
                        <h2 class="footer-wid-title">Newsletter</h2>
                        <p>Sign up to our newsletter and get exclusive deals you wont find anywhere else straight to your inbox!</p>
                        <div class="newsletter-form">
                            <form action="#">
                                <input type="email" placeholder="Type your email">
                                <input type="submit" value="Subscribe">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End footer top area -->
    
    <div class="footer-bottom-area">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="copyright">
                        <p>&copy; 2015 eElectronics. All Rights Reserved. Coded with <i class="fa fa-heart"></i> by <a href="http://wpexpand.com" target="_blank">WP Expand</a></p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="footer-card-icon">
                        <i class="fa fa-cc-discover"></i>
                        <i class="fa fa-cc-mastercard"></i>
                        <i class="fa fa-cc-paypal"></i>
                        <i class="fa fa-cc-visa"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End footer bottom area -->
   
  </body>
</html>
