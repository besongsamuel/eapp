
angular.module('eappApp').controller('CompanyStatsController', function($scope, $company)
{
    
    var ctrl = this;
    
    $scope.period = 1;
    
    $scope.sort = "desc";
    
    $scope.limit = 5;
    
    ctrl.periodChanged = function()
    {
        $scope.loading = true;
        $company.getStats('desc', $scope.limit, $scope.period, getStatsSuccess);
    };
        
    angular.element(document).ready(function()
    {
        
        (function()
        {
             $scope.company = 
            {
                id : $scope.loggedUser.company.id,
                name : $scope.loggedUser.company.name,
                neq : $scope.loggedUser.company.neq
            };

            $scope.loading = true;

            $company.getStats('desc', $scope.limit, $scope.period, getStatsSuccess);
            
        })();
        
    });
    
    function getStatsSuccess(response)
    {
        $scope.loading = false;

        $scope.stats = response.data;
        
        $scope.productStats = 
        [
            {
                data : $scope.stats.get_top_recurring_products,
                caption : "Les 5 produits qui reviennent le plus souvent en circulaire"
            },
            {
                data : $scope.stats.top_listed_products,
                caption : "Les 5 produits qui reviennent le plus souvent dans la liste d'épicerie des utilisateurs"
            },
            {
                data : $scope.stats.top_viewed_products,
                caption : "Les 5 produits les plus visités par les utilisateurs"
            },
            {
                data : $scope.stats.top_searched_products,
                caption : "Les 5 produits les plus recherchés par les utilisateurs"
            },
            {
                data : $scope.stats.top_product_categories,
                caption : "Les cinq catégories de produits les plus visités par les utilisateurs"
            },
            {
                data : $scope.stats.top_cart_products,
                caption : "Les 5 produits les plus ajoutés au panier par les utilisateurs"
            },
            {
                data : $scope.stats.top_product_brands,
                caption : "Les 5 marques les plus ajoutées au panier par les utilisateurs"
            },
            {
                data : $scope.stats.least_recurring_products,
                caption : "Les 5 produits qui reviennent le moins souvent en circulaire"
            },
            {
                data : $scope.stats.least_listed_products,
                caption : "Les 5 produits qui reviennent le moins souvent dans la liste d'épicerie des utilisateurs"
            },
            {
                data : $scope.stats.least_viewed_products,
                caption : "Les 5 produits les moins visités par les utilisateurs"
            },
            {
                data : $scope.stats.least_searched_products,
                caption : "Les 5 produits les moins recherchés par les utilisateurs"
            },
            {
                data : $scope.stats.least_product_categories,
                caption : "La catégorie de produits la moins visitée par les utilisateurs"
            }
            
        ];
        
        
        
    };
});