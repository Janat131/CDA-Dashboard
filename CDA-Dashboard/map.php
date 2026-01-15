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

    <h2 class="dashboard-heading">Dashboard</h2>
    <a href="dashboard.php">Home</a>
    <a href="sectors.php">Sectors</a>
    <a href="zones.php">Zones</a>
    <a href="map.php">Markaz</a>
    <a href="plots.php">Plots</a>
  </div>

  <div class="main">
    <div class="markaz-select-container">
      <label for="markazSelect">Select Markaz:</label>
      <select id="markazSelect">
        <option value="">-- Select --</option>
      </select>
    </div>

    <div class="search-container">
    <label for="searchType">Select Type:</label>
    <select id="searchType">
      <option value="">-- Select Type --</option>
      <option value="sector">Sector</option>
      <option value="subsector">Subsector</option>
      <option value="street">Street</option>
      <option value="plot">Plot</option>
    </select>

    <input type="text" id="searchInput" placeholder="Enter name or number..." />
    <button id="searchBtn">Search</button>
  </div>

    <div id="areaInfo" style="display:none;">
      <p><strong>Name:</strong> <span id="markazName"></span></p>
      <p><strong>Latitude:</strong> <span id="markazLatitude"></span></p>
      <p><strong>Longitude:</strong> <span id="markazLongitude"></span></p>
      <p><strong>Address:</strong> <span id="markazAddress"></span></p>
    </div>

    <div id="map"></div>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    const select = document.getElementById('markazSelect');
    const markazName = document.getElementById('markazName');
    const markazLat = document.getElementById('markazLatitude');
    const markazLng = document.getElementById('markazLongitude');
    const markazAddress = document.getElementById('markazAddress');
    const areaInfo = document.getElementById('areaInfo');

    const map = L.map('map').setView([33.6844, 73.0479], 7);
    let marker;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18
    }).addTo(map);

    fetch('get_markaz.php') 
      .then(res => res.json())
      .then(data => {
        data.forEach(row => {
          const option = document.createElement('option');
          option.value = JSON.stringify(row); 
          option.textContent = row.name;  
          select.appendChild(option);
        });
      })

    select.addEventListener('change', () => {
      if (!select.value) {
        areaInfo.style.display = 'none';
        if (marker) map.removeLayer(marker);
        return;
      }

      const markaz = JSON.parse(select.value);

      markazName.textContent = markaz.name;
      markazLat.textContent = markaz.latitude;
      markazLng.textContent = markaz.longitude;
      markazAddress.textContent = markaz.address; 
      areaInfo.style.display = 'block';

      const lat = parseFloat(markaz.latitude);
      const lon = parseFloat(markaz.longitude);

      if (marker) map.removeLayer(marker);
      marker = L.marker([lat, lon]).addTo(map);
      marker.bindPopup(markaz.name).openPopup();
      map.setView([lat, lon], 13);
    });
  </script>
</body>
</html>
