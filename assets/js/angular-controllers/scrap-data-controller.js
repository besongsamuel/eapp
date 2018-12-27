angular.module('eappApp').controller('ScrapDataController', ["$scope", "appService", "eapp", function ($scope, appService, eapp) 
{
    
    var ctrl = this;
    
    ctrl.scrapMetro = function()
    {
        var metroUrl = "https://www.metro.ca/epicerie-en-ligne/recherche?sortOrder=popularity&filter=%3Apopularity%3Adeal%3ACirculaire+et+promotions";
        
        var response = $.get(metroUrl, function(response)
        {
            
        });
    };
    
  
  
}]);
