angular.module('eappApp').controller('ShopController', ["$scope", "$q", "$http", function ($scope, $q, $http) 
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
        limit: '25',
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
      
      $scope.promise = $http.post("http://" + $scope.site_url.concat("/shop/get_store_products"), formData, {
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
        var element = $event.target;
	var store_id = parseInt(element.id);
	window.sessionStorage.setItem("store_id", store_id);    
	window.location = "http://" + $scope.site_url.concat("/shop");
    };
	
    $scope.select_category = function($event)
    {
        var element = $event.target;
	var category_id = parseInt(element.id);
	window.sessionStorage.setItem("category_id", category_id);    
	window.location = "http://" + $scope.site_url.concat("/shop");
    };
 
  
}]);
