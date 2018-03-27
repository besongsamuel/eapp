
angular.module('eappApp').directive("currentAddress", function()
{
    return {
        template : '<div class="col-sm-12 col-md-12 layout-padding" layout-align="center center"><p style="text-align : center;" ng-hide="isUserLogged">Résultats optimisés pour {{currentAddress}} | <a ng-href="{{changeLocationUrl}}" >Changer</a></p></div>'
    };
});