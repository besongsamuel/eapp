<!DOCTYPE html>

<div class="contact-us">
    <div class="container">
    
    <div class="row">
        <div class="col-md-12" ng-controller="HomeController">
        
            <div class="col-md-offset-6 col-md-6 col-sm-offset-0 col-sm-12 contact-us-form">
                
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

