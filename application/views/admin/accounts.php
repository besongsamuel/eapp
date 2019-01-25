
<md-content class="otiprix-section layout-padding" ng-controller="UserAccountsController" ng-cloak>
    
    <md-table-container>
        <table  md-table ng-model="selected" md-progress="promise">
            <thead md-head>
              <tr md-row>
                  <th ng-repeat="header in headers" md-column><span>{{header}}</span></th>
                  <th>Actions</th>
              </tr>
            </thead>
            
            <tbody md-body>
                <tr md-row md-select="product" md-select-id="id" ng-repeat="account in accounts">

                    <td md-cell ng-repeat="data in account">
                        {{data}}
                    </td>
                    
                    <td ng-if="type == 'company' && account.is_valid == 0">
                        <a href >Activate</a>
                    </td>
                    <td ng-if="type == 'company' && account.is_valid == 1">
                        <a href >Deactivate</a>
                    </td>
                    

                </tr>
            </tbody>
        </table>
    </md-table-container>

    <md-table-pagination md-limit="query.limit" md-limit-options="[20, 40, 100]" md-page="query.page" md-total="{{count}}" md-on-paginate="getAccounts" md-page-select></md-table-pagination>


    
</md-content>
