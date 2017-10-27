<script>
$(document).ready(function(){
    
    var scope = angular.element($("#admin-container")).scope();
    
    scope.$apply(function()
    {
       scope.load_icons(); 
       
       if(window.sessionStorage.getItem("accountCreated"))
       {
           scope.accountCreated = window.sessionStorage.getItem("accountCreated");
           window.sessionStorage.removeItem("accountCreated");
       }
    });
})
</script>


<div id="admin-container" class="container loginbox" ng-controller="AccountController" ng-cloak>
   <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
      <div class="panel panel-info" >
         <div class="panel-heading">
            <div class="panel-title">Se connecter</div>
            <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Mot de passe oublié?</a></div>
         </div>
          <div style="padding-top:30px" class="panel-body">
             <div id="login-alert" class="alert alert-danger col-sm-12" ng-show="message">
                 <p>{{message}}</p>
             </div>
             <div class="alert alert-success col-sm-12" ng-show="accountCreated">
                 <p>{{accountCreated}}</p>
             </div>
              <form name="loginForm" class="form-horizontal" role="form" ng-submit="login()" novalidate>
                
                <md-input-container class="md-block col-md-12" flex-gt-sm>
                    <label>Email</label>
                    <md-icon style="color: #1abc9c;"><i class="material-icons">email</i></md-icon>
                    <input required name="email" ng-model="user.email" />
                    <div ng-messages="loginForm.email.$error">
                        <div ng-message="required">Veillez entrer votre addresse email.</div>
                    </div>
                </md-input-container>
                
                <md-input-container class="md-block col-md-12" flex-gt-sm>
                    <label>Mot de passe</label>
                    <md-icon style="color: #1abc9c;"><i class="material-icons">lock</i></md-icon>
                    <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="user.password" />
                    <div ng-messages="loginForm.password.$error">
                        <div ng-message="required">Veillez entrer un mot de passe.</div>
                    </div>
                </md-input-container>
                
               <div class="input-group">
                  <div class="checkbox">
                     <label>
                     <input id="login-remember" type="checkbox" ng-model="user.rememberme" name="remember"> Rester connecté
                     </label>
                  </div>
               </div>
               <div style="margin-top:10px;" class="">
                    <!-- Button -->
                    <div class="" >
                        <!-- Boutton -->                                        
                        <div class="pull-right" style=" margin-top:20px; margin-bottom: 10px;">
                            <button id="btn-signup" type="submit" class="btn"><i class=""></i> &nbsp Se connecter</button>
                        </div>
                    </div>
               </div>
               <div class="form-group">
                  <div class="col-md-12 control">
                     <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                        Vous n'avez pas encore de compte? 
                        <a href="<?php echo site_url("/account/register") ?>" >
                        Créer un compte
                        </a>
                     </div>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
   </form>
</div>
