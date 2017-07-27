<script>
$(document).ready(function(){
    
    var scope = angular.element($("#admin-container")).scope();
    
    scope.$apply(function()
    {
        scope.categories = JSON.parse('<?php echo $categories; ?>');
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
                <div class="panel-title">Sélectionnez une categorie pour voire son contenu </div>
            </div>  
            <md-content id="retailer-contents" style="padding : 10px;">
                <div class="form-group-inline" ng-repeat="category in categories">
                    <div class="col-md-2" style="padding-top:40px;">
                        <label class="btn item-block">
                            <md-tooltip md-direction="top">{{category.name}}</md-tooltip>
                            <img  ng-click="select_category($event)" id="{{category.id}}" ng-src="http://<?php echo base_url("assets/img/categories/"); ?>{{category.image}}" alt="{{category.name}}" class="img-thumbnail img-check">
                            <input type="checkbox" name="category_{{category.id}}" value="{{category.id}}" class="hidden" autocomplete="off">
                        </label>
                    </div>
                </div>
            </md-content>
         </div> 
       </div> 
</div>
    
