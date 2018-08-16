/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module('eappApp').factory('sessionData', function() 
{
    
    var sessionData = 
    {
        gridView : false
    };
    
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
        clear : function()
        {
            window.sessionStorage.removeItem(settingString);
        }
    };
    
    return localSetting;
    
});


