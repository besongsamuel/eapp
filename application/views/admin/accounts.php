
<md-content class="otiprix-section layout-padding" ng-controller="UserAccountsController" ng-cloak>
    
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-offset-4 col-md-4">
                <md-radio-group ng-model="type" class="text-center">
                    <md-radio-button value="company"> Company Accounts </md-radio-button>
                    <md-radio-button value="user">User Accounts</md-radio-button>
              </md-radio-group>
            </div>
        </div>
        
    </div>
    
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
                        <a href ng-click="validateNEQ(account, $event)" >Valiate NEQ</a>
                    </td>
                    <td ng-if="type == 'company' && account.is_valid == 1">
                        <a href ng-click="revokeNEQ(account, $event)" >Revoke NEQ</a>
                    </td>
                    
                    <td ng-if="type == 'user' && account.is_active == 0">
                        <a href ng-click="activate(account, $event)" >Activate Account</a>
                    </td>
                    <td ng-if="type == 'user' && account.is_active == 1">
                        <a href ng-click="deactivate(account, $event)" >Deactivate account</a>
                    </td>
                    

                </tr>
            </tbody>
        </table>
    </md-table-container>

    <md-table-pagination md-limit="query.limit" md-limit-options="[20, 40, 100]" md-page="query.page" md-total="{{count}}" md-on-paginate="getAccounts" md-page-select></md-table-pagination>


    
</md-content>
