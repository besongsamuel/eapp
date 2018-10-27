/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

angular.module("eappApp").controller("BlogController", function($scope, $http, $sce, appService) 
{
    
    $scope.blogPostCount = 0;
    
    $scope.page = 1;
    
    $scope.selected_page_value = 1;
    
    $scope.page_list = [];
    
    $scope.maxPageCount = parseInt(parseInt($scope.blogPostCount) / 3);
    
    $scope.getiFrameSrc = function(name)
    {
        return $sce.trustAsResourceUrl("https://www.youtube.com/embed/" + name);
    };
    
    $scope.get_page_list = function()
    {
        var page_list = [];
        
        var remainder = parseInt($scope.blogPostCount) % 3;
        
        $scope.num_pages = parseInt(parseInt($scope.blogPostCount) / 3);
        
        if(remainder > 0)
        {
            $scope.num_pages++;
        }
        
        if($scope.num_pages > 0)
        {
            var current_index = $scope.page;
        
            while(current_index < $scope.num_pages + 1 && page_list.length < 3)
            {
                var item = { value : current_index };
                page_list.push(item);
                current_index++;
            }
        }
        
        $scope.page_list = page_list;
    };
    
    $scope.previousPage = function()
    {
        if(parseInt($scope.selected_page_value) === parseInt($scope.page_list[0].value))
        {
            if(parseInt($scope.page ) === 1)
            return;
            $scope.page = $scope.page - 1;
            $scope.get_page_list();
            $scope.selected_page_value--;
            
            var data = { value : $scope.selected_page_value};
            $scope.gotoPage(data);
            
        }
        else
        {
            $scope.selected_page_value--;
            var data = { value : $scope.selected_page_value};
            $scope.gotoPage(data);
        }
        
        
        
    };
    
    $scope.nextPage = function()
    {
        if(parseInt($scope.selected_page_value) === parseInt($scope.page_list[$scope.page_list.length - 1].value))
        {
            if(parseInt($scope.page) + 2 === $scope.num_pages)
            return;
            $scope.page = $scope.page + 1;
            $scope.get_page_list();
            $scope.selected_page_value++;
            
            var data = { value : $scope.selected_page_value};
            $scope.gotoPage(data);
            
        }
        else
        {
            $scope.selected_page_value++;
            var data = { value : $scope.selected_page_value};
            $scope.gotoPage(data);
        }
        
        
    };
    
    $scope.searchPosts = function(searchText)
    {
        
        var formdata = new FormData();
        formdata.append("filter", searchText);
        formdata.append("type", $scope.postType);

        $http.post( appService.siteUrl.concat("/blog/search_posts"), 
        formdata, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            $scope.otherPosts = response.data.otherPosts;
            
            $scope.otherPosts = $.map(response.data.otherPosts, function(value, index) 
            {
                return [value];
            });
        });
    };
    
    $scope.gotoPage = function(pageItem)
    {
        var page = pageItem.value;
        
        $scope.selected_page_value = page;
        
        var formdata = new FormData();
        formdata.append("offset", ($scope.selected_page_value - 1) * 3);
        formdata.append("type", $scope.postType);

        $http.post( appService.siteUrl.concat("/blog/get_posts"), 
        formdata, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            
            $scope.blogPostCount = parseInt(response.data.blogPostCount);
            
            $scope.recentPosts = $.map(response.data.recentPosts, function(value, index) 
            {
                return [value];
            });
            
            $scope.otherPosts = $.map(response.data.otherPosts, function(value, index) 
            {
                return [value];
            });
            
            $scope.get_page_list();
            
            
        });
        
        
    };
    
    /*
    * This function increments the number of likes for a given post
    * for a given user. 
    */
    $scope.like = function(post_id)
    {
        var formdata = new FormData();
        formdata.append("post_id", post_id);

        $http.post( appService.siteUrl.concat("/blog/like"), 
        formdata, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
                var likes = response.data.likes;
                $scope.recentPosts[post_id].likes = likes;
        });
    };
	
    $scope.dislike = function(post_id)
    {
        var formdata = new FormData();
        formdata.append("post_id", post_id);

        $http.post( appService.siteUrl.concat("/blog/dislike"), 
        formdata, { transformRequest: angular.identity, headers: {'Content-Type': undefined}}).then(
        function(response)
        {
            var likes = response.data.likes;
            $scope.recentPosts[post_id].likes = likes;
        });
    };
	
    $scope.canLike = function(post_id)
    {
        for(var i in $scope.recentPosts[post_id].likes)
        {
            var like = $scope.recentPosts[post_id].likes[i];

            if(parseInt(like.user_account_id) === parseInt(appService.loggedUser.id))
            {
                return false;
            }
        }

        return true;
    };
	
	
});
