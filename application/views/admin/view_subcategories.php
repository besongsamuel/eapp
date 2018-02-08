
<style>
        
</style>

<script src="<?php echo base_url("assets/js/admin-controller.js")?>"></script>

<link rel="stylesheet" href="<?php echo base_url("assets/css/admin.css")?>">

<md-content class="otiprix-section layout-padding" ng-controller="ViewSubCategoryController" ng-cloak>
    
    <div class="row">
        <md-toolbar class="md-otiprix">
            <h2 class="md-toolbar-tools">
                <span>Add new Subcategory</span>
            </h2>
        </md-toolbar>
        
        <div class="container">
            <form ng-submit="CreateSubcategory($event)" novalidate>
            
                <md-input-container>
                    <label>Name</label>
                    <input ng-model="newSubCategory.name">
                </md-input-container>
                
                <md-input-container>
                    <label>Sub category</label>
                    <md-select ng-model="newSubCategory.product_category_id">
                        <md-option ng-value="category.id" ng-repeat="category in categories track by $index">{{ category.name }}</md-option>
                    </md-select>
                </md-input-container>
                
                <md-input-container>
                    <md-button type="submit" class="md-raised md-otiprix">
                        Add    
                    </md-button>
                </md-input-container>
                
            </form>
        </div>
        
    </div>
    
    <div class="row">
        <md-toolbar class="md-otiprix">
            <h2 class="md-toolbar-tools">
                <span>Add new Category</span>
            </h2>
        </md-toolbar>
        
        <div class="container">
            <form ng-submit="CreateCategory($event)" novalidate>
            
                <md-input-container>
                    <label>Name</label>
                    <input ng-model="newCategory.name">
                </md-input-container>
                
                <div class="row layout-padding">
                    <lf-ng-md-file-input lf-files="categoryImage" lf-api="api" style="width:100%" preview>
                    </lf-ng-md-file-input>
                </div>
                
                <md-input-container>
                    <md-button type="submit" class="md-raised md-otiprix">
                        Add    
                    </md-button>
                </md-input-container>
                
            </form>
        </div>
        
    </div>
    
    
    <md-toolbar class="md-table-toolbar md-default" ng-show="filter.show">
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
    
    
    <md-table-container>
        <table  md-table ng-model="selected" md-progress="promise">
            <thead md-head>
              <tr md-row>
                <th md-column><span>Subcategory Name</span></th>
                <th md-column><span>Category</span></th>
                <th md-column>Actions</th>
              </tr>
            </thead>
            
            <tbody md-body>
                <tr md-row md-select="selectedSubcategory" md-select-id="id" ng-repeat="subcategory in subcategories track by $index">

                    <td md-cell>
                    
                        <md-input-container>
                            <label>Name</label>
                            <input ng-model="subcategory.name" >
                            <span>{{subcategory.id}}</span>
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

                        <md-button class="md-raised md-otiprix" ng-click="edit($event, subcategory)">
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

    <md-table-pagination md-limit="query.limit" md-limit-options="[50, 100, 150]" md-page="query.page" md-total="{{count}}" md-on-paginate="getSubCategories" md-page-select></md-table-pagination>


    
</md-content>
