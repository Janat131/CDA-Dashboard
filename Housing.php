<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Housing Schemes Map</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<style>
* { box-sizing: border-box; margin:0; padding:0; font-family: Arial, sans-serif; }
html, body { height:100%; width:100%; }

#searchSidebar {
  position: absolute; top:0; left:-250px; width:250px; height:100%; background:#fff;
  z-index:1000; box-shadow:2px 0 8px rgba(0,0,0,0.2); padding:15px;
  transition:left 0.3s ease; overflow-y:auto;
}
#searchSidebar.show { left:0; }
#toggleSidebarBtn {
  position:absolute; top:20px; left:15px; z-index:1100; background:#198754;
  color:#fff; border:none; border-radius:6px; padding:6px 10px; cursor:pointer; font-weight:600;
}

#map { position:absolute; top:0; left:0; right:0; bottom:0; z-index:0; }

.toolbar {
  position:absolute; top:15px; left:50%; transform:translateX(-50%);
  z-index:1000; display:flex; gap:10px; background:rgba(255,255,255,0.95);
  padding:8px 12px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.2);
}
.toolbar button {
  padding:8px 14px; border-radius:6px; background-color:#198754; color:white;
  border:none; cursor:pointer; font-size:14px; font-weight:600; box-shadow:0 1px 4px rgba(0,0,0,0.2);
}
.toolbar button:hover { background-color:#157347; transform:scale(1.05); }

.opacity-toolbar {
  position:absolute; top:80px; right:10px; z-index:1000;
  background: rgba(255,255,255,0.95); border-radius:10px;
  padding:12px 16px; box-shadow:0 2px 8px rgba(0,0,0,0.2);
  font-size:13px; color:#000; max-width:220px;
}
.opacity-toolbar h3 { margin-bottom:6px; font-size:14px; font-weight:700; }
.opacity-toolbar div { margin-bottom:8px; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.opacity-toolbar label { flex:0 0 100px; font-weight:600; color:black; font-size:13px; }
.opacity-toolbar input[type="range"] { -webkit-appearance:none; flex:1; height:6px; background: linear-gradient(90deg,#2196F3,#64B5F6); border-radius:4px; outline:none; }
.opacity-toolbar input[type="range"]::-webkit-slider-thumb { -webkit-appearance:none; height:16px; width:16px; background:#1976D2; border:2px solid #0D47A1; border-radius:3px; cursor:pointer; transition: all 0.3s ease; }
.opacity-toolbar input[type="range"]::-webkit-slider-thumb:hover { transform:scale(1.2); background:#0D47A1; }
</style>
</head>
<body>

<!-- Sidebar Toggle -->
<button id="toggleSidebarBtn">🔍</button>

<!-- Sidebar -->
<div id="searchSidebar">
  <h3>Search Housing Schemes</h3>
  <div class="search-box">
    <label>Name:</label>
    <input list="namesList" id="searchName" placeholder="Enter housing scheme name">
    <datalist id="namesList"></datalist>
  </div>
  <div class="search-box">
    <label>Directorate:</label>
    <input list="directorateList" id="searchDirectorate" placeholder="Enter directorate">
    <datalist id="directorateList"></datalist>
  </div>
  <button onclick="searchHousing()">Search</button>
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
  <button id="searchCoordBtn">🗺️</button>
</div>

<!-- Base & Overlay Layers Panel -->
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
    <input type="checkbox" id="housingSchemesLayerCheckbox" checked>
    <label for="housingSchemesLayerCheckbox">Private Housing Schemes</label>
    <input type="range" id="housingSchemesOpacity" min="0" max="1" step="0.1" value="1">
  </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
// === MAP & BASE LAYERS ===
const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3'] });
const googleStreets   = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3'] });
const googleHybrid    = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3'] });
const openStreet      = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 });
const baseMapsObj = { googleSatellite, googleStreets, googleHybrid, openStreet };

// Overlay Layer
const housingSchemesLayer = L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms", { layers:'ISB:Private_Housing_Schemes', format:'image/png', transparent:true });

// Initialize Map
const map = L.map('map', { center:[33.6844,73.0479], zoom:12, layers:[googleSatellite], zoomControl:false });
L.control.zoom({ position:'bottomright' }).addTo(map);
if(document.getElementById('housingSchemesLayerCheckbox').checked) map.addLayer(housingSchemesLayer);

// Base map switcher
document.getElementById('baseMapSelect').addEventListener('change', e=>{
    Object.values(baseMapsObj).forEach(layer=>map.removeLayer(layer));
    map.addLayer(baseMapsObj[e.target.value]);
    if(document.getElementById('housingSchemesLayerCheckbox').checked) map.addLayer(housingSchemesLayer);
});

// Overlay toggle & opacity
document.getElementById('housingSchemesLayerCheckbox').addEventListener('change', e=>e.target.checked?map.addLayer(housingSchemesLayer):map.removeLayer(housingSchemesLayer));
document.getElementById('housingSchemesOpacity').addEventListener('input', e=>housingSchemesLayer.setOpacity(parseFloat(e.target.value)));

// Sidebar toggle
const sidebar=document.getElementById('searchSidebar');
document.getElementById('toggleSidebarBtn').addEventListener('click',()=>sidebar.classList.toggle('show'));

// Fetch Housing Data
let housingData=[];
fetch('get_housing.php').then(res=>res.json()).then(data=>{ housingData=data; populateDatalists(housingData); });
function populateDatalists(data){
    const names=new Set(), directorates=new Set();
    data.forEach(r=>{ names.add(r.Name); directorates.add(r.Directorate); });
    function fillDatalist(id,set){ const dl=document.getElementById(id); dl.innerHTML=''; Array.from(set).sort().forEach(v=>{ const o=document.createElement('option'); o.value=v; dl.appendChild(o); }); }
    fillDatalist('namesList', names); fillDatalist('directorateList', directorates);
}
function searchHousing(){
    const nameVal=document.getElementById('searchName').value.trim().toLowerCase();
    const dirVal=document.getElementById('searchDirectorate').value.trim().toLowerCase();
    const matches=housingData.filter(i=>(!nameVal||i.Name.toLowerCase().includes(nameVal))&&(!dirVal||i.Directorate.toLowerCase().includes(dirVal)));
    if(matches.length>0){
        const r=matches[0]; const lat=parseFloat(r.Latitude); const lon=parseFloat(r.Longitude);
        if(!isNaN(lat)&&!isNaN(lon)){
            if(window.lastMarker) map.removeLayer(window.lastMarker);
            window.lastMarker=L.marker([lat,lon]).addTo(map).bindPopup(`<b>${r.Name}</b><br>${r.Directorate}<br>Area: ${r.Area}`).openPopup();
            map.flyTo([lat,lon],16); if(document.getElementById('housingSchemesLayerCheckbox').checked) map.addLayer(housingSchemesLayer);
        }
    } else alert('No results found!');
}

// === TOOLBAR FUNCTIONALITY ===
let measureActive=false, measurePoints=[], measureLine=null;
let areaActive=false, areaPoints=[], areaPolygon=null, areaLabel=null;

// Calculate polygon area
function polygonArea(latlngs){
    let area=0, R=6378137;
    for(let i=0;i<latlngs.length;i++){
        const p1=latlngs[i], p2=latlngs[(i+1)%latlngs.length];
        area+=(p2.lng-p1.lng)*(2+Math.sin(p1.lat*Math.PI/180)+Math.sin(p2.lat*Math.PI/180));
    }
    return Math.abs(area*R*R/2);
}

// Identify Tool
let identifyActive = false;
let identifyHandler = null;

document.getElementById('identifyBtn').addEventListener('click', () => {
    identifyActive = !identifyActive; // toggle identify mode

    if (identifyActive) {
        // Activate identify: listen for all clicks
        identifyHandler = function(e) {
            L.popup()
             .setLatLng(e.latlng)
             .setContent(`Lat: ${e.latlng.lat.toFixed(6)}<br>Lon: ${e.latlng.lng.toFixed(6)}`)
             .openOn(map);
        };
        map.on('click', identifyHandler);
        alert('Identify tool activated. Click anywhere on the map.');
    } else {
        // Deactivate identify: stop listening
        map.off('click', identifyHandler);
        identifyHandler = null;
        alert('Identify tool deactivated.');
    }
});


// Distance Tool
document.getElementById('measureBtn').addEventListener('click',()=>{
    measureActive=!measureActive; areaActive=false;
    if(!measureActive){ if(measureLine) map.removeLayer(measureLine); measurePoints=[]; }
    alert(measureActive?'Click points to measure distance':'Distance tool off');
});

// Area Tool
document.getElementById('areaBtn').addEventListener('click',()=>{
    areaActive=!areaActive; measureActive=false;
    if(!areaActive){ if(areaPolygon) map.removeLayer(areaPolygon); if(areaLabel) map.removeLayer(areaLabel); areaPolygon=null; areaLabel=null; areaPoints=[]; }
    alert(areaActive?'Click points to measure area':'Area tool off');
});

// Map click for tools
map.on('click', e=>{
    if(measureActive){
        measurePoints.push(e.latlng);
        if(measureLine) map.removeLayer(measureLine);
        measureLine=L.polyline(measurePoints, { color:'red' }).addTo(map);
        if(measurePoints.length>1){
            const dist=measurePoints.reduce((t,p,i,a)=>(i>0?t+a[i-1].distanceTo(p):t),0);
            L.popup({autoClose:false,closeOnClick:false}).setLatLng(e.latlng).setContent(`Distance: ${dist.toFixed(2)} m`).openOn(map);
        }
    }
    if(areaActive){
        areaPoints.push(e.latlng);
        if(areaPolygon) map.removeLayer(areaPolygon);
        if(areaLabel) map.removeLayer(areaLabel);
        areaPolygon=L.polygon(areaPoints,{color:'#1d7b1d', fillColor:'#1d7b1d', fillOpacity:0.3}).addTo(map);
        if(areaPoints.length>2){
            const area=polygonArea(areaPolygon.getLatLngs()[0]);
            const displayArea=area>1e6?(area/1e6).toFixed(2)+' km²':area.toFixed(2)+' m²';
            areaLabel=L.popup({closeButton:false, autoClose:false}).setLatLng(areaPolygon.getBounds().getCenter()).setContent(`Area: ${displayArea}`).openOn(map);
        }
    }
});

// Clear Measurement
document.getElementById('clearMeasure').addEventListener('click',()=>{
    if(measureLine) map.removeLayer(measureLine); measureLine=null; measurePoints=[];
    if(areaPolygon) map.removeLayer(areaPolygon); areaPolygon=null;
    if(areaLabel) map.removeLayer(areaLabel); areaLabel=null; areaPoints=[];
});

// Download Map as Image
document.getElementById('downloadMapBtn').addEventListener('click',()=>{
    html2canvas(document.getElementById('map')).then(canvas=>{
        const link=document.createElement('a'); link.href=canvas.toDataURL(); link.download='map.png'; link.click();
    });
});

// Search Coordinates
document.getElementById('searchCoordBtn').addEventListener('click',()=>{
    const lat=parseFloat(prompt('Enter latitude:')), lon=parseFloat(prompt('Enter longitude:'));
    if(!isNaN(lat)&&!isNaN(lon)){
        if(window.lastMarker) map.removeLayer(window.lastMarker);
        window.lastMarker=L.marker([lat,lon]).addTo(map).bindPopup(`Lat:${lat}<br>Lon:${lon}`).openPopup();
        map.flyTo([lat,lon],16);
    } else alert('Invalid coordinates!');
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</body>
</html>
