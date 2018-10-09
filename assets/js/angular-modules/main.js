

function convert_to_string_date(date)
{
    return date.getFullYear().toString() + "-" + date.getMonth().toString() + "-" + date.getDate().toString();
}

angular.isNullOrUndefined = function(value)
{
    return angular.isUndefined(value) || value === null || value == "undefined";
};

angular.getSearchParam = function(name, url)
{
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
};

// Define the `eapp Application` module
var eappApp = angular.module('eappApp', ['ngMaterial', 'vsGoogleAutocomplete', 'md.data.table', 'ui.carousel', 'lfNgMdFileInput', 'ngMessages', 'ngSanitize', 'mdCountrySelect', 'ngRoute', 'ngAnimate', 'angularCountryState']);

eappApp.config(function($mdThemingProvider)
{
    $mdThemingProvider.definePalette('otiprixPalette', {
    '50': 'e0f2f1',
    '100': 'b2dfdb',
    '200': '80cbc4',
    '300': '4db6ac',
    '400': '26a69a',
    '500': '00b893',
    '600': '00897b',
    '700': '00796b',
    '800': '00695c',
    '900': '004d40',
    'A100': 'a7ffeb',
    'A200': '64ffda',
    'A400': '1de9b6',
    'A700': '00bfa5',
    'contrastDefaultColor': 'light',    // whether, by default, text (contrast)
                                        // on this palette should be dark or light

    'contrastDarkColors': ['50', '100', //hues which contrast should be 'dark' by default
     '200', '300', '400', 'A100'],
    'contrastLightColors': undefined    // could also specify this if default was 'dark'
  });
  
  $mdThemingProvider.theme('default')
    .primaryPalette('otiprixPalette');
    
});

// configure our routes
eappApp.config(function($routeProvider) 
{
   
});



