<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<link rel="stylesheet" href="<?php echo base_url("assets/css/subscription.css")?>">

<div class="container otiprix-section" layout-margin='30px' ng-controller="SubscriptonController as ctrl">

    <ul class="subacription-ul">
        <li class="bg-purple">
            <button>Basique</button>
        </li>
        <li class="bg-blue">
            <button>Standard</button>
        </li>
        <li class="bg-blue">
            <button>Plus</button>
        </li>
    </ul>  

    <table class="subacription-table">
        <thead>
            <tr>
                <th class="mask"></th>
                <th class="bg-purple">Basique</th>
                <th class="bg-blue">Standard</th>
                <th class="bg-blue">Plus</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Prix par mois</td>
                <td><span class="txt-top">&dollar; CAD</span><span class="txt-l">0</span></td>
                <td><span class="txt-top">&dollar; CAD</span><span class="txt-l">99</span></td>
                <td><span class="txt-top">&dollar; CAD</span><span class="txt-l">199</span></td>
            </tr>
            <tr>
                <td colspan="4" class="sep">Statistiques: Produits plus</td>
            </tr>
            <tr>
                <td>La poportion des produits biologiques ajoutés au panier par les utilisateurs</td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>La catégorie de produits la plus visitée par les utilisateurs</td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Les 5 produits les plus ajoutés au panier par les utilisateurs</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Origine des produits visités par les utilisateurs</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Les 5 produits les plus visités par les utilisateurs</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Les 5 produits qui reviennent le plus souvent en circulaire</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Les 5 produits qui reviennent le plus souvent dans la liste d'épicerie des utilisateurs</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Les 5 produits les plus recherchés par les utilisateurs</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Les 5 marques les plus ajoutés au panier par les utilisateurs</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Origine des produits ajoutés au panier par les utilisateurs</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>

            <tr>
                <td colspan="4" class="sep">Statistiques: Produits moins</td>
            </tr>
            <tr>
                <td>La catégorie de produits la moins visitée par les utilisateurs</td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Les 5 produits qui reviennent le moins souvent en circulaire</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Les 5 produits les moins visités par les utilisateurs</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Les 5 produits les moins ajoutés au panier par les utilisateurs</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Les 5 produits qui reviennent le moins souvent dans la liste d'épicerie des utilisateurs </td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Les 5 produits les moins recherchés par les utilisateurs</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Les 5 marques les moins ajoutées au panier par utilisateurs</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td colspan="4" class="sep">Entreprises</td>
            </tr>
            <tr>
                <td>Combien d'utilisateurs visitent mon magasin</td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Combien de produits le magasin le plus visité met en ligne chaque semaine</td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>À quelle distance se trouvent les utilisateurs qui visitent mon magasin </td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Combien d'utilisateurs m'ont choisi comme magasin préféré</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>À quelle distance se trouvent les utilisateurs qui ajoutent mes produits à leur panier</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Quel est le magasin le plus visité</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
            <tr>
                <td>Quel est le magasin qui a plus de produits dans la liste des produits moins cher</td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tickred">&#x2716;</span></td>
                <td><span class="tick">&#10004;</span></td>
            </tr>
                <tr><td class="mask"></td>
                <td><md-button class='md-raised md-primary md-hue-2' ng-click="ctrl.selectSubscription(1)">Souscrire</md-button></td>
                <td><md-button class='md-raised md-primary' ng-click="ctrl.selectSubscription(2)">Souscrire</md-button></td>
                <td><md-button class='md-raised md-warn' ng-click="ctrl.selectSubscription(3)">Souscrire</md-button></td>
            </tr> 
        </tbody>
    </table>
  
</div>






