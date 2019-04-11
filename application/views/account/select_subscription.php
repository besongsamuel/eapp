<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<link rel="stylesheet" href="<?php echo base_url("assets/css/subscription.css")?>">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0-alpha1/jquery.min.js"></script>


<div class="container otiprix-section" layout-margin='30px' ng-controller="SubscriptonController as ctrl">

<ul>
  <li class="bg-green active">
    <button>Basique</button>
  </li>
  <li class="bg-yellow">
    <button>Standard</button>
  </li>
  <li class="bg-red">
    <button>Plus</button>
  </li>
</ul>  

<table>
  <thead>
    <tr>
      <th class="text-center">Forfait</th>
      <th class="bg-green default">Basique</th>
      <th class="bg-yellow">Standard</th>
      <th class="bg-red">Plus</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Prix par mois</td>
      <td class="default"><span class="txt-top">&dollar; CAD</span><span class="txt-l">0</span></td>
      <td><span class="txt-top">&dollar; CAD</span><span class="txt-l">10</span></td>
      <td><span class="txt-top">&dollar; CAD</span><span class="txt-l">20</span></td>
    </tr>
	
	<tr>
      <td colspan="4" class="sep">Produits</td>
    </tr>
	<tr>
      <td>Nombre de produits autorisés</td>
      <td class="default">30</td>
      <td>100</td>
      <td>Illimité</td>
    </tr>
	
	<tr>
      <td colspan="4" class="sep">Succursale</td>
    </tr>
	<tr>
      <td>Nombre de succursales autorisées</td>
      <td class="default">1</td>
      <td>5</td>
      <td>Illimité</td>
    </tr>
	
    <tr>
      <td colspan="4" class="sep">Statistiques sur les produits (+)</td>
    </tr>
	<tr>
      <td>La poportion des produits biologiques ajoutés au panier par les utilisateurs</td>
      <td class="default"><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
	<tr>
      <td>Les 5 catégories de produits les plus visités par les utilisateurs</td>
      <td class="default"><span class="tick">&#10004;</span></td>
	  <td><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
	<tr>
      <td>Les 5 produits les plus ajoutés au panier par les utilisateurs</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
	<tr>
      <td>La poportion des produits biologiques visités par les utilisateurs</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
	<tr>
      <td>Les 5 produits les plus visités par les utilisateurs</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
    <tr>
      <td>Les 5 produits qui reviennent le plus souvent en circulaire</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
    <tr>
      <td>Les 5 produits qui reviennent le plus souvent dans la liste d'épicerie des utilisateurs</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
    <tr>
      <td>Les 5 produits les plus recherchés par les utilisateurs</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
    <tr>
      <td>Les 5 marques les plus ajoutés au panier par les utilisateurs</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
    <tr>
      <td>Origine des produits ajoutés au panier par les utilisateurs</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
	
    <tr>
      <td colspan="4" class="sep">Statistiques sur les produits (-)</td>
    </tr>
	<tr>
      <td>La catégorie de produits la moins visitée par les utilisateurs</td>
      <td class="default"><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
    <tr>
      <td>Les 5 produits qui reviennent le moins souvent en circulaire</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
	<tr>
      <td>Les 5 produits les moins visités par les utilisateurs</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
	<tr>
      <td>Les 5 produits les moins ajoutés au panier par les utilisateurs</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
    <tr>
      <td>Les 5 produits qui reviennent le moins souvent dans la liste d'épicerie des utilisateurs </td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
	<tr>
      <td>Les 5 produits les moins recherchés par les utilisateurs</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
	<tr>
      <td>Les 5 marques les moins ajoutées au panier par utilisateurs</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
	<tr>
      <td colspan="4" class="sep">Statistiques des magasins</td>
    </tr>
	<tr>
      <td>Combien de produits le magasin le plus visité met en ligne chaque semaine</td>
      <td class="default"><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
	<tr>
      <td>Quel est le magasin le plus visité</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
    <tr>
      <td>Quel est le magasin qui a plus de produits dans la liste des produits moins cher</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
	<tr>
      <td colspan="4" class="sep">Statistiques de mon épicerie</td>
    </tr>
	<tr>
      <td>Combien d'utilisateurs visitent mon magasin</td>
      <td class="default"><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
	<tr>
      <td>À quelle distance se trouvent les utilisateurs qui visitent mon magasin</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
    <tr>
      <td>Combien d'utilisateurs m'ont choisi comme magasin préféré</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
    <tr>
      <td>À quelle distance se trouvent les utilisateurs qui ajoutent mes produits à leur panier</td>
      <td class="default"><span class="tickred">&#x2716;</span></td>
      <td><span class="tickred">&#x2716;</span></td>
      <td><span class="tick">&#10004;</span></td>
    </tr>
            <tr><td class="first"></td>
                <td class="default"><md-button class='first md-raised md-primary md-hue-2' ng-click="ctrl.selectSubscription(1)">Souscrire</md-button></td>
                <td>
                    Totale de la facture pour un an : 120$
                    <div id="dropin-container"></div>
                    <md-button id="submit-button" class='btn second md-raised md-primary'>Souscrire</md-button>
                </td>
                <td>
                    Totale de la facture pour un an : 240$
                    <div id="dropin-container2"></div>
                    <md-button id="submit-button2" class='btn third md-raised md-primary'>Souscrire</md-button>
                </td>
            </tr> 
        </tbody>
    </table>
  <script src="<?php echo base_url("assets/js/angular-components/select-subscription.js")?>"></script>
</div>






