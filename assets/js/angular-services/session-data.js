/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


const DEFAULT_PROFILE_DATA = 
{
    gridView : true,
    accountMenuIndex : 1,
    cartView : true,
    optimizedCart : false, 
    searchMyList : false,
    filterSettings : null,
    cartFilterSettings : null,
    optimizationDistance : 4,
    firstLaunch : true,
    viewAll : false // Need to figure out what this means
};

angular.module('eappApp').factory('profileData', function($http, sessionData, appService) 
{
    
    let profileData = DEFAULT_PROFILE_DATA;
    
    var ready = $http.post(getSiteUrl().concat("eapp/get_profile_data/"), null, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
        
    ready.then(function(response)
    {
        if(response.data.profileData)
        {
            profileSettings.instance = response.data.profileData;
        }
        else
        {
            profileSettings.instance = sessionData.get();
        }
    });
    
    function getSiteUrl()
    {
        var siteName = window.location.hostname.toString();
        
        if(siteName == "localhost")
        {
            siteName = siteName.concat("/eapp/");
        }
        
        return location.protocol.concat("//", siteName, "/index.php/");
    };
    
    var profileSettings = 
    {
        ready : ready,
        instance : profileData,
        get : function()
        {
            return this.instance;
        },
        set : function(property, value)
        {
            this.instance[property] = value;
            
            if(appService.isUserLogged)
            {
                var formData = new FormData();
        
                formData.append("value", JSON.stringify(this.instance));
        
                $http.post(getSiteUrl().concat("/eapp/set_profile_data"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
            }
            else
            {
                sessionData.set(property, value);
            }
        },
        reset : function(newValue)
        {
            if(newValue)
            {
                this.instance = newValue;
            }
            else
            {
                this.instance = DEFAULT_PROFILE_DATA;
            }
            
            if(appService.isUserLogged)
            {
                var formData = new FormData();
                
                formData.append("value", JSON.stringify(this.instance));
        
                $http.post(getSiteUrl().concat("/eapp/set_profile_data"), formData, { transformRequest: angular.identity, headers: {'Content-Type': undefined}});
            }
            else
            {
                sessionData.reset(this.instance);
            }
        },
        clear : function()
        {
            if(appService.isUserLogged)
            {
                $http.post(getSiteUrl().concat("eapp/clear_profile_data/"), null);
            }
            else
            {
                sessionData.clear();
            }
        }
    };
    
    return profileSettings;
    
});

angular.module('eappApp').factory('sessionData', function() 
{
    
    var sessionData = DEFAULT_PROFILE_DATA;
    
    const settingString = "session_data";
    
    var localSetting = 
    {
        get : function()
        {
            if(angular.isNullOrUndefined(window.sessionStorage.getItem(settingString)))
            {
                return sessionData;
            }
            else
            {
                sessionData = JSON.parse(window.sessionStorage.getItem(settingString));
                return sessionData;
            }
        },
        set : function(property, value)
        {
            sessionData = this.get();
            sessionData[property] = value;
            window.sessionStorage.setItem(settingString, JSON.stringify(sessionData));
        },
        reset : function(newValue)
        {
            window.sessionStorage.setItem(settingString, JSON.stringify(newValue));
        },
        clear : function()
        {
            window.sessionStorage.removeItem(settingString);
        }
    };
    
    return localSetting;
    
});


