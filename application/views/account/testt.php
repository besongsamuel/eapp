

<div class="container">
   <form action="user-suscrib.php" method="POST">
      <div class="row sect_form_inscr">
         <!-- Formulaire inscripation -->
         <div class="col-xs-12 col-md-12">
            <!-- 12 colonnes pour la zone secondaire -->
            <h2>FORMULAIRE D'INSCRIPTION</h2>
         </div>
         <div class="col-xs-12 col-md-8">
            <!-- 8 colonnes pour la zone principale -->
            <div class="form-group-inline">
               <!-- User -->
               <label for="user_id" class="control-label">Nom d'utisateur</label>
               <input type="text" class="form-control" pattern=".{6,}" required title="6 characters minimum" id="user_id" name="user" placeholder="nom d'utilisateur" required>
               <div class="help-block">Minimum de 6 caracteres</div>
            </div>
            <div class="form-group sect_pwd">
               <label for="inputPassword" class="control-label">Mot de passe</label>
               <div class="form-inline row">
                  <div class="form-group col-sm-6">
                     <input type="password" data-minlength="8" class="form-control" id="inputPassword" placeholder="Mot de passe" required>
                     <div class="help-block">Minimum de 8 caractere</div>
                  </div>
                  <div class="form-group col-sm-6">
                     <input type="password" name="psw"class="form-control" id="inputPasswordConfirm" data-match="#inputPassword" data-match-error="Oups, le mot passe n'est identique" placeholder="Confirmer" required>
                     <div class="help-block with-errors"></div>
                  </div>
               </div>
            </div>
            <div class="form-group-inline sec_quest">
               <!-- Prénom -->
               <label class="mr-sm-2" for="quest_secret">Question secrete</label>
               <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="quest_secret">
                  <option selected>Choisissez une question</option>
                  <option value="1">La destination de votre premier voyage</option>
                  <option value="2">Quel était l'héros de votre enfance</option>
                  <option value="3">Le prénom de votre meilleur ami</option>
                  <option value="4">Le prénom de votre premier amour</option>
                  <option value="5">Le deuxième prenom de votre plus jeune enfant</option>
               </select>
            </div>
            <div class="form-group sec_reponse">
               <label for="repons_quest">Réponse</label>
               <input type="text" class="form-control" id="repons_quest" placeholder="Example input">
            </div>
            <div class="form-group sect_mail">
               <label for="exampleInputEmail1">Email address</label>
               <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
               <small id="emailHelp" class="form-text text-muted">Nous ne partagerons jamais votre email avec qui que ce soit</small>
            </div>
         </div>
      </div>
      <div class="form-group row profil_membre">
         <div class="col-xs-12 col-md-12">
            <!-- 12 colonnes pour la zone secondaire -->
            <h2>PROFIL DU MEMBRE</h2>
         </div>
         <div class="form-group col-sm-8">
            <div class="form-group-inline appellation">
               <!-- Prénom -->
               <label class="mr-sm-2" for="quest_secret">Appellation</label>
               <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="quest_secret">
                  <option selected>----</option>
                  <option value="1">Mme</option>
                  <option value="2">Mr</option>
                  <option value="3">Dr</option>
                  <option value="4">M.</option>
               </select>
            </div>
            <div class="form-group pren_nom">
               <!-- Prénom -->
               <label for="prenom_id" class="control-label">Prénom</label>
               <input type="text" class="form-control" id="prenom_id" name="prenom" placeholder="John">
            </div>
            <div class="form-group pren_nom">
               <!-- Nom -->
               <label for="nom_id" class="control-label">Nom</label>
               <input type="text" class="form-control" id="nom_id" name="prenom" placeholder="Deer">
            </div>
            <div class="form-group adress">
               <!-- Adresse -->
               <label for="adresse" class="control-label">Adresse</label>
               <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Votre adresse">
            </div>
            <div class="form-inline row ville_sect">
               <div class="form-group col-sm-6">
                  <!-- Phone1 -->
                  <label for="ville" class="control-label">Ville</label>
                  <input type="text" class="form-control" id="ville" name="adresse" placeholder="Votre Ville">
               </div>
               <div class="form-group col-sm-6">
                  <!-- Phone 2 -->
                  <label for="code_postal" class="control-label">Code Postal</label>
                  <input type="text" class="form-control" maxlength="6" id="code_postal" name="CP" placeholder="Code postal">
               </div>
            </div>
            <div class="form-inline row contry_sect">
               <div class="form-group col-sm-6">
                  <!-- Province -->
                  <label class="mr-sm-2" for="province">Province</label>
                  <select class="selectpicker" data-live-search="true" id="province">
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
               <div class="form-group col-sm-6">
                  <!-- Pays -->
                  <label for="pays" class="control-label">Pays</label>
                  <input type="text" class="form-control" id="pays" name="prenom" placeholder="Pays">
               </div>
            </div>
            <div class="form-inline row phone_sect">
               <div class="form-group col-sm-6">
                  <!-- Phone1 -->
                  <label for="phone" class="control-label">Téléphone</label>
                  <input type="text" class="input-medium bfh-phone"  id="phone" name="phone" placeholder="000 000 0000">
               </div>
               <div class="form-group col-sm-6">
                  <!-- Phone 2 -->
                  <label for="phone" class="control-label">Téléphone</label>
                  <input type="text" class="form-control bfh-phone"  id="phone" name="phone" placeholder="000 000 0000">
               </div>
            </div>
         </div>
         <div class="form-group col-sm-4">
            <h3>Magasins souvent utilisés</h3>
            <select class="selectpicker" multiple>
               <option>Mustard</option>
               <option>Ketchup</option>
               <option>Relish</option>
            </select>
            <div class="checkbox">
               <label><input type="checkbox" value="">Recevoir l'infolettre</label>
            </div>
            <div class="checkbox">
               <label><input type="checkbox" value="">Recevoir SMS de rappel</label>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-xs-12 col-md-8">
            <button class="btn btn-primary " name="envoyer" type="submit">Envoyer</button>
            <div class="d-inline"> <!-- Lien vers page recovery -->
	    		<p class="pg_connex">
            		<a href="#">Terme et Condition</a>
        		</p>
			</div>
         </div>
      </div>
   </form>
</div>

