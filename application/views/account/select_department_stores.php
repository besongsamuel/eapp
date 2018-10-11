<link href="<?php echo base_url("assets/css/register.css"); ?>" rel="stylesheet">


<div id="admin-container" class="container mainbox" ng-controller="AccountController" ng-cloak>  

    <div class="row layout-padding">
        <add-department-store department-stores='loggedUser.company.chain.department_stores'></add-department-store>
        
        <div class="col-sm-12">
            <p otiprix-text style="text-align: center">Vous pouvez ajouter ajouter les détailents à n'importe quelle moment à partir de votre compte</p>
        </div>
        
        <div class="col-sm-12">
            <md-button class='md-raised md-primary pull-right' ng-click="finishCompanyRegistration()">
                Términer Inscription
            </md-button>
        </div>
        
    </div>
    
</div>