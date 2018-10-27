
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
        // Log the user if he is connected. 
        if(response.status  === 'connected')
        {
            FB.api(
            '/me',
            {fields: "email,first_name,gender,hometown,location,middle_name,name,last_name"}, 
            function(response2) 
            {
                
            });
        }
    });
}

  
</script>

<link href="<?php echo base_url("assets/css/login.css"); ?>" rel="stylesheet">


<div id="admin-container" class="container mainbox" ng-controller="AccountController as ctrl" ng-cloak>
   <div id="loginbox" class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
      <div class="panel panel-info" >
        
        <md-toolbar class="md-primary">
            <div>
                <h2 class="md-toolbar-tools">Se connecter</h2>
            </div>
        </md-toolbar>  
          
        <md-content class="login-box">
            
            <div id="login-alert" class="alert alert-danger col-sm-12" ng-show="message">
               <p>{{message}}</p>
            </div>
            
             <div class="col-12" style="text-align: center; margin : 10px;">
                <div class="fb-login-button" onlogin="loginFBUser()" data-max-rows="1" data-scope="public_profile,email,user_location" data-size="large" data-button-type="continue_with" data-auto-logout-link="false" data-use-continue-as="true"></div>
            </div>
            
            <form name="loginForm" class="form-horizontal" role="form" ng-submit="login()" novalidate>

                <md-input-container class="md-block col-md-12" flex-gt-sm>
                    <label class="md-no-float">Email</label>
                    <md-icon class="md-primary"><i class="material-icons">email</i></md-icon>
                    <input required name="email" ng-model="user.email" />
                    <div ng-messages="loginForm.email.$error">
                        <div ng-message="required">Veillez entrer votre addresse email.</div>
                    </div>
                </md-input-container>

                <md-input-container class="md-block col-md-12">
                    <label class="md-no-float">Mot de passe</label>
                    <md-icon class="md-primary"><i class="material-icons">lock</i></md-icon>
                    <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="user.password" />
                    <div ng-messages="loginForm.password.$error">
                        <div ng-message="required">Veillez entrer un mot de passe.</div>
                    </div>
                </md-input-container>
                
                <div class="col-sm-12">
                    <md-checkbox class="pull-left" ng-model="user.rememberme" aria-label="Rester connecté">
                        Rester connecté
                    </md-checkbox>
                </div>
                
                <div class="col-sm-12">
                  <md-button class="md-raised md-primary pull-right" type="submit">
                      Se connecter
                  </md-button>
                </div>

                
                <p style="text-align: center;"><a href="<?php echo site_url("/account/password_forgotten") ?>">Mot de passe oublié?</a></p>
                <p style="text-align: center;">
                    Vous n'avez pas encore de compte? 
                    <a href="<?php echo site_url("/account/register") ?>" >Créer un compte</a>
                </p>
                
                
          </form>
       </md-content>
      </div>
   </div>
   </form>
</div>
