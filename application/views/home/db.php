<?php
/* We first connect to our database */
$connection = mysqli_connect('localhost','root','','eapp');

/* Capture connection error if any */
if (mysqli_connect_errno($connection)) {
        echo "Failed to connect to DataBase: " . mysqli_connect_error();
    }
else {

  /* Declare an array containing our data points */
   $data_points = array();

  /* Usual SQL Queries */
     $query = "SELECT `timeStamp`,`myData2` FROM `myTable`";
     $result = mysqli_query($connection, $query);

     while($row = mysqli_fetch_array($result))
        {        
      /* Push the results in our array */
            $point = array("ts" =>  $row['timeStamp'] ,"d1" =>  $row['myData1']);
            array_push($data_points, $point);
        }

    /* Encode this array in JSON form */
        echo json_encode($data_points, JSON_NUMERIC_CHECK);
        }
    mysqli_close($connection);
?>