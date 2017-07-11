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
                        <li class="active"><a href="http://<?php echo site_url("cart"); ?>">Panier</a></li>
                        <li><a href="#">Catégories</a></li>
                        <li><a href="#">Dépliants</a></li>
                        <li><a href="#">Contactez nous</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div> 
    <!-- End mainmenu area -->

<div class="form-connex"> <!-- Texte a modifier -->
	    <p class="pg_connex">
            Si vous êtes un nouveau client, passez à la section commande et expédition.
        </p>
</div>


<form class="form-inline">
	
	<div class="form-group"> <!-- Nom d'utilisateur -->
		<input class="form-control" id="username_id" name="user_name" type="text" placeholder="nom d'utilisateur"/>
	</div>
	
	<div class="form-group"> <!-- Mot de passe -->
		<input class="form-control" id="password_id" name="user_password" type="password" placeholder="Mot de passe"/>
	</div>
	
	<div class="form-group"> <!-- Boutton Login  -->
		<button class="btn btn-primary " name="submit" type="submit">S'identifier</button>
	</div>	
	
	<div class="form-group"> <!-- Se rappeler -->
		<div class="checkbox">
			<label class="checkbox">
			<input name="remember" type="checkbox" value="yes"/>
				Se rappeler de moi
			</label>
		</div>
	</div>

</form>

<div class="d-inline"> <!-- Lien vers page recovery -->
	    <p class="pg_connex">
            <a href="#">Mot de passe oublié</a>
        </p>
</div>


		

