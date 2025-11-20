<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CDA Plots Map</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

<style>
/* === Reset & Base === */
* { box-sizing: border-box; margin:0; padding:0; font-family: Arial, sans-serif; }
html, body { height:100%; width:100%; }

/* Sidebar (hidden by default) */
#searchSidebar {
  position: absolute;
  top: 0;
  left: -250px; /* hidden initially */
  width: 250px;
  height: 100%;
  background: #fff;
  z-index: 1000;
  box-shadow: 2px 0 8px rgba(0,0,0,0.2);
  padding: 15px;
  transition: left 0.3s ease;
  overflow-y: auto;
}

/* When visible */
#searchSidebar.show {
  left: 0;
}

/* Toggle button */
#toggleSidebarBtn {
  position: absolute;
  top: 15px;
  left: 15px;
  z-index: 1100;
  background: #198754;
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 6px 10px;
  cursor: pointer;
  font-weight: 600;
}


/* === Map === */
#map {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 0;
}


/* === Toolbar Buttons === */
.toolbar {
  position: absolute;
  top: 60px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1000;
  display: flex;
  gap: 10px;
  background: rgba(255,255,255,0.95);
  padding: 8px 12px;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
.toolbar button {
  padding: 8px 14px;
  border-radius: 6px;
  background-color: #198754;
  color: white;
  border: none;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  box-shadow: 0 1px 4px rgba(0,0,0,0.2);
  transition: all 0.2s ease-in-out;
}
.toolbar button:hover {
  background-color: #157347;
  transform: scale(1.05);
}

.opacity-toolbar {
  position:absolute;
  top:80px;
  right:10px;
  z-index:1000;
  background: rgba(255,255,255,0.95);
  border-radius:10px;
  padding:12px 16px;
  box-shadow:0 2px 8px rgba(0,0,0,0.2);
  font-size:13px;
  color:#000;
  max-width:220px;   /* increased to fit label + slider */
}

.opacity-toolbar h3 { margin-bottom:6px; font-size:14px; font-weight:700; }

.opacity-toolbar div { 
  margin-bottom:8px; 
  display:flex; 
  align-items:center; 
  gap:6px; 
  flex-wrap:wrap;  /* allows label + slider to go to next line if needed */
}

.opacity-toolbar label { 
  flex:0 0 100px; /* label width fixed, won't shrink */
  font-weight:600; 
  color:black;     /* text in black */
  font-size:13px; 
}

.opacity-toolbar input[type="range"] {
  -webkit-appearance:none;
  flex:1;        /* slider fills remaining space */
  height:6px;
  background: linear-gradient(90deg,#2196F3,#64B5F6);  /* blue slider */
  border-radius:4px;
  outline:none;
}

.opacity-toolbar input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance:none;
  height:16px; width:16px; background:#1976D2;  /* darker blue thumb */
  border:2px solid #0D47A1; border-radius:3px; cursor:pointer;
  transition: all 0.3s ease;
}

.opacity-toolbar input[type="range"]::-webkit-slider-thumb:hover {
  transform:scale(1.2); 
  background:#0D47A1;
}


/* Hide markaz container */
.markaz-select-container { display:none !important; }
</style>
</head>
<body>
  

<!-- Search Toolbar -->
<!-- Toggle button -->
<button id="toggleSidebarBtn">🔍 Search</button>

<!-- Sidebar -->
<div id="searchSidebar">
  <h3>Search Plots</h3>
  <div class="search-box">
    <label>Sector:</label>
    <input list="sectorsList" id="searchSector" placeholder="Enter sector">
    <datalist id="sectorsList"></datalist>
  </div>
  <div class="search-box">
    <label>Subsector:</label>
    <input list="subsectorsList" id="searchSubsector" placeholder="Enter subsector">
    <datalist id="subsectorsList"></datalist>
  </div>
  <div class="search-box">
    <label>Plot:</label>
    <input list="plotsList" id="searchPlot" placeholder="Enter plot">
    <datalist id="plotsList"></datalist>
  </div>
  <div class="search-box">
    <label>Street:</label>
    <input list="streetsList" id="searchStreet" placeholder="Enter street">
    <datalist id="streetsList"></datalist>
  </div>
  <button onclick="searchAll()">Search</button>
  <button onclick="toggleSidebar()">Close</button>
</div>


<!-- Map -->
<div id="map"></div>

<!-- Toolbar Buttons -->
<div class="toolbar">
  <button id="identifyBtn">📍</button>
  <button id="measureBtn">📏</button>
  <button id="areaBtn">📐</button>
  <button id="clearMeasure">🧹</button>
  <button id="downloadMapBtn">📄</button>
  <button id="searchCoordBtn">🗺️</button> <!-- New button -->
</div>


<!-- Combined Panel for Base & Overlay Layers -->
<div class="opacity-toolbar">
  <h3>Base Maps</h3>
  <div>
    <select id="baseMapSelect" style="width:100%; padding:4px 6px; border-radius:4px; border:1px solid #ccc;">
      <option value="openStreet">OpenStreetMap</option>
      <option value="googleStreets">Google Streets</option>
      <option value="googleSatellite" selected>Google Satellite</option>
      <option value="googleHybrid">Google Hybrid</option>
    </select>
  </div>

 <h3>Overlay Layers</h3>

<div>
  <input type="checkbox" id="boundaryLayerCheckbox" checked>
  <label for="boundaryLayerCheckbox">ICT Boundary</label>
  <input type="range" id="boundaryOpacity" min="0" max="1" step="0.1" value="0.7">
</div>

<div>
  <input type="checkbox" id="zonesLayerCheckbox">
  <label for="zonesLayerCheckbox">Zones</label>
  <input type="range" id="zonesOpacity" min="0" max="1" step="0.1" value="0.7">
</div>

<div>
  <input type="checkbox" id="plotsLayerCheckbox">
  <label for="plotsLayerCheckbox">Plots</label>
  <input type="range" id="plotsOpacity" min="0" max="1" step="0.1" value="0.7">
</div>

<div>
  <input type="checkbox" id="railwayLayerCheckbox">
  <label for="railwayLayerCheckbox">Railway Lines</label>
  <input type="range" id="railwayOpacity" min="0" max="1" step="0.1" value="0.7">
</div>

<div>
  <input type="checkbox" id="roadsLayerCheckbox">
  <label for="roadsLayerCheckbox">Major Roads</label>
  <input type="range" id="roadsOpacity" min="0" max="1" step="0.1" value="0.7">
</div>

<div>
  <input type="checkbox" id="housingSchemesLayerCheckbox">
  <label for="housingSchemesLayerCheckbox">Private Housing Schemes</label>
  <input type="range" id="housingSchemesOpacity" min="0" max="1" step="0.1" value="1">
</div>
<div>
  <input type="checkbox" id="sectorBoundariesLayerCheckbox">
  <label for="sectorBoundariesLayerCheckbox">Sector & Sub-Sector Boundaries</label>
  <input type="range" id="sectorBoundariesOpacity" min="0" max="1" step="0.1" value="0.7">
</div>
<div>
  <input type="checkbox" id="d12TiffCheckbox">
  <label for="d12TiffCheckbox">D-12 Raster (TIFF)</label>
  <input type="range" id="d12TiffOpacity" min="0" max="1" step="0.1" value="1">
</div>



<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
  // Function to calculate polygon area in m²
function polygonArea(latlngs) {
    let area = 0;
    const R = 6378137; // Earth radius in meters
    for (let i = 0, len = latlngs.length; i < len; i++) {
        const p1 = latlngs[i];
        const p2 = latlngs[(i + 1) % len];
        area += (p2.lng - p1.lng) * (2 + Math.sin(p1.lat * Math.PI/180) + Math.sin(p2.lat * Math.PI/180));
    }
    return Math.abs(area * R * R / 2);
}

/* === MAP & BASE LAYERS === */

const openStreet = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' });
const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3'] });
const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3'] });
const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3'] });

const map = L.map('map', {center:[33.6844,73.0479], zoom:12, layers:[googleSatellite]});

/* === OVERLAY LAYERS === */
const boundaryLayer = L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms", { layers:'ISB:ICT_Boundary', format:'image/png', transparent:true }).addTo(map);
const zonesLayer = L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms", { layers:'ISB:ICT_Zones', format:'image/png', transparent:true });
const railwayLinesLayer = L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms", { layers:'ISB:Railway_Line', format:'image/png', transparent:true });
const majorRoadsLayer = L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms", { layers:'ISB:Major_Roads', format:'image/png', transparent:true });
const plotsLayer = L.tileLayer.wms("http://localhost:8080/geoserver/D12/wms", { layers:'D12:D-12_GCS_11032024', format:'image/png', transparent:true });
const housingSchemesLayer = L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms", { layers:'ISB:Private_Housing_Schemes', format:'image/png', transparent:true });
// Sector & Sub-Sector Boundaries Layer
const sectorBoundariesLayer = L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms", {
    layers: "ISB:Sector_and_Sub_Sector_Boundaries",
    format: "image/png",
    transparent: true
});
// D-12 Raster TIFF Layer
const d12TiffLayer = L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms", {
    layers: 'ISB:D-12_tiff',
    format: 'image/png',
    transparent: true
});


/* === CUSTOM PANEL FUNCTIONALITY === */
const baseMapsObj = { googleSatellite, googleStreets, openStreet , googleHybrid };
document.getElementById('baseMapSelect').addEventListener('change', e=>{
  const selected = e.target.value;
  Object.values(baseMapsObj).forEach(layer=>map.removeLayer(layer));
  map.addLayer(baseMapsObj[selected]);
});

// Overlay checkboxes
document.getElementById('boundaryLayerCheckbox').addEventListener('change', e=>e.target.checked?map.addLayer(boundaryLayer):map.removeLayer(boundaryLayer));
document.getElementById('zonesLayerCheckbox').addEventListener('change', e=>e.target.checked?map.addLayer(zonesLayer):map.removeLayer(zonesLayer));
document.getElementById('plotsLayerCheckbox').addEventListener('change', e=>e.target.checked?map.addLayer(plotsLayer):map.removeLayer(plotsLayer));
document.getElementById('railwayLayerCheckbox').addEventListener('change', e=>e.target.checked?map.addLayer(railwayLinesLayer):map.removeLayer(railwayLinesLayer));
document.getElementById('roadsLayerCheckbox').addEventListener('change', e=>e.target.checked?map.addLayer(majorRoadsLayer):map.removeLayer(majorRoadsLayer));
document.getElementById('housingSchemesLayerCheckbox').addEventListener('change', e=>e.target.checked?map.addLayer(housingSchemesLayer):map.removeLayer(housingSchemesLayer));
document.getElementById("sectorBoundariesLayerCheckbox").addEventListener("change", (e) => {
    if (e.target.checked) {
        map.addLayer(sectorBoundariesLayer);
    } else {
        map.removeLayer(sectorBoundariesLayer);
    }
});

// Overlay opacity sliders
document.getElementById("boundaryOpacity").addEventListener("input", e=>boundaryLayer.setOpacity(parseFloat(e.target.value)));
document.getElementById("zonesOpacity").addEventListener("input", e=>zonesLayer.setOpacity(parseFloat(e.target.value)));
document.getElementById("plotsOpacity").addEventListener("input", e=>plotsLayer.setOpacity(parseFloat(e.target.value)));
document.getElementById("railwayOpacity").addEventListener("input", e=>railwayLinesLayer.setOpacity(parseFloat(e.target.value)));
document.getElementById("roadsOpacity").addEventListener("input", e=>majorRoadsLayer.setOpacity(parseFloat(e.target.value)));
document.getElementById("housingSchemesOpacity").addEventListener("input", e=>housingSchemesLayer.setOpacity(parseFloat(e.target.value)));
document.getElementById("sectorBoundariesOpacity").addEventListener("input", (e) => {
    sectorBoundariesLayer.setOpacity(parseFloat(e.target.value));
});
// D-12 TIFF Checkbox Toggle
document.getElementById('d12TiffCheckbox').addEventListener('change', e =>
    e.target.checked ? map.addLayer(d12TiffLayer) : map.removeLayer(d12TiffLayer)
);

// D-12 TIFF Opacity Control
document.getElementById("d12TiffOpacity").addEventListener("input", e =>
    d12TiffLayer.setOpacity(parseFloat(e.target.value))
);

// IDENTIFY TOOL
let identifyActive = false;
document.getElementById('identifyBtn').addEventListener('click', () => {
  identifyActive = !identifyActive;
  alert(identifyActive ? "Click map to identify coordinates." : "Identify tool off.");
});
map.on('click', e => {
  if (identifyActive)
    L.popup()
      .setLatLng(e.latlng)
      .setContent(`<b>Lat:</b>${e.latlng.lat.toFixed(6)}<br><b>Lng:</b>${e.latlng.lng.toFixed(6)}`)
      .openOn(map);
});

// DISTANCE TOOL
let measureActive = false,
  measurePoints = [],
  measureLine;
document.getElementById('measureBtn').addEventListener('click', () => {
  measureActive = !measureActive;
  alert(measureActive ? "Click points to measure distance." : "Distance tool off.");
});
map.on('click', e => {
  if (!measureActive) return;
  measurePoints.push(e.latlng);
  if (measureLine) map.removeLayer(measureLine);
  measureLine = L.polyline(measurePoints, { color: 'red' }).addTo(map);
  if (measurePoints.length > 1) {
    const distance = measurePoints.reduce((t, p, i, a) => (i > 0 ? t + a[i - 1].distanceTo(p) : t), 0);
    L.popup()
      .setLatLng(measurePoints[measurePoints.length - 1])
      .setContent(`<b>Total Distance:</b> ${distance.toFixed(2)} m`)
      .openOn(map);
  }
});

// AREA TOOL
let areaActive = false,
    areaPoints = [],
    areaPolygon = null,
    areaLabel = null;

document.getElementById('areaBtn').addEventListener('click', () => {
    areaActive = !areaActive;
    alert(areaActive ? "Area measurement active." : "Area measurement off.");
    if (!areaActive) {
        areaPoints = [];
        if (areaPolygon) map.removeLayer(areaPolygon);
        if (areaLabel) map.removeLayer(areaLabel);
        areaPolygon = null;
        areaLabel = null;
    }
});

map.on('click', e => {
    if (!areaActive) return;
    areaPoints.push(e.latlng);
    if (areaPolygon) map.removeLayer(areaPolygon);
    if (areaLabel) map.removeLayer(areaLabel);
    areaPolygon = L.polygon(areaPoints, { color: '#1d7b1d', fillColor: '#1d7b1d', fillOpacity: 0.3 }).addTo(map);
    if (areaPoints.length > 2) {
        const latlngs = areaPolygon.getLatLngs()[0];
        const area = polygonArea(latlngs);
        const displayArea = area > 1e6 ? (area / 1e6).toFixed(2) + " km²" : area.toFixed(2) + " m²";
        areaLabel = L.popup({ closeButton: false, autoClose: false, className: 'measurement-popup' })
            .setLatLng(areaPolygon.getBounds().getCenter())
            .setContent(`<b>Total Area:</b> ${displayArea}`)
            .openOn(map);
    }
});

// CLEAR BUTTON
document.getElementById('clearMeasure').addEventListener('click', () => {
  if (measureLine) map.removeLayer(measureLine);
  measurePoints = [];
  map.closePopup();
  if (areaPolygon) map.removeLayer(areaPolygon);
  areaPolygon = null;
  if (areaLabel) map.removeLayer(areaLabel);
  areaLabel = null;
  areaPoints = [];
});

// DOWNLOAD MAP
document.getElementById('downloadMapBtn').addEventListener('click', async () => {
  const mapContainer = document.getElementById('map');
  await new Promise(resolve => {
    const interval = setInterval(() => {
      const tiles = mapContainer.querySelectorAll('img.leaflet-tile');
      if (Array.from(tiles).every(t => t.complete && t.naturalHeight !== 0)) {
        clearInterval(interval);
        resolve();
      }
    }, 500);
  });
  html2canvas(mapContainer, { useCORS: true }).then(canvas => {
    const imgData = canvas.toDataURL('image/png');
    const pdf = new jspdf.jsPDF('landscape', 'mm', 'a4');
    const imgWidth = 290;
    const imgHeight = (canvas.height * imgWidth) / canvas.width;
    pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
    pdf.save('CDA_Map_View.pdf');
  });
});

// SEARCH COORDINATES TOOL
document.getElementById('searchCoordBtn').addEventListener('click', () => {
  let input = prompt("Enter coordinates as 'latitude, longitude' (e.g., 33.6844, 73.0479):");
  if (!input) return;

  let parts = input.split(',').map(s => parseFloat(s.trim()));
  if (parts.length !== 2 || parts.some(isNaN)) {
    alert("Invalid coordinates. Please enter as 'latitude, longitude'.");
    return;
  }

  let lat = parts[0];
  let lng = parts[1];

  // Fly to location
  map.flyTo([lat, lng], 16);

  // Show popup at the location
  L.popup()
    .setLatLng([lat, lng])
    .setContent(`<b>Lat:</b> ${lat.toFixed(6)}<br><b>Lng:</b> ${lng.toFixed(6)}`)
    .openOn(map);
});



let plotsData = []; // global variable to store plot data

// Fetch once when page loads
fetch('get_plots.php')
  .then(res => res.json())
  .then(data => {
    plotsData = data; // save to global variable
    populateDatalists(plotsData); // fill datalists for search
  });

// Populate datalists function
function populateDatalists(data){
  const sectors = new Set(), subsectors = new Set(), plots = new Set(), streets = new Set();
  data.forEach(row => {
    if(row.Sector) sectors.add(String(row.Sector).trim());
    if(row.Subsector) subsectors.add(String(row.Subsector).trim());
    if(row.Plot) plots.add(String(row.Plot).trim());
    const streetVal = row['Street_No/Road'] ?? row.Street ?? row.street;
    if(streetVal) streets.add(String(streetVal).trim());
  });
  function fillDatalist(id, set){ 
    const dl = document.getElementById(id); 
    dl.innerHTML = ''; 
    Array.from(set).sort().forEach(v=>{
      const o = document.createElement('option'); 
      o.value = v; 
      dl.appendChild(o);
    }); 
  }
  fillDatalist('sectorsList', sectors);
  fillDatalist('subsectorsList', subsectors);
  fillDatalist('plotsList', plots);
  fillDatalist('streetsList', streets);
}

function searchAll() {
  const sectorVal = document.getElementById('searchSector').value.trim().toLowerCase();
  const subsectorVal = document.getElementById('searchSubsector').value.trim().toLowerCase();
  const plotVal = document.getElementById('searchPlot').value.trim().toLowerCase();
  const streetVal = document.getElementById('searchStreet').value.trim().toLowerCase();

  if(!sectorVal && !subsectorVal && !plotVal && !streetVal){ 
    alert('Enter at least one value!'); 
    return; 
  }

  // Get all matches
  const matches = plotsData.filter(item => {
    return (!sectorVal || String(item.Sector).toLowerCase() === sectorVal) &&
           (!subsectorVal || String(item.Subsector).toLowerCase() === subsectorVal) &&
           (!plotVal || String(item.Plot).toLowerCase() === plotVal) &&
           (!streetVal || String(item['Street_No/Road'] ?? item.Street ?? '').toLowerCase() === streetVal);
  });

  if(matches.length > 0){
    // Remove previous marker if exists
    if(window.lastMarker) map.removeLayer(window.lastMarker);

    const match = matches[0]; // pick the first exact match
    const lat = parseFloat(match.Latitude ?? match.latitude);
    const lon = parseFloat(match.Longitude ?? match.longitude);

    if(!isNaN(lat) && !isNaN(lon)){
      const popupContent = `
        <b>Sector:</b> ${match.Sector ?? '-'}<br>
        <b>Subsector:</b> ${match.Subsector ?? '-'}<br>
        <b>Plot:</b> ${match.Plot ?? '-'}<br>
        <b>Street:</b> ${match['Street_No/Road'] ?? match.Street ?? '-'}<br>
        <b>Latitude:</b> ${lat.toFixed(6)}<br>
        <b>Longitude:</b> ${lon.toFixed(6)}
      `;

      const marker = L.marker([lat, lon]).addTo(map);
      window.lastMarker = marker;

      map.flyTo([lat, lon], 16, { duration: 1.5, easeLinearity: 0.25 });
      setTimeout(() => marker.bindPopup(popupContent).openPopup(), 500); // popup appears faster
    } else {
      alert('No valid coordinates found!');
    }
  } else {
    alert('No matching result found!');
  }
}




const sidebar = document.getElementById('searchSidebar');
const toggleBtn = document.getElementById('toggleSidebarBtn');

toggleBtn.addEventListener('click', toggleSidebar);

function toggleSidebar() {
  sidebar.classList.toggle('show');
}

</script>

</body>
</html>
