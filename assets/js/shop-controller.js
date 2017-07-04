angular.module('eappApp').controller('ShopController', ["$scope", "$q", "$http", function ($scope, $q, $http) 
{
    
  var bookmark;
  
  $scope.add_to_cart = function(product_id) 
  {
      $scope.addToCart(product_id);
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
      limit: '5',
      order: 'nameToLower',
      page: 1
  };
  
  function success(products) 
  {
      $scope.products = products;
  }
  
  $scope.getProducts = function () 
  {
      var q = $q.defer();

      $scope.promise = $http.post("http://" + $scope.site_url.concat("/shop/get_store_products"), null, {
          transformRequest: angular.identity,
          headers: {'Content-Type': undefined}
      }).then(function(response)
      {
          var array = $.map(response.data, function(value, index) {
              return [value];
          });
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
    
      $scope.getDesserts();
  });
  
}]);
