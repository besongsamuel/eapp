
<link href="<?php echo base_url("assets/css/register.css"); ?>" rel="stylesheet">

<div id="admin-container" class="container mainbox" ng-controller="AccountController"> 
    
    <div>
        
        <h2 class="section-title md-otiprix-text">Sélectionnez le type de compte</h2>
        
        <div layout="row" layout-xs="column" style="margin-bottom: 150px; margin-top: 50px;">
            
            <div  layout="column" layout-align="center center" class="col-sm-6 col-md-6">

                <a href="<?php echo site_url("account/register/personal"); ?>" class="center-div">
                    <div class="choice-block layout-padding">
                        <img src="<?php echo base_url("assets/img/register/user-512.png");  ?>">
                    </div>
                </a>

                <h3 class="md-otiprix-text" style="text-align: center"> Compte Consommateur</h3>

            </div>

            <div  layout="column" layout-align="center center" class="col-sm-6 col-md-6">

                <a href="<?php echo site_url("account/register/company"); ?>">
                    <div class="choice-block layout-padding">
                        <img src="<?php echo base_url("assets/img/register/white-shop-512.png");  ?>">
                    </div>
                </a>

                <h3 class="md-otiprix-text" style="text-align: center"> Compte Entreprise</h3>

            </div>

        </div>
    </div>
    
</div>


