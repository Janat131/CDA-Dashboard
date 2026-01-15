<?php
header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "cda_dashboard";

$conn = new mysqli($host, $user, $pass, $dbname);
if($conn->connect_error){
    die(json_encode(["error"=>"Database connection failed: ".$conn->connect_error]));
}

// Join tables
$sql = "
SELECT 
    d12_2.Sector,
    d12_2.SS AS Subsector,
    d12_2.Plot,
    d12_2.Classifica,
    d12_2.St_No_name AS Street,
    d12_2.Street_Roa AS StreetAlt,
    d12_2.Dimension,
    d12_2.Size_Sq__Y AS Size,
    d12_2.Sector_Sch,
    d12__1___1_.Type,
    d12__1___1_.GP_code,
    d12__1___1_.`Street_No/Road` AS Street2,
    d12__1___1_.Corner_status,
    d12__1___1_.Longitude,
    d12__1___1_.Latitude
FROM d12_2
LEFT JOIN d12__1___1_
ON d12_2.Plot = d12__1___1_.Plot
AND d12_2.SS = d12__1___1_.Subsector
AND d12_2.Sector = d12__1___1_.Sector
";

$result = $conn->query($sql);

$data = [];
if($result){
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }
}

echo json_encode($data);

$conn->close();
?>
