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
        const EMPTY_IMAGE = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
        
        ctrl.removeImage = function()
        {
            ctrl.image = EMPTY_IMAGE;
            
            $scope.hasImage = false;
            
            ctrl.onFileRemoved({});
        };
        
        ctrl.$onChanges = function(changesObj)
        {
            if(!angular.isNullOrUndefined(changesObj.image.currentValue))
            {
                ctrl.image = changesObj.image.currentValue;
                $scope.hasImage = ctrl.image != EMPTY_IMAGE && ctrl.image != null;
            }
        };
        
        ctrl.guidGenerator = function() 
        {
            var S4 = function() 
            {
               return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
            };
            return (S4()+S4());
        };
        
        $scope.inputButtonId = ctrl.guidGenerator();
        
        $scope.uploadButtonId = ctrl.guidGenerator();
        
        
        ctrl.$onInit = function()
        {
            
            angular.element(document).ready(function()
            {
                $scope.hasImage = ctrl.image != EMPTY_IMAGE && ctrl.image != null;
            
                // The hidden input button
                var inputButton = $("#" + $scope.inputButtonId);

                // The button used to upload a new image. 
                // It triggers the click on the hidden file input
                var uploadButton = $("#" + $scope.uploadButtonId);

                // Whenever the selected file is changed, this is called. 
                inputButton.on('change', function()
                {
                    readURL(this);
                });

                // Whenever the upload button is clicked.
                uploadButton.on('click', function() 
                {
                   inputButton.click();
                });
            });
        };
        
        var readURL = function(input) 
        {
            if (input.files && input.files[0]) 
            {
                var reader = new FileReader();

                reader.onload = function (e) 
                {
                    ctrl.image = e.target.result;
                    
                    $scope.hasImage = true;
                
                    $scope.$apply();
                };

                reader.readAsDataURL(input.files[0]);
                
                ctrl.onFileSelected({ file : input.files[0]});
                                
            }
        };
    },
    bindings : 
    {
        caption : '@',
        name : '@',
        image : '<',
        onFileSelected : '&',
        onFileRemoved : '&'
    }
});

