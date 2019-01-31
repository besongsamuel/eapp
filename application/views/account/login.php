
<div id="fb-root"></div>

<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '347670992472912',
      cookie     : true,
      xfbml      : true,
      version    : 'v3.2'
    });
      
    FB.AppEvents.logPageView();   
      
  };

(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/fr_CA/sdk.js#xfbml=1&version=v3.2&appId=347670992472912&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function loginFBUser()
{
    FB.getLoginStatus(function(response) 
    {
        
        var scope = angular.element($("#admin-container")).scope();

        scope.socialLogin = true;
        
        scope.$apply();
        
        // Log the user if he is connected. 
        if(response.status  === 'connected')
        {
            // Send the auth token to the server
            $.ajax(
            {
                type : "POST", 
                url : "<?php echo site_url("account/facebook_login"); ?>", 
                data : { token : response.authResponse}, 
                dataType : "json",
                success : function(loginResponse)
                {
                    scope.socialLogin = true;

                    scope.$apply();

                    // Redirect
                    if(loginResponse.success)
                    {
                        location.href = "<?php echo site_url(); ?>".concat("/").concat(loginResponse.redirect);
                    }
                }
            });
        }
        else
        {
            scope.message = "Échec de l'authentification.";
            scope.socialLogin = true;
            scope.$apply();
        }
    });
}

  
</script>

<link href="<?php echo base_url("assets/css/login.css"); ?>" rel="stylesheet">


<div id="admin-container" class="container mainbox" ng-controller="AccountController as ctrl" ng-cloak>
    
   <div class="row justify-content-center">
       
      <div class="card m-1" >
        
        <md-toolbar class="md-primary">
            <div class="md-toolbar-tools">
                <span>Se connecter </span>
                <span flex><img class="pull-right" src="<?php echo base_url("assets/img/logo.png"); ?>" style="height : 40px; margin : 10px;" /></span>
            </div>
            
        </md-toolbar>  
          
        <div class="login-box">
            
            <div id="login-alert" class="alert alert-danger col-sm-12" ng-show="message">
               <p>{{message}}</p>
            </div>
            
            <div class="row justify-content-center my-2" layout-padding>
                <div class="fb-login-button" onlogin="loginFBUser()" data-max-rows="1" data-scope="public_profile,email,user_location" data-size="large" data-button-type="continue_with" data-auto-logout-link="false" data-use-continue-as="true"></div>
            </div>
            
            <div ng-if="socialLogin"  class="row justify-content-center my-2">
                <md-progress-circular md-mode="indeterminate"></md-progress-circular>
            </div> 
            
            <form name="loginForm" class="container-fluid" role="form" ng-submit="login()" novalidate>

                <div class="row">
                    
                    <md-input-container class="col-12">
                        <label class="md-no-float">Email</label>
                        <md-icon class="md-primary"><i class="material-icons">email</i></md-icon>
                        <input required name="email" ng-model="user.email" />
                        <div ng-messages="loginForm.email.$error">
                            <div ng-message="required">Veillez entrer votre addresse email.</div>
                        </div>
                    </md-input-container>

                    <md-input-container class="col-12">
                        <label class="md-no-float">Mot de passe</label>
                        <md-icon class="md-primary"><i class="material-icons">lock</i></md-icon>
                        <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="user.password" />
                        <div ng-messages="loginForm.password.$error">
                            <div ng-message="required">Veillez entrer un mot de passe.</div>
                        </div>
                    </md-input-container>
                </div>
                
                <div class="row justify-content-center">
                    <md-checkbox ng-model="user.rememberme" aria-label="Rester connecté">
                        Rester connecté
                    </md-checkbox>
                </div>
                
                <div class="row justify-content-end mb-4">
                  <md-button class="md-raised md-primary pull-right" type="submit">
                      Se connecter
                  </md-button>
                </div>

                <p class="text-center"><a href="<?php echo site_url("/account/password_forgotten") ?>">Mot de passe oublié?</a></p>
                
                <p  class="text-center my-2">
                    Vous n'avez pas encore de compte? <a href="<?php echo site_url("/account/register") ?>" >Créer un compte</a>
                </p>
                
                
          </form>
       </div>
          
      </div>
       
   </div>
   
</div>
