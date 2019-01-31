
<link href="<?php echo base_url("assets/css/register.css"); ?>" rel="stylesheet">

<div class="container mainbox" ng-controller="AccountController"> 
    
        
        <h4 otiprix-text class="text-center">Sélectionnez le type de compte</h4>
        

        <div class="row mt-5">

            <div class="col">

                <div class="container-fluid">
                    <div class="row justify-content-end">
                        <div>
                            <a href="<?php echo site_url("account/register/personal"); ?>">
                                <div class="choice-block layout-padding">
                                    <img src="<?php echo base_url("assets/img/register/user-512.png"); ?>">
                                    <h3  class="text-white text-center">Consommateur</h3>
                                </div>
                            </a>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col justify-content-start">

                <div class="container-fluid">

                    <div class="row justify-content-start">

                        <div class="justify-content-center">
                            <a class="text-center" href="<?php echo site_url("account/register/company"); ?>">
                                <div class="choice-block layout-padding">
                                    <img src="<?php echo base_url("assets/img/register/white-shop-512.png"); ?>">
                                    <h3 class="text-white text-center">Entreprise</h3>
                                </div>
                            </a>


                        </div>

                    </div>

                </div>

            </div>

        </div>
        
    
    
</div>


