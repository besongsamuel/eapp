<!DOCTYPE html>

<script src="<?php echo base_url("assets/js/angular-controllers/blog-controller.js")?>"></script>

<script>
    
    $(document).ready(function()
    {
        var scope = angular.element($("#blog-container")).scope();
	scope.$apply(function()
 	{
            scope.post = JSON.parse('<?php echo $post; ?>');
            var otherPosts = JSON.parse('<?php echo $otherPosts; ?>');
            
            scope.otherPosts = $.map(otherPosts, function(value, index) 
            {
                return [value];
            });
            
            $(".single-blog-desc").html(scope.post.article);
            
	});
        
    });
	
</script>

  
