<?php
header('Content-Type: application/json');

$host = "localhost";
$user = "root";
$password = "";
$dbname = "cda_dashboard";
$table = "d12__1___1_";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Fetch only Schools, Mosques, and Graveyards
$sql = "SELECT * FROM `$table`
        WHERE LOWER(`Type`) LIKE '%school%'
           OR LOWER(`Type`) LIKE '%mosque%'
           OR LOWER(`Type`) LIKE '%graveyard%'";

$result = $conn->query($sql);
$features = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Make sure coordinates are numeric
        $lat = floatval($row['Latitude']);
        $lng = floatval($row['Longitude']);

        $features[] = [
            "type" => "Feature",
            "properties" => $row, // keep all other fields
            "geometry" => [
                "type" => "Point",
                "coordinates" => [$lng, $lat]
            ]
        ];
    }
}

// Return proper GeoJSON
echo json_encode([
    "type" => "FeatureCollection",
    "features" => $features
]);

$conn->close();
?>
