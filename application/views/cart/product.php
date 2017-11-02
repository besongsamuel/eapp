<script src="<?php echo base_url("assets/js/product-controller.js")?>"></script>

<script>

$(document).ready(function()
{
    var scope = angular.element($("#admin-container")).scope();
    
    scope.$apply(function()
    {
        scope.storeProduct = JSON.parse('<?php echo $store_product; ?>');
    });
    
    var rootScope = angular.element($("html")).scope();
    rootScope.$apply(function()
    {
        rootScope.menu = "cart";
    });
});

</script>   
    

   
