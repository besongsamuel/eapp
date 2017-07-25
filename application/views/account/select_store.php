<script>
$(document).ready(function(){
    
    var scope = angular.element($("#admin-container")).scope();
    
    scope.$apply(function()
    {
        scope.retailers = JSON.parse('<?php echo $retailers; ?>');
        
        if(sessionStorage.getItem("registered_email"))
        {
	    scope.registered_email = sessionStorage.getItem("registered_email");
            window.sessionStorage.removeItem("registered_email");
        }
        else
        {
            // redirect to home page
            window.location = "http://" + scope.site_url.concat("/home");
        }
    });
})
</script>

<div id="admin-container" class="container" ng-controller="AccountController">    

        <div id="signupbox" style=" margin-top:50px" class="container">
                    <div class="panel panel-info">
                        
                        <div class="panel-heading">
                            <div class="panel-title">Cochez les 5 magasins que vous utilisez souvent </div>
                        </div>  
                        
                        
                            <form id="signupform" class="form-horizontal panel-body" role="form" novalidate ng-submit="submit_favorite_stores()">
                                <md-content id="retailer-contents" style="overflow : scroll; height: 400px;">
                                
                                <div class="form-group-inline" ng-repeat="store in retailers">
                                    <div class="col-md-2" style="padding-top:25px;">
                                        <label class="btn"  style="background-color : #1abc9c;">
                                            <md-tooltip md-direction="top">{{store.name}}</md-tooltip>
                                            <img  ng-click="select_retailer($event)" id="{{store.id}}" ng-src="http://<?php echo base_url("assets/img/stores/"); ?>{{store.image}}" alt="{{store.name}}" class="img-thumbnail img-check">
                                            <input type="checkbox" name="store_{{store.id}}" value="{{store.id}}" class="hidden" autocomplete="off">
                                        </label>
                                    </div>
                                </div>

                                
                                </md-content>
                                <div class="form-group">
                                    <!-- Button -->                                        
                                    <div class="col-md-offset-0 col-md-3 pull-right" style="padding-top:25px;">
                                        <button id="btn-signup" type="submit" class="btn btn-info col-md-12"><i class="icon-hand-right"></i> &nbsp Terminer l'inscription</button>
                                    </div>
                                </div>
                            </form>
                        
                    </div>
         </div> 
    </div>
    
<style>
.check
{
    opacity:0.5;
    color:#996;
}
</style>
   
        
    
    
