<div class="container" ng-controller="AccountController">
   <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
      <div class="panel panel-info" >
         <div class="panel-heading">
            <div class="panel-title">Se connecter</div>
            <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Mot de passe oublié?</a></div>
         </div>
         <div style="padding-top:30px" class="panel-body" >
            <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
            <form name="loginform" class="form-horizontal" role="form" novalidate>
                
                <md-input-container class="md-block" flex-gt-sm>
                    <label>Email</label>
                    <md-icon md-svg-src="http://{{base_url}}/assets/icons/ic_person_black_24px.svg"></md-icon>
                    <input md-maxlength="30" required name="email" ng-model="user.email" />
                    <div class="hint" ng-if="showHints">Entrez votre nom d'utilisateur ou votre email</div>
                    <div ng-messages="loginform.email.$error" ng-if="!showHints">
                        <div ng-message="required">Name is required.</div>
                        <div ng-message="md-maxlength">The username has to be less than 30 characters.</div>
                    </div>
                </md-input-container>
                <md-input-container class="md-block" flex-gt-sm>
                    <label>Mot de passe</label>
                    <md-icon md-svg-src="http://{{base_url}}/assets/icons/ic_work_black_24px.svg"></md-icon>
                    <input style="border-left : none; border-right : none;border-top : none;" type="password" required name="password" ng-model="user.password" />
                    <div class="hint" ng-if="showHints">Entrez un mot de passe avec au moins 8 caractères</div>
                        <div ng-messages="loginform.password.$error" ng-if="!showHints">
                        <div ng-message="required">Un mot de passe est requis.</div>
                    </div>
                </md-input-container>
                
               <div class="input-group">
                  <div class="checkbox">
                     <label>
                     <input id="login-remember" type="checkbox" name="remember" value="1"> Rester connecté
                     </label>
                  </div>
               </div>
               <div style="margin-top:10px" class="form-group">
                  <!-- Button -->
                  <div class="col-sm-12 controls">
                     <a id="btn-login" href="#" class="btn btn-success">Se connecter  </a>
                     <a id="btn-fblogin" href="#" class="btn btn-primary">Se connecter avec Facebook</a>
                  </div>
               </div>
               <div class="form-group">
                  <div class="col-md-12 control">
                     <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                        Vous n'avez pas encore de compte? 
                        <a href="#" >
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
