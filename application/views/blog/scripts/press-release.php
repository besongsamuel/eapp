<!DOCTYPE html>

<script src="<?php echo base_url("assets/js/angular-controllers/blog-controller.js")?>"></script>

<script>
    
    $(document).ready(function()
    {
        var scope = angular.element($("#blog-container")).scope();
	scope.$apply(function()
 	{
            scope.baseurl = scope.base_url;
            
            scope.postType = parseInt('<?php echo $post_type; ?>');
            
            scope.gotoPage({ value : 1});
	});
        
    });
	
</script>

