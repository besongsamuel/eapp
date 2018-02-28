
<link href="<?php echo base_url("assets/css/register.css"); ?>" rel="stylesheet">

<div id="admin-container" class="container mainbox" ng-controller="AccountController"> 
    
    <div>
        <h2 class="section-title md-otiprix-text">Sélectionnez le type de compte</h2>
        <div class="row" style="margin-bottom: 150px; margin-top: 50px;">
            <div class="col-sm-6 col-md-4 col-md-offset-2">

                <a href="<?php echo site_url("account/register/personal"); ?>" class="center-div">
                    <div class="choice-block layout-padding">
                        <img src="<?php echo base_url("assets/img/register/user-512.png");  ?>">
                    </div>
                </a>

                <h3 class="md-otiprix-text" style="text-align: center"> Compte Personel</h3>

            </div>

            <div class="col-sm-6 col-md-4">

                <a href="<?php echo site_url("account/register/company"); ?>" class="center-div">
                    <div class="choice-block layout-padding">
                        <img src="<?php echo base_url("assets/img/register/briefcase-512.png");  ?>">
                    </div>
                </a>

                <h3 class="md-otiprix-text" style="text-align: center"> Compte Entreprise</h3>

            </div>

        </div>
    </div>
    
</div>


