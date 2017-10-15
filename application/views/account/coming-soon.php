<!DOCTYPE html>

<html lang="en" ng-app="pageApp">

    <head>
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.4/angular-material.min.css">
        <link rel="stylesheet" href="<?php echo base_url("assets/css/tether.css")?>">
        <link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap.css")?>">
        <link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap-grid.css")?>">
        <link rel="stylesheet" href="<?php echo base_url("assets/css/bootstrap-reboot.css")?>">
        <link rel="stylesheet" href="<?php echo base_url("assets/css/construction-page.css")?>">

        <script src="https://code.jquery.com/jquery.min.js"></script>

        <!-- Angular Material requires Angular.js Libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-animate.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-aria.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-messages.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular-sanitize.min.js"></script>
        <!-- Angular Material Library -->
        <script src="https://ajax.googleapis.com/ajax/libs/angular_material/1.1.4/angular-material.min.js"></script>  
        <!-- Bootstrap JS form CDN -->
        <script src="<?php echo base_url("assets/js/tether.js")?>"></script>
        <script src="<?php echo base_url("assets/js/bootstrap.js")?>"></script>
        <script src="<?php echo base_url("assets/js/construction-page.js")?>"></script>
        
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
                });
            });
        </script>
    </head>    

    <body class="bgimg container" style="height: 100%; width : 100%;" ng-controller="AccountController">
        <div class="row">
			
            <div class="col-sm-6" style="margin : auto;">
                <img src="<?php echo base_url("assets/img/logo.png")?>" style="width : 100%;" />
            </div>
    
            <div class="col-sm-6" style="margin-top : 5%;">
        		<h1 style="text-align: center;">Site en construction</h1>
        		<hr>
        		<p id="time" style="text-align: center;">35 days</p>


                <md-content style="margin : auto; background-color: whitesmoke; min-width : 450px;" class="col-sm-8" layout-padding>
	                <form name="loginForm" class="" role="form" ng-submit="login()" novalidate>
	                    <div id="login-alert" class="alert alert-danger col-sm-12" ng-show="message">
	                        <p>{{message}}</p>
	                    </div>
	                    <p style="text-align: center;">Admin Login</p>
	                    <md-input-container class="md-block col-md-12" flex-gt-sm>
	                        <input required name="email" ng-model="user.email" />
	                        <div ng-messages="loginForm.email.$error">
	                            <div ng-message="required">Veillez entrer votre addresse email.</div>
	                        </div>
	                    </md-input-container>
	                
		                <md-input-container class="md-block col-md-12" flex-gt-sm>
		                    <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="user.password" />
		                    <div ng-messages="loginForm.password.$error">
		                        <div ng-message="required">Veillez entrer un mot de passe.</div>
		                    </div>
		                </md-input-container>
		                    
		                <div class="col-sm-12">
		                    <div class="input-group checkbox">
		                        <label>
		                            <input id="login-remember" type="checkbox" ng-model="user.rememberme" name="remember"> Rester connecté
		                        </label>
		                    </div>
		                </div>
		                                                       
		                <div class="col-sm-12">
		                    <button id="btn-signup" type="submit" class="pull-right btn btn-primary"><i class=""></i> &nbsp Se connecter</button>
		                </div>
	                </form>
                </md-content>

        
    </div>
    </div> 
    </body>
</html>



