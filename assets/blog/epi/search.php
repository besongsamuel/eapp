<?php
$dbHost = 'localhost';
$dbUsername = 'perfex';
$dbPassword = 'Password01$';
$dbName = 'script';
//connect with the database
$db = new mysqli($dbHost,$dbUsername,$dbPassword,$dbName);
if($db->connect_error){
    echo 'Connection Faild: '.$db->connect_error;
    }else{
			//get search term
			$searchTerm = $_GET['term'];
			//get matched data from skills table
			$query = $db->query("SELECT * FROM produit WHERE nom_produit LIKE '%".$searchTerm."%' ORDER BY nom_produit ASC");
			if ($row = $query->fetch_assoc()) {
				$data[] = $row['nom_produit'];
			}
	}
//return json data
echo json_encode($data);
?>