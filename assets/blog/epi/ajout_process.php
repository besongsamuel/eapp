
<?php include 'db.php'; ?>
 
<?php
 
// recuperation des variable
$nom_produit=$_POST['nom_produit'];
$prix_produit=$_POST['prix_produit'];
$epicerie=$_POST['epicerie'];

//Requette
 
 
mysqli_query($db,"INSERT INTO produit (nom_produit,prix_produit,epicerie)
		        VALUES ('$nom_produit','$prix_produit','$epicerie')");
				
	if(mysqli_affected_rows($db) > 0){
	    echo "<p>Produit Ajouter</p>";
	    echo "<a href='ajout.php'>Retour</a>";
} else {
	echo "Produit non ajouté<br />";
	echo mysqli_error ($db);
}
?>