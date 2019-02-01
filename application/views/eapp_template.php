<!DOCTYPE html>
<html lang="en" ng-app="eappApp">
  <head>
    <base href="/" />
    <link rel="icon" type="image/x-icon" href="<?php echo base_url("assets/img/favicon/")?>favicon.ico" sizes="32x32" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">   
    <script src="<?php echo base_url("node_modules/jquery/dist/jquery.min.js")?>"></script>
    
    <title>{title}</title>
    
    {css}
    
    	  
  </head>
  
  <body class="md-body-1">
        
        
     
        <div class="otiprix-header" ng-controller="MenuController as menuCtrl">

            <div class="top-header" ng-cloak>

              <div class="container">

                  <div layout="row">

                      <div class="col-lg-3 hide-text">
                          <div class="pull-left header-social">
                              <a href="https://fr-fr.facebook.com/pages/category/Grocery-Store/Otiprix-2231399793546712/" target="_blank"><i otiprix-text class="fa fa-facebook"></i></a>
                              <a href="https://twitter.com/otiprix" target="_blank"><i otiprix-text class="fa fa-twitter"></i></a>
                              <a href="https://www.youtube.com/channel/UCbwxS8s1WKYgGCRzd9vIl5A" target="_blank"><i otiprix-text class="fa fa-youtube"></i></a>
                              <a ng-hide="true" href="https://www.instagram.com/otiprix/" target="_blank"><i otiprix-text class="fa fa-instagram"></i></a>
                              <a ng-hide="true" href="https://plus.google.com/u/0/117638375580963001925" target="_blank"><i class="md-primary" class="fa fa-google-plus"></i></a>
                          </div>
                      </div>
                      
                      <div class="col-lg-6 col-sm-6" >
                            <p style="text-align: center;" id="step1">
                                <b>
                                    <span>{{optimizationDistance}} Km de </span>
                                    <a href ng-click="menuCtrl.changeAddress($event)">
                                        <md-tooltip>
                                            Changer address
                                        </md-tooltip>
                                        {{postcode}} | Changer
                                    </a>
                                </b>
                            </p>
                      </div>

                      <div flex>

                            <div  id="step5" class="pull-right" style="border: 2px solid rgb(255,87,34); padding: 1px;">
                                <span>
                                    <a href="<?php echo site_url("cart"); ?>" style="color : rgb(255,87,34);" class="md-icon-button" aria-label="Cart">
                                        <md-icon><i class="material-icons" style="color : rgb(255,87,34);">shopping_cart</i></md-icon>
                                        <span class="badge text-white" style="background-color : rgb(255,87,34);" ng-show="getTotalItemsInCart() > 0">{{getTotalItemsInCart()}} | {{getCartPrice() | number : 2}} C $</span>
                                    </a>
                                </span>
                            </div>

                      </div>

                  </div>

              </div>

          </div>

            <div class="navbar-wrapper text-white" ng-cloak>
                <nav otiprix-background class="navbar navbar-expand-lg fixed-top" style="top : 50px;">
                    
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div id="navbar" class="navbar-collapse collapse">

                        <!-- Branding -->
                        <a class="navbar-brand" href="<?php echo site_url("home"); ?>">
                            <span><img src="<?php echo base_url("assets/img/logo.png"); ?>" class="eapp-logo" /></span>
                            <span class="align-middle" style="font-size: 14px;">Beta</span>
                        </a>

                        <ul class="nav navbar-nav">

                            <li ng-class="{active : selectedMenu == 100}" class="nav-item dropdown " ng-show="loggedUser.subscription == 2">
                                <a href class="dropdown-toggle nav-link align-middle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin<span class="caret"></span></a>
                                <div class="dropdown-menu" otiprix-background>
                                    <a class="dropdown-item" ng-show="loggedUser.subscription == 2" href="<?php echo addslashes(site_url("admin/uploads")); ?>">Uploads</a>
                                    <a class="dropdown-item" href="<?php echo addslashes(site_url("admin/create_store_product")); ?>">Create Product</a>
                                    <a class="dropdown-item" href="<?php echo addslashes(site_url("admin/store_products")); ?>">View Store Products</a>
                                    <a class="dropdown-item" href="<?php echo addslashes(site_url("admin/view_products")); ?>">View Otiprix Products</a>
                                    <a class="dropdown-item" href="<?php echo addslashes(site_url("admin/view_subcategories")); ?>">View Sub Categories</a>
                                    <a class="dropdown-item" href="<?php echo addslashes(site_url("admin/scrap_data")); ?>">Scrap Data</a>
                                    <a class="dropdown-item" href="<?php echo addslashes(site_url("admin/user_accounts")); ?>">Accounts</a>
                                </div>
                            </li>

                            <li ng-class="{active : selectedMenu == 0}" class="nav-item " ><a class="nav-link align-middle" href="<?php echo site_url("home"); ?>" ><md-icon class="text-white"><i class="material-icons">home</i> </md-icon></a></li>

                            <li id="step3" class="nav-item dropdown" ng-class="{active : selectedMenu == 1}">
                                <a href class="dropdown-toggle nav-link align-middle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Réduisez vos dépenses<span class="caret"></span></a>
                                <div class="dropdown-menu" otiprix-background>
                                    <a class="dropdown-item" href="<?php echo site_url("account/my_grocery_list"); ?>">Votre liste d'épicerie</a>
                                    <a class="dropdown-item" href="<?php echo site_url("shop/select_flyer_store"); ?>">Les circulaires des magasins</a>
                                    <a class="dropdown-item" href="<?php echo site_url("shop/categories"); ?>">Les catégories de produits</a>
                                </div>
                            </li>

                            <li id="step4" class="nav-item" ng-class="{active : selectedMenu == 2}" ><a class="nav-link align-middle" href ng-click="gotoShop()"><span class="p-2 border rounded border-white">Trouvez un produit</span></a></li>
                            <li ng-class="{active : selectedMenu == 3}" class="nav-item " ><a class="nav-link align-middle" href="<?php echo site_url("home/contact"); ?>">Contact</a></li>
                            <li ng-class="{active : selectedMenu == 4}" class="nav-item " ><a class="nav-link align-middle"  href="<?php echo site_url("home/about"); ?>">À propos</a></li>
                        </ul>

                        <ul class="nav navbar-nav"  ng-controller="AccountController" id="step6">
                            <li ng-class="{active : selectedMenu == 5}" class="nav-item" ng-hide="isUserLogged"><a class="align-middle nav-link" href="<?php echo site_url("account/login"); ?>"><i style="color : #F7FDCA;" class="fa fa-user"></i>    S'identifier</a></li>
                            <li ng-class="{active : selectedMenu == 6}" class="nav-item" ng-hide="isUserLogged"><a class="align-middle nav-link" href="<?php echo site_url("account/register"); ?>"><i style="color : #F7FDCA;" class="fa fa-user"></i>    Créer un compte</a></li>
                            <li ng-class="{active : selectedMenu == 5}" class="nav-item dropdown" ng-show="isUserLogged">
                                <a href="#" class="dropdown-toggle nav-link active" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                    Bonjour 
                                    <span ng-if="!loggedUser.company"><span ng-show="loggedUser.profile.firstname">{{loggedUser.profile.firstname}},</span> {{loggedUser.profile.lastname}}</span>
                                    <span ng-if="loggedUser.company">{{loggedUser.company.name}}</span>
                                    <span class="caret"></span>
                                </a>
                                <div class="dropdown-menu" otiprix-background>
                                    <a class="dropdown-item" href="<?php echo site_url("account"); ?>"><i style="color : #F7FDCA;" class="fa fa-user mr-2"></i>Mon compte</a>
                                    <a class="dropdown-item" ng-if="isRegularUser" href="<?php echo site_url("account/my_grocery_list"); ?>"><i style="color : #F0776C;" class="fa fa-heart mr-2"></i>Ma liste d'épicerie</a>
                                    <a class="dropdown-item" href ng-click="logout()">Logout</a>
                                </div>
                            </li>
                        </ul>

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
                            <a href="<?php echo site_url("home"); ?>"><img src="<?php echo base_url("assets/img/logo.png"); ?>" class="eapp-logo-footer" /></a>
                            <p style="margin-top : 10px;">  
                                En un seul clic, OtiPrix réduit le coût de votre panier d’épicerie en identifiant les meilleurs et les vrais rabais dans les magasins proches de vous. Avec OtiPrix, consulter en un seul et même endroit l’ensemble des produits alimentaires en rabais dans les grandes surfaces, mais aussi dans tous les petits magasins situés à proximité de votre lieu de résidence.
                            </p>
                            <div class="footer-social">
                                <a href="https://fr-fr.facebook.com/pages/category/Grocery-Store/Otiprix-2231399793546712/" target="_blank"><i class="fa fa-facebook"></i></a>
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
                                <li><a href="<?php echo site_url("home/contact"); ?>">Contacter nous</a></li>
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
                                    <div class="col-sm-18">
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
                            <p>&copy; {{currentYear}} OtiPrix. Tous droits réservés.</p>
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
  
</html>