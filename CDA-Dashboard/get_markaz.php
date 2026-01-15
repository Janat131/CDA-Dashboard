<?php
$host = "localhost"; 
$user = "root";   
$pass = "";       
$dbname = "cda_dashboard";  

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT `name`, `address`, `longitude`, `latitude` FROM `markaz_in___1_`"; 
$result = $conn->query($sql);

if (!$result) {
    die("SQL Error: " . $conn->error);
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "name"      => $row["name"],      
        "address"   => $row["address"],   
        "latitude"  => $row["latitude"],
        "longitude" => $row["longitude"]
    ];
}

header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
