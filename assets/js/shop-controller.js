angular.module('eappApp').controller('ShopController', ["$scope", "$q", "$http", "$mdDialog", function ($scope, $q, $http, $mdDialog) 
{
    
    var bookmark;
    
    $scope.assets_dir = "http://" + window.location.hostname + "/eapp/assets/";

    $scope.add_to_cart = function(product_id) 
    {
        $scope.addProductToCart(product_id);
    };
    
    $scope.remove_from_cart = function(product_id)
    {
        $scope.removeItemFromCart(product_id);
    };

    $scope.selected = [];
  
    $scope.filter = 
    {
        options: 
        {
            debounce: 500
        }
    };

    $scope.query = 
    {
        filter: '',
        limit: '50',
        order: 'name',
        page: 1
    };
  
  $scope.getProducts = function () 
  {
    if(typeof $scope.site_url === 'undefined')
    {
        return;
    }
      
      var q = $q.defer();
      
      var formData = new FormData();
      formData.append("page", $scope.query.page);
      formData.append("limit", $scope.query.limit);
      formData.append("filter", $scope.query.filter);
      formData.append("order", $scope.query.order);
      
      if(typeof $scope.store_id !== 'undefined')
      {
          formData.append("store_id", $scope.store_id);
      }
      if(typeof $scope.category_id !== 'undefined')
      {
          formData.append("category_id", $scope.category_id);
      }	  
      
      $scope.promise = $http.post( $scope.site_url.concat("/shop/get_store_products"), formData, {
          transformRequest: angular.identity,
          headers: {'Content-Type': undefined}
      }).then(function(response)
      {
          var array = $.map(response.data.products, function(value, index) {
              return [value];
          });
          
          
          $scope.count = response.data.count;
          $scope.products = array;
          q.resolve( array );

      });
	
      return q.promise;
  };
  
  $scope.removeFilter = function () 
  {
      $scope.filter.show = false;
      $scope.query.filter = '';
    
      if($scope.filter.form.$dirty) 
      {
        $scope.filter.form.$setPristine();
      }
  };
  
  $scope.$watch('query.filter', function (newValue, oldValue) 
  {
      if(!oldValue) 
      {
          bookmark = $scope.query.page;
      }
    
      if(newValue !== oldValue) 
      {
          $scope.query.page = 1;
      }
    
      if(!newValue) 
      {
          $scope.query.page = bookmark;
      }
    
      $scope.getProducts();
  });
  
    
	
    $scope.select_retailer = function($event)
    {
        $scope.clearSessionItems();  
        var element = $event.target;
	var store_id = parseInt(element.id);
	window.sessionStorage.setItem("store_id", store_id);    
	window.location =  $scope.site_url.concat("/shop");
    };
	
    $scope.searchProducts = function(searchText)
    {
        $scope.clearSessionItems();
        window.sessionStorage.setItem("searchText", searchText);
        window.location.href =  $scope.site_url.concat("/shop");
    };
    
    $scope.changeDistance = function(ev)
    {
        $scope.default_distance = $scope.distance;
        
        $mdDialog.show({
            controller: ChangeDistance,
            templateUrl:  $scope.base_url + 'assets/templates/change-distance.html',
            parent: angular.element(document.body),
            targetEvent: ev,
            clickOutsideToClose:true,
            preserveScope:true,
            scope : $scope,
            fullscreen: true //
          })
          .then(function(answer) {
                
          }, function() {
                
          });
    };
    
    function ChangeDistance($scope, $mdDialog, $http) 
    {
        $scope.hide = function() 
        {
            $mdDialog.hide();
        };

        $scope.cancel = function() 
        {
            $mdDialog.cancel();
        };
        
        $scope.change = function()
        {
            $scope.distance = $scope.default_distance;
            $scope.loading = true;
            var formData = new FormData();
            formData.append("distance", $scope.distance);
            formData.append("longitude", $scope.longitude);
            formData.append("latitude", $scope.latitude);
            
            $http.post($scope.site_url.concat("/shop/get_retailers"), formData, {
                transformRequest: angular.identity,
                headers: {'Content-Type': undefined}
            }).then(function(response)
            {
                window.sessionStorage.setItem('distance', $scope.distance);
                $scope.loading = false;
                $scope.retailers = response.data.retailers;
                $mdDialog.hide();
            });
            
        };
    };
 
  
}]);
