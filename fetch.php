<?php

// include "http://10.96.4.34/TPLGIS/resources/custom/config.php";

$host = "localhost";
$user = "postgres";
$password = "Khaiden666*";
$dbname = "demo";

$con = pg_connect("host=$host dbname=$dbname user=$user password=$password");

if(!$con){
    die("Connection failed.");
}

$request = "";

if(isset($_POST['request'])){
  $request = $_POST['request'];
  $searchTxt = $_POST['searchTxt'];
}

// Fetch all records
if($request == 'liveSearch'){
  $query = "SELECT rep_name, ST_AsGeoJson(geom) as geom from wards_from_2012 where rep_name like '%$searchTxt%'";
  $result = pg_query($con, $query);
  $response = array();
  while ($row = pg_fetch_assoc($result) ){
     $value = $row["rep_name"];
     $geom = $row["geom"];
     $response[] = array(
      "rep_name" => $value,
      "geom" => $geom
     );
  }

  echo json_encode($response);
  die;
}

?>