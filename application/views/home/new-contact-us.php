<!DOCTYPE html>

<html lang="en">
	
	<body>

	</br></br>
		<!-- page builer -->
		<div class="map-wrapper">
			<div id="map"></div>
		</div><!-- end map -->
		

		<div class="contact-us">
			<div class="container">
			
				<div class="row">
					<div class="col-md-12" ng-controller="HomeController">
					
						<div class="col-md-12 col-sm-12 contact-us-form">

								<div class="col-md-4 col-xs-12">
								
									<div class="services_vertical horizontal_contact">
									<div class="service_vertical_box">
										<div class="wow slideInLeft service-icon color-blue border-radius animated animated animated" style="visibility: visible; animation-name: slideInLeft;">
											<i class="fa fa-map-marker"></i>
										</div>
										<h3>Addresse</h3>
										<p>550 Avenue Saint-Dominique, <br>Saint-Hyacinthe, <br>J2S 5M6 </p>
									</div><!-- end service_vertical_box -->

								   <!--
								   <div class="service_vertical_box">
										<div class="wow slideInLeft service-icon color-green border-radius animated animated animated" style="visibility: visible; animation-name: slideInLeft;">
											<i class="fa fa-phone"></i>
										</div>
										<h3>Phone Number</h3>
										<p>Phone: +1 (581) 984 9900</p>
									</div> 
									-->
									<!-- end service_vertical_box -->
									

									<div class="service_vertical_box">
										<div class="wow slideInLeft service-icon color-purple border-radius animated animated animated" style="visibility: visible; animation-name: slideInLeft;">
											<i class="fa fa-envelope-o"></i>
										</div>
										<h3>E-Mail</h3>
										 <p>infos@otiprix.com </p>
									</div><!-- end service_vertical_box -->
									</div><!-- end services -->
									
								</div>		
								
								<div class="col-md-8 col-xs-12">
									
									<form name="contactusForm" novalidate ng-submit="contactus()">
										
										<div class="alert alert-success" ng-show="message">
											<strong>Succès!</strong> {{message}}.
										</div>

										<div class="alert alert-danger" ng-show="errorMessage">
											<strong>Erreur!</strong> {{errorMessage}}.
										</div>
										
										<div class="col-sm-12">
											<md-input-container class="md-block" flex-gt-xs>
												<label>Nom</label>
												<md-icon style="color: #1abc9c;"><i class="material-icons">person</i></md-icon>
												<input name="contactName" ng-model="contact.name" required />
												<div ng-messages="contactusForm.contactName.$error">
													<div ng-message="required">Veillez entrer votre nom.</div>
												</div>
												
											</md-input-container>
											
										</div>

										<div class="col-sm-12">
											<md-input-container class="md-block" flex-gt-xs>
												<label>Email</label>
												<md-icon style="color: #1abc9c;"><i class="material-icons">email</i></md-icon>
												<input style="border-left: none; border-top: none; border-right: none;" type="email" name="contactEmail" ng-model="contact.email" required />
												<div ng-messages="contactusForm.contactEmail.$error">
													<div ng-message="required">Veillez entrer votre address email.</div>
													<div ng-message="email">Entrez un email valide.</div>
												</div>
											</md-input-container>
										</div>

										<div class="col-sm-12">
											<md-input-container class="md-block" flex-gt-xs>
												<label>Sujet</label>
												<md-icon style="color: #1abc9c;"><i class="material-icons">subject</i></md-icon>
												<input name="contactSubject" ng-model="contact.subject" required />
												<div ng-messages="contactusForm.contactSubject.$error">
													<div ng-message="required">Veillez entrer un sujet.</div>
												</div>
											</md-input-container>
											
										</div>

										<div class="col-sm-12">
											<md-input-container class="md-block" flex-gt-xs>
												<label>Commentaires</label>
												<md-icon style="color: #1abc9c;"><i class="material-icons">comment</i></md-icon>
												<textarea name="contactComment" ng-model="contact.comment" md-maxlength="100" rows="5" md-select-on-focus required></textarea>
												<div ng-messages="contactusForm.contactComment.$error">
													<div ng-message="required">Veillez entrer un message.</div>
												</div>
											</md-input-container>
										</div>
										
										<div class="col-sm-12">
											<input type="submit" class="btn btn-primary" class="pull-right" value="Envoyer" />
										</div>
										
									</form>
								
								</div>
								
						</div>   
					
					</div> 
				</div>
			
			</div>
		</div>
	
	</body>
	
</html>
