<?php
header("Content-Type: application/json");

$host = "localhost"; 
$user = "root";   
$pass = "";       
$dbname = "playgrounds";  

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

$sql = "SELECT `id`, `name`, `longitude`, `latitude` FROM `playgrounds`";
$result = $conn->query($sql);

$locations = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
}

echo json_encode($locations);
$conn->close();
?>
