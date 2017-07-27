<script>
$(document).ready(function(){
    
    var scope = angular.element($("#admin-container")).scope();
    
    scope.$apply(function()
    {
        scope.retailers = JSON.parse('<?php echo $retailers; ?>');
    });
})
</script>

<!-- Main Script -->
<script src="http://<?php echo base_url("assets/js/shop-controller.js")?>"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!-- Animate CSS -->
<link rel="stylesheet" href="http://<?php echo base_url("assets/css/shop.css")?>">

<div id="admin-container" class="container" ng-controller="ShopController">    

        <div id="signupbox" style=" margin-top:50px" class="container">
	    <div class="panel panel-info">

		<div class="panel-heading">
		    <div class="panel-title">Sélectionnez le magasin pour afficher le contenu du circulaire </div>
		</div>  
		<md-content id="retailer-contents" style="padding : 10px;">
                    <div class="form-group-inline" ng-repeat="store in retailers">
                        <div class="col-md-2" style="padding-top:25px;">
                            <label class="btn item-block">
                                <md-tooltip md-direction="top">{{store.name}}</md-tooltip>
                                <img  ng-click="select_retailer($event)" id="{{store.id}}" ng-src="http://<?php echo base_url("assets/img/stores/"); ?>{{store.image}}" alt="{{store.name}}" class="img-thumbnail img-check">
                                <input type="checkbox" name="store_{{store.id}}" value="{{store.id}}" class="hidden" autocomplete="off">
                            </label>
                        </div>
                    </div>
                </md-content>
	    </div>
         </div> 
    </div>
    
   
        
    
    
