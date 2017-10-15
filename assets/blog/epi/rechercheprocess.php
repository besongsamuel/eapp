<?php

mysql_connect("localhost","perfex","Password01$") or die("could not connect");
mysql_select_db("script") or die("could not find db!");
$output = '';
$nom_produit = '';
	
	
//collect

if(isset($_POST['produit']) && ($_POST['produit']!='')){
	$produitq = $_POST['produit'];
	$produitq = preg_replace("#[^0-9a-z]#i","",$produitq);
	$query = mysql_query("SELECT * FROM produit WHERE nom_produit LIKE '%$produitq%' ORDER BY prix_produit ASC") or die("could not search"); //ASC plus petit au plus grand || DESC plus grand au plus petit
	$count = mysql_num_rows($query);
	if($count == 0){
		$outpout = "il y pas de resultat";
	}
	else{
		$nom_produit = $produitq;
		while($row = mysql_fetch_array($query)){
			$pepicerie = $row['epicerie'];
			$pprix = $row['prix_produit'];
			$output .= '<div>'.$pepicerie.' '.$pprix. "$".'</div>';
		}

	}
}
?>