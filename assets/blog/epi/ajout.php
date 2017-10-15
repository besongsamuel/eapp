
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <style>
    label{display:inline-block;width:100px;margin-bottom:10px;}
  </style>
  <title>Ajouter produit</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
</head>
<body>
 
<div class="ui-widget">
    <form method="POST" action="ajout_process.php">
        <center>
            <label>nom produit</label>
            <input type="text" name="nom_produit" />
            <br />
            <label>prix produit</label>
            <input type="text" name="prix_produit" />
            <br />
            <label>epicerie</label>
            <input type="text" name="epicerie" />

            <br />
            <input type="submit" value="Ajouter produit">
        </center>
    </form>
</div>


<?php

?>

</body>
</html>

