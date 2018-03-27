/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("HomeController", ["$rootScope", "$scope", "eapp", function($rootScope, $scope, eapp) 
{
    
    angular.element(document).ready(function()
    {
        $rootScope.isHome = true;
    
        $rootScope.hideSearchArea = true;

        $scope.yes = true;
        
        $scope.no = false;
        
        $scope.caption_01 = "Recherchez et ajouter un ou plusieurs produits à votre liste d’épicerie en passant par les circulaires ou les différentes catégories de produits";
        $scope.caption_02 = "En un seul clic, Otiprix recherche les meilleurs prix pour chaque produit de votre liste dans tous les magasins";
        $scope.caption_03 = "Otiprix vous fournit votre liste avec les magasins qui offrent les meilleurs prix. Vous pouvez imprimer cette liste, l’envoyé par SMS ou par courriel.";
        
        var getLatestProductsPromise = eapp.getLatestProducts();

        getLatestProductsPromise.then(function(response)
        {
            var array = $.map(response.data, function(value, index) {
                return [value];
            });

            $scope.latestProducts = array;
        });
    });
    
}]);

