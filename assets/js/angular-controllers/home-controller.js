/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("HomeController", function(appService, $scope, eapp, profileData, $mdDialog, ngIntroService) 
{
    var ctrl = this;
    
    $scope.loadingProducts = true;
    
    Promise.all([appService.ready, profileData.ready]).then(function()
    {
        ctrl.getProducts();
        
    });
    
    ctrl.selectCategory = function(category)
    {
        appService.selectCategory(category);
    };
    
    ctrl.howItWorks = function(ev)
    { 
        if(angular.isNullOrUndefined(window.localStorage.getItem("firstLaunch")) && screen.width > 760)
        {
            $scope.IntroOptions = 
            {
                steps:[
                {
                    element: "#step1",
                    intro: "Utilisez la <strong  class='text-success'>géolocalisation</strong> ou <strong  class='text-success'>Indiquez votre code postal</strong>"
                },
                {
                    element: "#step2",
                    intro: "<strong class='text-success'>Ajoutez des produits</strong></span> à votre liste d’achat à partir de la page d’accueil"
                },
                {
                    element: '#step3',
                    intro: 'Utilisez ce menu pour choisir <ul><li>Les <strong class="text-success">circulaires</strong> des magasins pour voir les épiceries proches de chez vous.</li><li> Les <strong class="text-success">catégories</strong> en cliquant sur Les catégories de produits.</li></ul>'
                },
                {
                    element: '#step4',
                    intro: '<strong class="text-success">Trouver un produit</strong> permet de voir tous les produits disponibles et d\'effectuer une recherche par nom'
                },
                {
                    element: '#step5',
                    intro: '<strong class="text-success">Voir</strong> tous les produits ajoutés à votre liste et demandez à OTIPRIX de rechercher les magasins où ils sont le plus <strong class="text-danger">moins chers</strong>.'
                },
                {
                    element: '#step6',
                    intro: 'Pour gagner du temps, <strong class="text-success">Identifiez-vous</strong> ou <strong class="text-success">créez un compte</strong> pour<ul><li>Enregistrer votre liste d\’épicerie et la soumettre chaque semaine</li><li>Voir votre historique d’économie</li><li>Indiquer les magasins où vous souhaiter faire vos recherches de produits</li><li>Envoyer votre liste par sms ou par mail</li></ul>'
                }
                ],
                showStepNumbers: true,
                showBullets: true,
                exitOnOverlayClick: true,
                exitOnEsc:true,
                nextLabel: '<span style="color:green">Prochain</span>',
                prevLabel: '<span style="color:red">Precedent</span>',
                skipLabel: 'Quitter',
                doneLabel: 'Quitter',
                overlayOpacity : 0.5,
                highlightClass : "half-transparent"
                
            };

            ngIntroService.clear();
            ngIntroService.setOptions($scope.IntroOptions);

            ngIntroService.onComplete(function()
            {
               window.localStorage.setItem("firstLaunch", true);
            });

            ngIntroService.onExit(function()
            {
                window.localStorage.setItem("firstLaunch", true);
            });

            ngIntroService.onBeforeChange(function()
            {
              console.log("[service] before change");
            });

            ngIntroService.onChange(()=>{
              console.log("[service] on change");
            });

            ngIntroService.onAfterChange(()=>{
              console.log("[service] after Change");
            });

            ngIntroService.start();
        }
        
        
        if(angular.isNullOrUndefined(window.localStorage.getItem("firstLaunch")) && screen.width < 760)
        {
            $mdDialog.show({
                controller: function($scope)
                {
                    $scope.logo = appService.baseUrl.concat("/assets/img/logo.png");

                    $scope.close = function()
                    {
                        $mdDialog.hide();
                    };
                },
                templateUrl: 'templates/dialogs/howItWorks.html',
                parent: angular.element(document.body),
                targetEvent: ev,
                clickOutsideToClose:false,
                fullscreen: true
            });
            
            window.localStorage.setItem("firstLaunch", true);
        }
    };
    
    ctrl.getProducts = function()
    {
        eapp.getHomeProducts().then(function(response)
        {
            
            var allCategoryProducts = response.data;
            
            $scope.categoryProducts = allCategoryProducts.slice(0, 3);
            
            $scope.categoryProducts2 = allCategoryProducts.slice(3, allCategoryProducts.length);
            
            $scope.loadingProducts = false;
            
            
            
            setTimeout(function()
            {
                ctrl.howItWorks();
                
                $('.product-carousel').owlCarousel({
                    loop:true,
                    nav:false,
                    autoplay:false,
                    autoplayTimeout: 1000,
                    autoplayHoverPause:true,
                    margin:10,
                    responsiveClass:true,
                    navText : ['Précédent', 'Suivant'],

                    responsive:{
                        0:{
                            items:1
                        },
                        600:{
                            items:2
                        },
                        1000:{
                            items:4
                        }
                    }
                });
                
                
                
            }, 100);
            
            
        });
    };
    
    ctrl.getHomeCategories = function()
    {
        var categoriesPromise = eapp.getCategories(5, 8);
        
        $scope.loading = true;
        
        categoriesPromise.then(function(response)
        {
            $scope.homePageCategories = response.data;
            
            $scope.loading = false;
        });
    };
    
    ctrl.gotoShop = function()
    {
        appService.gotoShop();
    };
    
});

