<?php
header("Content-Type: application/json");

$host = "localhost";
$user = "root";   
$pass = "";       
$dbname = "cda_dashboard";  

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

$sql = "
SELECT 
    a.`Sector`, 
    a.`Subsector`, 
    a.`Plot`, 
    a.`Type`, 
    a.`Street_No/Road` AS `Street`, 
    a.`Corner_status` AS `CornerStatus`, 
    a.`Size`, 
    a.`Longitude`, 
    a.`Latitude`,
    b.`plot_no` AS `PFMS_Plot_No`,
    b.`street_road` AS `PFMS_Street_Road`,
    b.`size` AS `PFMS_Size`,
    b.`corner_status` AS `PFMS_Corner_Status`,
    b.`allotment_status` AS `PFMS_Allotment_Status`
FROM 
    `d12__1___1_` a
JOIN 
    `pfms_plots_info_sectord12` b
ON 
    a.`Street_No/Road` = b.`street_road`
    AND a.`Size` = b.`size`
    AND a.`Plot` = b.`plot_no`
";

$result = $conn->query($sql);

$locations = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row;
    }
    echo json_encode($locations);
} else {
    echo json_encode(["error" => "Query failed: " . $conn->error]);
}

$conn->close();
?>
