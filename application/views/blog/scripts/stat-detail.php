<!DOCTYPE html>

<script src="<?php echo base_url("assets/js/angular-controllers/blog-controller.js")?>"></script>

<script>
	var scope = angular.element($("#blog-container")).scope;
	
	scope.$apply(function()
 	{
		scope.blog = JSON.parse('<?php echo $post; ?>');
	});
</script>
   
