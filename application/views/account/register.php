
    <div class="container">    

        <div id="signupbox" style=" margin-top:50px" class="mainbox col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">FORMULAIRE D'INSCRIPTION</div>
                            <div style="float:right; font-size: 85%; position: relative; top:-10px; ">Vous avez un compte!  <a id="signinlink" href="#" onclick="$('#signupbox').hide(); $('#loginbox').show()">Se connecter</a></div>
                        </div>  
                        <div class="panel-body" >
                            <form id="signupform" class="form-horizontal" role="form">
                                
                                <div id="alert_enregist" style="display:none" class="alert alert-danger">
                                    <p>Erreur:</p>
                                    <span></span>
                                </div>
                                
                                <!-- RENSEIGNEMENT -->
                                <div class="form-group">
                                    <label for="prenom" class="col-md-3 control-label">Prenom</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="prenom" name="prenom" placeholder="prenom" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nom" class="col-md-3 control-label">Nom</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="nom" name="prenom" placeholder="Nom" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="adresse" class="col-md-3 control-label">Adresse</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Adresse" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="ville" class="col-md-3 control-label">Ville</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="ville" name="ville" placeholder="Ville" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="code_postal" class="col-md-3 control-label">Code Postal</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" maxlength="7" id="code_postal" name="cp" placeholder="Code postal" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="province" class="col-md-3 control-label">Province</label>
                                    <div class="col-md-9">
                                        <select class=" form-control selectpicker" data-live-search="true" id="province" required>
                                            <option>Alberta</option>
                                            <option>Colombie-Britannique</option>
                                            <option>Île-du-Prince-Édouard</option>
                                            <option>Manitoba </option>
                                            <option>Nouveau-Brunswick </option>
                                            <option>Nouvelle-Écosse </option>
                                            <option>Ontario </option>
                                            <option selected>Québec </option>
                                            <option>Saskatchewan </option>
                                            <option>Terre-Neuve et Labrador </option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="pays" class="col-md-3 control-label">Pays</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="pays" name="pays" placeholder="Pays">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone" class="col-md-3 control-label">Téléphone</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Téléphone" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone_2" class="col-md-3 control-label">Téléphone</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="phone_2" name="phone2" placeholder="Téléphone 2">
                                    </div>
                                </div>
                                
                                <div class="panel-heading">
                                    <div class="panel-title" style="font-weight:600; margin-top:50px">IDENTIFICATION</div>
                                </div>
                                
                                  
                                <div class="form-group">
                                    <label for="email" class="col-md-3 control-label">Email</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Adresse email" required>
                                    </div>
                                </div>
                                    
                                <div class="form-group">
                                    <label for="register_password" class="col-md-3 control-label">Mot de passe</label>
                                    <div class="col-md-9">
                                        <input type="password" id="register_password" data-minlength="8" class="form-control" name="password" placeholder="mot de passe" required>
                                        <div class="help-block">Minimum de 8 caractere</div>
                                    </div>
                                    
                                </div>
                                
                                <div class="form-group">
                                    <label for="confir_password" class="col-md-3 control-label">Confirmer le mot de passe</label>
                                    <div class="col-md-9">
                                        <input type="password" id="confir_password" data-minlength="8" class="form-control" name="psw" placeholder="Entrer le même mot de passe" required>
                                    </div>
                                    <div class="help-block avec_erreur"></div>
                                </div>
                                
                                
                                
                                <div class="form-group">
                                    <label for="quest_secret" class="col-md-3 control-label">Question secrète</label>
                                    <div class="col-md-9">
                                        <select class="form-control" id="quest_secret" required>
                                            <option selected>Choisissez une question</option>
                                            <option value="1">La destination de votre premier voyage</option>
                                            <option value="2">Quel était l'héros de votre enfance</option>
                                            <option value="3">Le prénom de votre meilleur ami</option>
                                            <option value="4">Le prénom de votre premier amour</option>
                                            <option value="5">Le deuxième prenom de votre plus jeune enfant</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="reponse" class="col-md-3 control-label">Réponse</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="reponse" name="reponse" placeholder="Réponse" required>
                                    </div>
                                </div>
                                
                                
                                

                                <div class="form-group" >
                                    <!-- Boutton -->                                        
                                    <div class="col-md-offset-3 col-md-9" style=" margin-top:20px;">
                                        <button id="btn-signup" type="button" class="btn btn-info col-md-12"><i class="icon-hand-right"></i> &nbsp S'enregister</button>
                                        
                                    </div>
                                </div>
                                
                                <div class="form-group" style="border-top: 1px solid #999; padding-top:20px">
                                    <!-- Button -->                                        
                                    <div class="col-md-offset-3 col-md-9">
                                        <button id="fbsignup" type="button" class="btn btn-primary col-md-12"><i class="icon-facebook"></i></i> &nbsp S'enregistrer avec facebook</button>
                                          
                                    </div>
                                </div>
                                
                                <div class="col-md-3  condition" style=" margin-top:20px;"> <!-- Lien vers page Terme -->
	    		                    <p class="pg_connex ">
            		                    <a href="#">Terme et Condition</a>
        		                    </p>
			                    </div>
                                
                            </form>
                         </div>
                    </div>

               
               
                
         </div> 
    </div>
    
