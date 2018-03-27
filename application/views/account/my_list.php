

<md-content id="admin-container" class="otiprix-section" ng-controller="UserListController">
    
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
    
    <div id="groceryListContainer" class="container" ng-include="'<?php echo base_url(); ?>/assets/templates/user_grocery_list.html'"></div>
    
</md-content>


