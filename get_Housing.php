<?php
$host = "localhost"; 
$user = "root";   
$pass = "";       
$dbname = "cda_dashboard";  

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch data from your new table
$sql = "SELECT `ID`, `Name`, `Directorate`, `Length`, `Area`, `Latitude`, `Longitude` FROM `housing_scheme`"; 
$result = $conn->query($sql);

if (!$result) {
    die("SQL Error: " . $conn->error);
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "ID"         => $row["ID"],
        "Name"       => $row["Name"],
        "Directorate"=> $row["Directorate"],
        "Length"     => $row["Length"],
        "Area"       => $row["Area"],
        "Latitude"   => $row["Latitude"],
        "Longitude"  => $row["Longitude"]
    ];
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($data);

// Close connection
$conn->close();
?>
