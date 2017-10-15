<?php
    include('rechpremiumprocess.php');
    
   //session_unset();
    if(!isset($_SESSION["resultat"])){
        $_SESSION["resultat"]='';
    }
    //$_SESSION["resultat"] .= $nom_produit. "</br>". $output . "</br>";
      //$_SESSION["resultat"] .= "<div id=$nom_produit>". $nom_produit. "</br>". $output . "</br>" . "</div>" . "<input type='button' id=$nom_produit value='supprimer'>";
      if($nom_produit !=''){
      $_SESSION["resultat"] .= "<div id=$nom_produit>". $nom_produit. "<br>". $output . "<br></div><input type=\"button\" id=$nom_produit value=\"Supprimer\" onClick=\"removeElement('$nom_produit');\"><br><br>";
      
      }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Rechercher le produit</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

  <script>
  $(function() {
    $( "#produit" ).autocomplete({
      source: 'search.php'
    });
  });

  function removeElement(childDiv){
        document.getElementById(childDiv).remove();
        document.getElementById(childDiv).remove();
     }
  </script>





</head>
<body>
 
<div class="ui-widget">
	<form action="" method="post" >
		    <label for="produit">Produits: </label>
		    <input type="text" name="produit" id="produit">
		    <input  type="submit" name="submit" value="Valider"> 
	</form>
</div>

<?php 

?>


<?php
 
 //echo  $_SESSION["favcolor"] . ".<br>";
if($nom_produit!=''){
echo $_SESSION["resultat"];
}

 //session_destroy(); 
 //session_unset(); 

     //$tableau = array($nom_produit => $output);
    
    //array_push($tableau, $nom_produit,$output);
    //if(isset($tableau)){
    //    print_r($tableau);
    //    }
?>
 

</body>
</html>