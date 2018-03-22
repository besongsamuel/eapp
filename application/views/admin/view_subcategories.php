
<style>
        
</style>

<script src="<?php echo base_url("assets/js/admin-controller.js")?>"></script>

<link rel="stylesheet" href="<?php echo base_url("assets/css/admin.css")?>">

<md-content class="otiprix-section" ng-controller="ViewSubCategoriesController" ng-cloak>
    
    <div class="layout-padding">
        
        <div class="col-sm-12">
            <md-button data-toggle="collapse" data-target="#create_category" class="md-raised md-primary pull-left">
                Create Category   
            </md-button>
        </div>
        
        <div class="row collapse layout-padding" id="create_category">

            <div class="container">
                
                <form ng-submit="CreateCategory($event)" novalidate>

                    <md-input-container class="col-sm-12">
                        <label>Name</label>
                        <input ng-model="newCategory.name">
                    </md-input-container>

                    <div class="row layout-padding">
                        <lf-ng-md-file-input lf-files="categoryImage" lf-api="api" style="width:100%" preview>
                        </lf-ng-md-file-input>
                    </div>

                    <md-input-container class="col-sm-12">
                        <md-button type="submit" class="md-raised md-primary pull-right">
                            Create    
                        </md-button>
                    </md-input-container>

                </form>
            </div>

        </div>
        
        <div class="col-sm-12">
            <md-button data-toggle="collapse" data-target="#create_subcategory" class="md-raised md-primary pull-left">
                Create Subcategory   
            </md-button>
        </div>
       
        <div id="create_subcategory" class="row collapse layout-padding">

            <div class="container">
                
                <form ng-submit="CreateSubcategory($event)" novalidate>

                    <md-input-container class="col-sm-12">
                        <label>Name</label>
                        <input ng-model="newSubCategory.name">
                    </md-input-container>

                    <md-input-container class="col-sm-12">
                        <label>Category</label>
                        <md-select ng-model="newSubCategory.product_category_id">
                            <md-option ng-value="category.id" ng-repeat="category in categories track by $index">{{ category.name }}</md-option>
                        </md-select>
                    </md-input-container>

                    <md-input-container class="col-sm-12">
                        <md-button type="submit" class="md-raised md-primary pull-right">
                            Create    
                        </md-button>
                    </md-input-container>

                </form>
            </div>

        </div>

        
    </div>
    
    <md-toolbar class="md-primary">
        <h2 class="md-toolbar-tools">
            <span>Sub Categories</span>
        </h2>
    </md-toolbar>
    
    <md-toolbar class="md-table-toolbar md-default" ng-hide="selected.length || filter.show">
        <div class="md-toolbar-tools">
        <div flex></div>
        <md-button class="md-icon-button" ng-click="filter.show = true">
            <md-icon>filter_list</md-icon>
        </md-button>
      </div>
    </md-toolbar>

    <md-toolbar class="md-table-toolbar md-default" ng-show="filter.show && !selected.length">
        <div class="md-toolbar-tools">
            <md-icon>search</md-icon>
            <form flex name="filter.form">
                <input type="text" ng-model="query.filter" ng-model-options="filter.options" placeholder="search">
            </form>
            <md-button class="md-icon-button" ng-click="removeFilter()">
                <md-icon>close</md-icon>
            </md-button>
        </div>
    </md-toolbar>
    
    <md-table-container class="container">
        <table  md-table ng-model="selected" md-progress="promise">
            <thead md-head>
              <tr md-row>
                  <th md-column><span>Subcategory Name</span></th>
                <th md-column><span>Category</span></th>
                <th md-column>Actions</th>
              </tr>
            </thead>
            
            <tbody md-body>
                <tr class="layout-padding" md-row md-select="selectedSubcategory" md-select-id="id" ng-repeat="subcategory in subCategories track by $index">

                    <td md-cell>
                    
                        <md-input-container class="col-sm-12">
                            <label>Sub Category ID {{subcategory.id}}</label>
                            
                            <input ng-model="subcategory.name" >
                        </md-input-container>
                        
                    </td>

                    <td md-cell>
                        
                        <md-input-container>
                            <label>Category</label>
                            <md-select ng-model="subcategory.product_category_id">
                                <md-option ng-value="category.id" ng-repeat="category in categories track by $index">{{ category.name }}</md-option>
                            </md-select>
                        </md-input-container>

                    </td>

                    <td md-cell>

                        <md-button class="md-raised md-primary" ng-click="edit($event, subcategory)">
                            Edit
                        </md-button>
                        <md-button class="md-raised md-warn" ng-click="delete($event, subcategory.id)">
                            Delete
                        </md-button>
                        
                    </td>

                </tr>
            </tbody>
        </table>
    </md-table-container>

    <md-table-pagination md-limit="query.limit" md-limit-options="[20, 40, 100]" md-page="query.page" md-total="{{count}}" md-on-paginate="getSubCategories" md-page-select></md-table-pagination>

    
</md-content>
