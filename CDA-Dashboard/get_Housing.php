<?php
$host = "localhost"; 
$user = "root";   
$pass = "";       
$dbname = "cda_dashboard";  

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query joining on Name
$sql = "
    SELECT 
        hs.ID, 
        hs.Name, 
        hs.Directorate, 
        hs.Length, 
        hs.Area, 
        hd.Latitude, 
        hd.Longitude
    FROM housing_scheme hs
    LEFT JOIN housingdata hd ON hs.Name = hd.Name
";

$result = $conn->query($sql);
if (!$result) {
    die("SQL Error: " . $conn->error);
}

// Fetch data and prepare JSON
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "ID"          => $row["ID"],
        "Name"        => $row["Name"],
        "Directorate" => $row["Directorate"],
        "Length"      => $row["Length"],
        "Area"        => $row["Area"],
        "Latitude"    => $row["Latitude"],
        "Longitude"   => $row["Longitude"],
    ];
}

header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
