<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Housing Schemes Dashboard</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    #map { height: 500px; width: 100%; }
    .sidebar { width: 200px; float: left; }
    .main { margin-left: 210px; padding: 10px; }
    .info-box { margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 6px; }
  </style>
</head>
<body>
    <!--
  <div class="sidebar">
    <img src="housing_logo.png" alt="Logo" class="logo" style="width:100%;">
    <h2>Dashboard</h2>
    <a href="dashboard.php">Home</a>
    <a href="schemes.php">Schemes</a>
    <a href="blocks.php">Blocks</a>
    <a href="plots.php">Plots</a>
  </div>
-->

  <div class="main">
    <div class="scheme-select-container">
      <label for="schemeSelect">Select Scheme:</label>
      <select id="schemeSelect">
        <option value="">-- Select --</option>
      </select>
    </div>

    <div id="schemeInfo" class="info-box" style="display:none;">
      <p><strong>Scheme Name:</strong> <span id="schemeName"></span></p>
      <p><strong>Directorate:</strong> <span id="schemeDirectorate"></span></p>
      <p><strong>Length:</strong> <span id="schemeLength"></span></p>
      <p><strong>Area:</strong> <span id="schemeArea"></span></p>
      <p><strong>Latitude:</strong> <span id="schemeLat"></span></p>
      <p><strong>Longitude:</strong> <span id="schemeLng"></span></p>
    </div>

    <div id="map"></div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    const select = document.getElementById('schemeSelect');
    const schemeName = document.getElementById('schemeName');
    const schemeDirectorate = document.getElementById('schemeDirectorate');
    const schemeLength = document.getElementById('schemeLength');
    const schemeArea = document.getElementById('schemeArea');
    const schemeLat = document.getElementById('schemeLat');
    const schemeLng = document.getElementById('schemeLng');
    const schemeInfo = document.getElementById('schemeInfo');
    let marker;

    // Initialize map
    const map = L.map('map').setView([33.6844, 73.0479], 10);

    // Base map
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18
    }).addTo(map);

    // Example overlay (optional, replace with your WMS layer)
    L.tileLayer.wms("http://localhost:8080/geoserver/Housing/wms", {
      layers: 'Housing:Plots',
      format: 'image/png',
      transparent: true
    }).addTo(map);

    // Fetch Housing Scheme data
    fetch("get_Housing.php")
      .then(res => res.json())
      .then(data => {
        data.forEach(row => {
          const option = document.createElement("option");
          option.value = JSON.stringify(row);
          option.textContent = row.Name;
          select.appendChild(option);
        });
      });

    // On scheme selection
    select.addEventListener("change", (e) => {
      if (!e.target.value) return;

      const scheme = JSON.parse(e.target.value);

      schemeName.textContent = scheme.Name;
      schemeDirectorate.textContent = scheme.Directorate;
      schemeLength.textContent = scheme.Length;
      schemeArea.textContent = scheme.Area;
      schemeLat.textContent = scheme.Latitude;
      schemeLng.textContent = scheme.Longitude;
      schemeInfo.style.display = "block";

      if (marker) map.removeLayer(marker);
      const lat = parseFloat(scheme.Latitude);
      const lng = parseFloat(scheme.Longitude);

      marker = L.marker([lat, lng]).addTo(map);
      marker.bindPopup(scheme.Name).openPopup();
      map.setView([lat, lng], 13);
    });
  </script>

</body>
</html>
