
angular.module('eappApp').controller('CompanyStatsController', function($scope, $company, appService)
{
    
    var ctrl = this;
    
    $scope.period = 1;
    
    $scope.fromDate = new Date();
    $scope.fromDate.setDate($scope.fromDate.getDate() - 365);
    $scope.toDate = new Date();
    
    $scope.sort = "desc";
    
    $scope.limit = 5;
    
    ctrl.formatDate = function(date) 
    {
        var day = date.getDate().toString().length === 1 ? "0" + date.getDate().toString() : date.getDate().toString();
        var monthIndex = (date.getMonth() + 1).toString().length === 1 ? "0" + (date.getMonth() + 1).toString() : (date.getMonth() + 1).toString();
        var year = date.getFullYear();

        return year + '-' +  monthIndex + '-' + day;
    };
    
    ctrl.refresh = function()
    {
        $scope.loading = true;
        $company.getStats('desc', $scope.limit, ctrl.formatDate($scope.fromDate), ctrl.formatDate($scope.toDate), getStatsSuccess);
    };
        
    appService.ready.then(function()
    {
        
        (function()
        {
             $scope.company = 
            {
                id : appService.loggedUser.company.id,
                name : appService.loggedUser.company.name
            };

            $scope.loading = true;

            $company.getStats('desc', $scope.limit, ctrl.formatDate($scope.fromDate), ctrl.formatDate($scope.toDate), getStatsSuccess);
            
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
                data : $scope.stats.least_recurring_products,
                caption : "Les 5 produits qui reviennent le moins souvent en circulaire"
            },
            {
                data : $scope.stats.top_listed_products,
                caption : "Les 5 produits qui reviennent le plus souvent dans la liste d'épicerie des utilisateurs"
            },
            {
                data : $scope.stats.least_listed_products,
                caption : "Les 5 produits qui reviennent le moins souvent dans la liste d'épicerie des utilisateurs"
            },
            {
                data : $scope.stats.top_viewed_products,
                caption : "Les 5 produits les plus visités par les utilisateurs"
            },
            {
                data : $scope.stats.least_viewed_products,
                caption : "Les 5 produits les moins visités par les utilisateurs"
            },
            {
                data : $scope.stats.top_searched_products,
                caption : "Les 5 produits les plus recherchés par les utilisateurs"
            },
            {
                data : $scope.stats.least_searched_products,
                caption : "Les 5 produits les moins recherchés par les utilisateurs"
            },
            {
                data : $scope.stats.top_product_categories,
                caption : "Les cinq catégories de produits les plus visités par les utilisateurs"
            },
            {
                data : $scope.stats.least_product_categories,
                caption : "La catégorie de produits la moins visitée par les utilisateurs"
            },
            {
                data : $scope.stats.top_cart_products,
                caption : "Les 5 produits les plus ajoutés au panier par les utilisateurs"
            }
            
        ];
        
        
        
    };
});