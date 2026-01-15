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
    <div class="zones-select-container">
      <label for="zonesSelect">Select Zone:</label>
      <select id="zonesSelect">
        <option value="">-- Select --</option>
      </select>
    </div>

    <div id="areaInfo" style="display:none;">
      <p><strong>Name:</strong> <span id="zoneName"></span></p>
      <p><strong>Latitude:</strong> <span id="zoneLat"></span></p>
      <p><strong>Longitude:</strong> <span id="zoneLng"></span></p>
    </div>

    <div id="map"></div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    const select = document.getElementById('zonesSelect');
    const zoneName = document.getElementById('zoneName');
    const areaInfo = document.getElementById('areaInfo');
    const map = L.map('map').setView([33.6844, 73.0479], 7);
    let marker;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18
    }).addTo(map);


    fetch("get_zone.php")
      .then(res => res.json())
      .then(data => {
        data.forEach(row => {
          let option = document.createElement("option");
          option.value = JSON.stringify(row); 
          option.textContent = row.name; // 👈 make sure your DB returns `name`
          select.appendChild(option);
        });
      });

    select.addEventListener("change", (e) => {
      if (!e.target.value) return;

      const zone = JSON.parse(e.target.value);

      // show values in info box
      zoneName.textContent = zone.name;
      document.getElementById("zoneLat").textContent = zone.latitude;
      document.getElementById("zoneLng").textContent = zone.longitude;
      areaInfo.style.display = "block";

      // add marker
      if (marker) map.removeLayer(marker);
      let lat = parseFloat(zone.latitude);
      let lng = parseFloat(zone.longitude);

      marker = L.marker([lat, lng]).addTo(map);
      marker.bindPopup(zone.name).openPopup();
      map.setView([lat, lng], 13);
    });
  </script>
</body>
</html>
