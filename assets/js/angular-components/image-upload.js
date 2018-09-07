/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


angular.module('eappApp').component("imageUpload", 
{
    templateUrl : "templates/components/imageUpload.html",
    controller : function($scope)
    {
        var ctrl = this;
        
        
        $scope.removeImage = function()
        {
            ctrl.image = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
            
            $scope.hasImage = false;
            
            ctrl.onFileRemoved({});
        };
        
        ctrl.$onChanges = function(changesObj)
        {
            if(!angular.isNullOrUndefined(changesObj.image.currentValue))
            {
                ctrl.image = changesObj.image.currentValue;
                $scope.hasImage = ctrl.image != 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' && ctrl.image != null;
            }

        };
        
        ctrl.$onInit = function()
        {
            $scope.hasImage = ctrl.image != 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' && ctrl.image != null;
        };
        
        var readURL = function(input) 
        {
            if (input.files && input.files[0]) 
            {
                var reader = new FileReader();

                reader.onload = function (e) 
                {
                    ctrl.image = e.target.result;
                };

                reader.readAsDataURL(input.files[0]);
                
                ctrl.onFileSelected({ file : input.files[0]});
                
                $scope.hasImage = true;
                
                $scope.$apply();
                                
            }
        };
    
        $(".file-upload").on('change', function()
        {
            readURL(this);
        });
    
        $(".upload-button").on('click', function() 
        {
           $(".file-upload").click();
        });
    },
    bindings : 
    {
        caption : '@',
        name : '@',
        id : '@',
        image : '<',
        onFileSelected : '&',
        onFileRemoved : '&'
    }
});

