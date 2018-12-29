// Component to Select from user grocery list
angular.module('eappApp').component("topProducts", 
{
    templateUrl : "templates/components/top-products.html",
    controller : function($scope)
    {
        var ctrl = this;
        
        ctrl.guidGenerator = function() 
        {
            var S4 = function() 
            {
               return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
            };
            return (S4()+S4());
        };
        
        ctrl.$onInit = function()
        {
            $scope.products = ctrl.data;
            $scope.caption = ctrl.caption;
            
            if(ctrl.caption2)
            {
                $scope.caption = ctrl.caption2;
            }
            
            ctrl.chartID = ctrl.guidGenerator();
            
            if(angular.isNullOrUndefined(ctrl.countCaption))
            {
                ctrl.countCaption = "Vues: ";
            }
            
            $scope.countCaption = ctrl.countCaption;
            
            window.chartColors = 
            {
                red: 'rgb(255, 99, 132)',
                orange: 'rgb(255, 159, 64)',
                yellow: 'rgb(255, 205, 86)',
                green: 'rgb(75, 192, 192)',
                blue: 'rgb(54, 162, 235)',
                purple: 'rgb(153, 102, 255)',
                grey: 'rgb(231,233,237)'
            };
        };
        
        
        ctrl.getData = function()
        {
            var dataArray = [];
            
            if(!angular.isNullOrUndefined(ctrl.data))
            {
                ctrl.data.forEach(function(dataElement)
                {
                    dataArray.push(dataElement.count);
                });
            }
            
            return dataArray;
        };
        
        ctrl.getDataLabels = function()
        {
            var dataLabelsArray = [];
            
            if(!angular.isNullOrUndefined(ctrl.data))
            {
                ctrl.data.forEach(function(dataElement)
                {
                    dataLabelsArray.push(dataElement.name);
                });
            }
            
            return dataLabelsArray;
        };
        
        angular.element(document).ready(function()
        {
            var canvas = document.getElementById(ctrl.chartID);
            
            if(angular.isNullOrUndefined(canvas))
            {
                return;
            }
            
            var ctx = canvas.getContext('2d');
            
            var config = 
            {
                type : 'pie',
                data : 
                {
                    datasets:[
                    {
                        data: ctrl.getData(),
                        backgroundColor: 
                        [
                            window.chartColors.red,
                            window.chartColors.orange,
                            window.chartColors.yellow,
                            window.chartColors.green,
                            window.chartColors.blue
                        ],
                        label: ctrl.caption
                    }],
                    labels: ctrl.getDataLabels()
                },
                options: 
                {
                    responsive : true
                }
            };
            
            var myPie = new Chart(ctx, config);
            myPie.update();
            
        });
        
        
    },
    bindings : 
    {
        data : '=',
        caption : '@',
        caption2 : '<',
        countCaption : '@'
    }
});