<script>
    $(document).ready(function()
    {
        var scope = angular.element($("#admin-container")).scope();
    
        scope.$apply(function()
        {
           scope.load_icons(); 
           
           scope.getUserProductList();
        });
    });
</script>

<div class="product-big-title-area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="product-bit-title text-center">
                    <h2>Ma liste d’épicerie</h2>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Page title area -->

<div id="admin-container" class="container" ng-controller="AccountController">
    
    <div id="groceryListContainer" ng-include="'<?php echo base_url(); ?>/assets/templates/user_grocery_list.html'"></div>
    
</div>


