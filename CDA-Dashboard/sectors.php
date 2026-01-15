<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Capital Development Authority</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="map.css" />
  <style>
    #map { height: 500px; width: 100%; }
    .sidebar { width: 200px; float: left; }
    .main { margin-left: 210px; padding: 10px; }
  </style>
</head>
<body>

  <div class="sidebar">
    <img src="CDALOGO.png" alt="Logo" class="logo">
    <h2>Dashboard</h2>
    <a href="dashboard.php">Home</a>
    <a href="sectors.php">Sectors</a>
    <a href="zones.php">Zones</a>
    <a href="map.php">Markaz</a>
    <a href="plots.php">Plots</a>
  </div>

  <div class="main">
    <div class="sector-select-container">
      <label for="sectorSelect">Select Sector:</label>
      <select id="sectorSelect">
        <option value="">-- Select --</option>
      </select>
    </div>

    <div id="areaInfo" style="display:none;">
      <p><strong>Name:</strong> <span id="sectorName"></span></p>
      <p><strong>Latitude:</strong> <span id="sectorLat"></span></p>
      <p><strong>Longitude:</strong> <span id="sectorLng"></span></p>
    </div>


    <div id="map"></div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    const select = document.getElementById('sectorSelect');
    const sectorName = document.getElementById('sectorName');
    const areaInfo = document.getElementById('areaInfo');
    const map = L.map('map').setView([33.6844, 73.0479], 10);
    let marker;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18
    }).addTo(map);
      L.tileLayer.wms("http://localhost:8080/geoserver/demo/wms", {
      layers: 'demo:streets',
      format: 'image/png',
      transparent: true
    }).addTo(map);
    fetch("get_sector.php")
      .then(res => res.json())
      .then(data => {
        console.log(data);
        data.forEach(row => {
          let option = document.createElement("option");
          option.value = JSON.stringify(row); 
          option.textContent = row.name; 
          select.appendChild(option);
        });
      });

    select.addEventListener("change", (e) => {
  if (!e.target.value) return;

  const sector = JSON.parse(e.target.value);

  sectorName.textContent = sector.name;
  document.getElementById("sectorLat").textContent = sector.latitude;
  document.getElementById("sectorLng").textContent = sector.longitude;
  areaInfo.style.display = "block";

  if (marker) map.removeLayer(marker);
  let lat = parseFloat(sector.latitude);
  let lng = parseFloat(sector.longitude);

  marker = L.marker([lat, lng]).addTo(map);
  marker.bindPopup(sector.name).openPopup();
  map.setView([lat, lng], 13);
});

  </script>
</body>
</html>

