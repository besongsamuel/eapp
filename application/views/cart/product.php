

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
    

   
