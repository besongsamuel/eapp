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
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!-- Animate CSS -->
<link rel="stylesheet" href="<?php echo base_url("assets/css/shop.css")?>">


<div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Categories</h2>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Page title area -->

<div id="admin-container" class="container" ng-controller="ShopController">    

      <div id="signupbox" style=" margin-top:50px" class="container">
        <div class="panel panel-info">
            
            <md-toolbar style="background-color: #1abc9c;">
                <div>
                    <h2 class="md-toolbar-tools">Sélectionnez une categorie pour voir son contenu</h2>
                </div>
            </md-toolbar>
           
            <md-content id="retailer-contents" style="padding : 10px;">
                <div class="form-group-inline" ng-repeat="category in categories">
                    
                    <div class="col-md-3" style="padding-top:40px;">
                        <label class="btn item-block">
                            <img  ng-click="select_category($event)" id="{{category.id}}" ng-src="{{category.image}}" alt="{{category.name}}" class="category-block img-check">
                            <input type="checkbox" name="category_{{category.id}}" value="{{category.id}}" class="hidden" autocomplete="off">
                        </label>
                        <b><p style="text-align: center;">{{category.name}}</p></b>
                    </div>
                </div>
            </md-content>
         </div> 
       </div> 
</div>
    
