<?php


$host = "localhost";
$user = "postgres";
$password = "postgres";
$dbname = "btl_1";

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
  $query = "SELECT varname_1, ST_AsGeoJson(geom) as geom from gadm41_vnm_1 where varname_1 like '%$searchTxt%'";
  $result = pg_query($con, $query);
  $response = array();
  while ($row = pg_fetch_assoc($result) ){
     $value = $row["varname_1"];
     $geom = $row["geom"];
     $response[] = array(
      "varname_1" => $value,
      "geom" => $geom
     );
  }

  echo json_encode($response);
  die;
}

?>