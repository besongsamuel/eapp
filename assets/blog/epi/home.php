<?php
    include('rechercheprocess.php');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Rechercher le produit</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#produit" ).autocomplete({
      source: 'search.php'
    });
  });

  function SetCookie(c_name,value,expiredays)
   {
            var exdate=new Date()
            exdate.setDate(exdate.getDate()+expiredays)
            document.cookie=c_name+ "=" +escape(value)+
            ((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
    		location.reload()
        }
  </script>
  

</head>
<body>
 
<div class="ui-widget">
	<form action="#" method="post" >
		    <label for="produit">Produits: </label>
		    <input type="text" name="produit" id="produit">
		    <input  type="submit" name="submit" value="Valider"> 
	</form>
</div>

<?php 

?>


<?php

    print("$nom_produit");
    print("$output");

?>
 
</br>
<a href="ajout.php">Ajouter produit</a>  &emsp; &emsp; &emsp; <a href="recherchepremuim.php">Recherche premuim </a>

</body>
</html>

