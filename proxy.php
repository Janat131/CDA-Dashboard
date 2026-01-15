<?php
// proxy.php

header('Content-Type: application/json');

// GeoServer WFS URL hardcoded for D-12
$geoserverUrl = "http://localhost:8080/geoserver/D12/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=D12:D-12_GCS_11032024&outputFormat=application/json";

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $geoserverUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // if using https

$response = curl_exec($ch);

if ($response === false) {
    echo json_encode(["error" => curl_error($ch)]);
} else {
    echo $response; // Return GeoServer JSON
}

curl_close($ch);
?>
