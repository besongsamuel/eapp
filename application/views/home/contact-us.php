<!DOCTYPE html>

    <!-- Bootstrap Core CSS -->

    <link href="<?php echo base_url("assets/css/contact.css"); ?>" rel="stylesheet"> 

    <!-- Custom Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,300,600,400italic,600italic,700,700italic,900' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	
    <div class="contact_check" ng-cloak>
        
        <div class="contact-us">
            <div class="container">
                <div class="row">
                    <div class="col-md-12" ng-controller="ContactUsController">
                        <div class="col-md-12 col-sm-12 contact-us-form">
                            
                            <div class="col-md-4 col-xs-12">
                                <div class="services_vertical horizontal_contact">
                                    
                                    <div class="service_vertical_box">
                                        <div class="wow slideInLeft service-icon color-blue border-radius animated animated animated" style="visibility: visible; animation-name: slideInLeft;">
                                            <i class="fa fa-map-marker"></i>
                                        </div>
                                        <h3>Addresse</h3>
                                        <p>{{officeStreet}}, <br>{{officeCity}}, <br>{{officePostcode}} </p>
                                    </div><!-- end service_vertical_box -->

                                    <div class="service_vertical_box">
                                        <div class="wow slideInLeft service-icon color-purple border-radius animated animated animated" style="visibility: visible; animation-name: slideInLeft;">
                                            <i class="fa fa-envelope-o"></i>
                                        </div>
                                        <h3>E-Mail</h3>
                                        <p>{{officeContact}} </p>
                                    </div><!-- end service_vertical_box -->
                                    
                                </div><!-- end services -->
                            </div>		
                            
                            <div class="col-md-8 col-sm-12">
                                
                                <div class="panel panel-info">
                                
                                    <md-toolbar style="background-color: #1abc9c;">
                                        <div>
                                            <h2 class="md-toolbar-tools">Nous Contacter</h2>
                                        </div>
                                    </md-toolbar>

                                    <md-content class="contactus-box">

                                        <form name="contactusForm" novalidate ng-submit="contactus()">

                                            <div class="alert alert-success" ng-show="message">
                                                    <strong>Succès!</strong> {{message}}.
                                            </div>

                                            <div class="alert alert-danger" ng-show="errorMessage">
                                                    <strong>Erreur!</strong> {{errorMessage}}.
                                            </div>

                                            <div class="col-sm-6">
                                                <md-input-container class="md-block" flex-gt-xs>
                                                    <label>Nom</label>
                                                    <md-icon style="color: #1abc9c;"><i class="material-icons">person</i></md-icon>
                                                    <input name="contactName" ng-model="contact.name" required />
                                                    <div ng-messages="contactusForm.contactName.$error">
                                                            <div ng-message="required">Veillez entrer votre nom.</div>
                                                    </div>
                                                </md-input-container>
                                            </div>

                                            <div class="col-sm-6">
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
                                                    <textarea name="contactComment" ng-model="contact.comment" md-maxlength="100" rows="3" md-select-on-focus required></textarea>
                                                    <div ng-messages="contactusForm.contactComment.$error">
                                                        <div ng-message="required">Veillez entrer un message.</div>
                                                    </div>
                                                </md-input-container>
                                            </div>

                                            <div class="col-sm-12">
                                                <md-button type="submit" class="md-raised md-otiprix pull-right">
                                                    Envoyer
                                                </md-button>
                                            </div>

                                        </form>
                                    </md-content>
                                </div>
                            </div>
                        </div>   
                    </div> 
                </div>

            </div>
        </div>
        
        <br>
        
        <!-- page builer -->
        <div class="map-wrapper">
            <div id="map"></div>
        </div><!-- end map -->
    </div>
	
    <!-- Map Scripts-->
    <script src="<?php echo base_url("assets/js/contactus-controller.js")?>"></script>

	
	