<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CDA Plots Map</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<style>
* { box-sizing: border-box; margin:0; padding:0; font-family: Arial, sans-serif; }
html, body { height:100%; width:100%; }

#searchSidebar {
  position: absolute; top: 0; left: -250px; width: 250px; height: 100%; background: #fff;
  z-index: 1000; box-shadow: 2px 0 8px rgba(0,0,0,0.2); padding: 15px; transition: left 0.3s ease; overflow-y: auto;
}
#searchSidebar.show { left: 0; }
#toggleSidebarBtn {
  position: absolute; top: 20px; left:15px; z-index: 1100;
  background: #198754; color: #fff; border: none; border-radius: 6px;
  padding: 6px 10px; cursor: pointer; font-weight: 600;
}
#map { position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 0; }

.toolbar {
  position: absolute; top: 15px; left: 50%; transform: translateX(-50%);
  z-index: 1000; display: flex; gap: 10px; background: rgba(255,255,255,0.95);
  padding: 8px 12px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
.toolbar button {
  padding: 8px 14px; border-radius: 6px; background-color: #198754; color: white; border: none;
  cursor: pointer; font-size: 14px; font-weight: 600; box-shadow: 0 1px 4px rgba(0,0,0,0.2);
}
.toolbar button:hover { background-color: #157347; transform: scale(1.05); }

.opacity-toolbar {
  position:absolute; top:80px; right:10px; z-index:1000; background: rgba(255,255,255,0.95);
  border-radius:10px; padding:12px 16px; box-shadow:0 2px 8px rgba(0,0,0,0.2); font-size:13px; color:#000; max-width:220px;
}
.opacity-toolbar h3 { margin-bottom:6px; font-size:14px; font-weight:700; }
.opacity-toolbar div { margin-bottom:8px; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.opacity-toolbar label { flex:0 0 100px; font-weight:600; color:black; font-size:13px; }
.opacity-toolbar input[type="range"] { -webkit-appearance:none; flex:1; height:6px; background: linear-gradient(90deg,#2196F3,#64B5F6); border-radius:4px; outline:none; }
.opacity-toolbar input[type="range"]::-webkit-slider-thumb { -webkit-appearance:none; height:16px; width:16px; background:#1976D2; border:2px solid #0D47A1; border-radius:3px; cursor:pointer; transition: all 0.3s ease; }
.opacity-toolbar input[type="range"]::-webkit-slider-thumb:hover { transform:scale(1.2); background:#0D47A1; }

.markaz-select-container { display:none !important; }
</style>
</head>
<body>

<button id="toggleSidebarBtn">🔍</button>

<div id="searchSidebar">
  <h3>Search Plots</h3>
  <div class="search-box"><label>Sector</label><input list="sectorsList" id="searchSector" placeholder="Enter sector"><datalist id="sectorsList"></datalist></div>
  <div class="search-box"><label>Subsector</label><input list="subsectorsList" id="searchSubsector" placeholder="Enter subsector"><datalist id="subsectorsList"></datalist></div>
  <div class="search-box"><label>Plot</label><input list="plotsList" id="searchPlot" placeholder="Enter plot"><datalist id="plotsList"></datalist></div>
  <div class="search-box"><label>Street</label><input list="streetsList" id="searchStreet" placeholder="Enter street"><datalist id="streetsList"></datalist></div>
  <button onclick="searchAll()">Search</button>
  <a href="Housing.php" style="display:block;margin-top:10px;padding:8px 12px;background-color:#198754;color:white;text-align:center;border-radius:6px;text-decoration:none;font-weight:600;">Housing Schemes</a>
</div>

<div id="map"></div>

<div class="toolbar">
  <button id="identifyBtn">📍</button>
  <button id="measureBtn">📏</button>
  <button id="areaBtn">📐</button>
  <button id="clearMeasure">🧹</button>
  <button id="downloadMapBtn">📄</button>
  <button id="searchCoordBtn">🗺️</button>
</div>

<div class="opacity-toolbar">
  <h3>Base Maps</h3>
  <div><select id="baseMapSelect" style="width:100%; padding:4px 6px; border-radius:4px; border:1px solid #ccc;">
    <option value="openStreet">OpenStreetMap</option>
    <option value="googleStreets">Google Streets</option>
    <option value="googleSatellite" selected>Google Satellite</option>
    <option value="googleHybrid">Google Hybrid</option>
  </select></div>

  <h3>Overlay Layers</h3>
  <div><input type="checkbox" id="boundaryLayerCheckbox" checked><label for="boundaryLayerCheckbox">ICT Boundary</label><input type="range" id="boundaryOpacity" min="0" max="1" step="0.1" value="0.7"></div>
  <div><input type="checkbox" id="zonesLayerCheckbox"><label for="zonesLayerCheckbox">Zones</label><input type="range" id="zonesOpacity" min="0" max="1" step="0.1" value="0.7"></div>
  <div><input type="checkbox" id="plotsLayerCheckbox"><label for="plotsLayerCheckbox">Plots</label><input type="range" id="plotsOpacity" min="0" max="1" step="0.1" value="0.7"></div>
  <div><input type="checkbox" id="railwayLayerCheckbox"><label for="railwayLayerCheckbox">Railway Lines</label><input type="range" id="railwayOpacity" min="0" max="1" step="0.1" value="0.7"></div>
  <div><input type="checkbox" id="roadsLayerCheckbox"><label for="roadsLayerCheckbox">Major Roads</label><input type="range" id="roadsOpacity" min="0" max="1" step="0.1" value="0.7"></div>
  <div><input type="checkbox" id="housingSchemesLayerCheckbox"><label for="housingSchemesLayerCheckbox">Private Housing Schemes</label><input type="range" id="housingSchemesOpacity" min="0" max="1" step="0.1" value="1"></div>
  <div><input type="checkbox" id="sectorBoundariesLayerCheckbox"><label for="sectorBoundariesLayerCheckbox">Sector & Sub-Sector Boundaries</label><input type="range" id="sectorBoundariesOpacity" min="0" max="1" step="0.1" value="0.7"></div>
  <div><input type="checkbox" id="d12TiffCheckbox"><label for="d12TiffCheckbox">D-12 Raster (TIFF)</label><input type="range" id="d12TiffOpacity" min="0" max="1" step="0.1" value="1"></div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function polygonArea(latlngs){let area=0;const R=6378137;for(let i=0,len=latlngs.length;i<len;i++){const p1=latlngs[i],p2=latlngs[(i+1)%len];area+=(p2.lng-p1.lng)*(2+Math.sin(p1.lat*Math.PI/180)+Math.sin(p2.lat*Math.PI/180));}return Math.abs(area*R*R/2);}

const openStreet=L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19, attribution:'&copy; OpenStreetMap'});
const googleStreets=L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{maxZoom:20, subdomains:['mt0','mt1','mt2','mt3']});
const googleSatellite=L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{maxZoom:20, subdomains:['mt0','mt1','mt2','mt3']});
const googleHybrid=L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',{maxZoom:20, subdomains:['mt0','mt1','mt2','mt3']});

const map=L.map('map',{center:[33.6844,73.0479], zoom:12, layers:[googleSatellite], zoomControl:false});
L.control.zoom({position:'bottomright'}).addTo(map);

const boundaryLayer=L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms",{layers:'ISB:ICT_Boundary', format:'image/png', transparent:true}).addTo(map);
const zonesLayer=L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms",{layers:'ISB:ICT_Zones', format:'image/png', transparent:true});
const railwayLinesLayer=L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms",{layers:'ISB:Railway_Line', format:'image/png', transparent:true});
const majorRoadsLayer=L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms",{layers:'ISB:Major_Roads', format:'image/png', transparent:true});
const plotsLayer=L.tileLayer.wms("http://localhost:8080/geoserver/D12/wms",{layers:'D12:D-12_GCS_11032024', format:'image/png', transparent:true});
const housingSchemesLayer=L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms",{layers:'ISB:Private_Housing_Schemes', format:'image/png', transparent:true});
const sectorBoundariesLayer=L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms",{layers:"ISB:Sector_and_Sub_Sector_Boundaries",format:"image/png",transparent:true});
const d12TiffLayer=L.tileLayer.wms("http://localhost:8080/geoserver/ISB/wms",{layers:'ISB:D-12_tiff', format:'image/png', transparent:true});

const baseMapsObj={googleSatellite, googleStreets, openStreet, googleHybrid};
document.getElementById('baseMapSelect').addEventListener('change', e=>{const selected=e.target.value;Object.values(baseMapsObj).forEach(l=>map.removeLayer(l));map.addLayer(baseMapsObj[selected]);});

// Overlay checkboxes & opacity
const overlayLayers=[
  {cb:'boundaryLayerCheckbox', layer:boundaryLayer, op:'boundaryOpacity'},
  {cb:'zonesLayerCheckbox', layer:zonesLayer, op:'zonesOpacity'},
  {cb:'plotsLayerCheckbox', layer:plotsLayer, op:'plotsOpacity'},
  {cb:'railwayLayerCheckbox', layer:railwayLinesLayer, op:'railwayOpacity'},
  {cb:'roadsLayerCheckbox', layer:majorRoadsLayer, op:'roadsOpacity'},
  {cb:'housingSchemesLayerCheckbox', layer:housingSchemesLayer, op:'housingSchemesOpacity'},
  {cb:'sectorBoundariesLayerCheckbox', layer:sectorBoundariesLayer, op:'sectorBoundariesOpacity'},
  {cb:'d12TiffCheckbox', layer:d12TiffLayer, op:'d12TiffOpacity'}
];

overlayLayers.forEach(o=>{
  document.getElementById(o.cb).addEventListener('change', e=>e.target.checked?map.addLayer(o.layer):map.removeLayer(o.layer));
  document.getElementById(o.op).addEventListener('input', e=>o.layer.setOpacity(parseFloat(e.target.value)));
});

// Identify Tool
let identifyActive = false;

// Toggle Identify tool
document.getElementById('identifyBtn').addEventListener('click', () => {
    identifyActive = !identifyActive;
    alert(identifyActive ? "Identify ON: Click map to get plot info" : "Identify OFF");
});

// Map click event for Identify
map.on('click', e => {
    if (!identifyActive) return;

    const lat = parseFloat(e.latlng.lat);
    const lng = parseFloat(e.latlng.lng);

    // Tolerance in degrees (~0.001 ≈ 100m)
    const tolerance = 0.0002; // smaller tolerance
;

    // Find nearest plot within tolerance
    let nearestPlot = null;
    let minDistance = Infinity;

    plotsData.forEach(p => {
        const plotLat = parseFloat(p.Latitude ?? p.latitude);
        const plotLng = parseFloat(p.Longitude ?? p.longitude);

        if (isNaN(plotLat) || isNaN(plotLng)) return;

        const distance = Math.hypot(plotLat - lat, plotLng - lng);
        if (distance < tolerance && distance < minDistance) {
            nearestPlot = p;
            minDistance = distance;
        }
    });

    if (nearestPlot) {
        // Show full plot info
        const popupContent = `
            <b>Sector:</b> ${nearestPlot.Sector ?? '-'}<br>
            <b>Subsector:</b> ${nearestPlot.Subsector ?? '-'}<br>
            <b>Plot:</b> ${nearestPlot.Plot ?? '-'}<br>
            <b>Street:</b> ${nearestPlot['Street_No/Road'] ?? nearestPlot.Street ?? '-'}<br>
            <b>Type:</b> ${nearestPlot.Type ?? '-'}<br>
            <b>Size:</b> ${nearestPlot.Size ?? '-'}<br>
            <b>Latitude:</b> ${nearestPlot.Latitude ?? nearestPlot.latitude}<br>
            <b>Longitude:</b> ${nearestPlot.Longitude ?? nearestPlot.longitude}
        `;
        L.popup()
         .setLatLng([nearestPlot.Latitude ?? nearestPlot.latitude, nearestPlot.Longitude ?? nearestPlot.longitude])
         .setContent(popupContent)
         .openOn(map);
    } else {
        // Fallback: show only coordinates
        L.popup()
         .setLatLng([lat, lng])
         .setContent(`
            <b>Coordinates</b><br>
            Latitude: ${lat.toFixed(6)}<br>
            Longitude: ${lng.toFixed(6)}
         `)
         .openOn(map);
    }
});


// Measure
let measureActive=false, measurePoints=[], measureLine;
document.getElementById('measureBtn').addEventListener('click', ()=>{measureActive=!measureActive;alert(measureActive?"Click points to measure distance.":"Distance tool off.");});
map.on('click', e=>{if(measureActive){measurePoints.push(e.latlng);if(measureLine) map.removeLayer(measureLine);measureLine=L.polyline(measurePoints,{color:'red'}).addTo(map);if(measurePoints.length>1){const d=measurePoints.reduce((t,p,i,a)=>i>0?t+a[i-1].distanceTo(p):t,0);L.popup().setLatLng(measurePoints[measurePoints.length-1]).setContent(`<b>Total Distance:</b> ${d.toFixed(2)} m`).openOn(map);}}});

// Area
let areaActive=false, areaPoints=[], areaPolygon=null, areaLabel=null;
document.getElementById('areaBtn').addEventListener('click', ()=>{areaActive=!areaActive;alert(areaActive?"Area measurement active.":"Area measurement off.");if(!areaActive){areaPoints=[];if(areaPolygon) map.removeLayer(areaPolygon);if(areaLabel) map.removeLayer(areaLabel);areaPolygon=null;areaLabel=null;}});
map.on('click', e=>{if(areaActive){areaPoints.push(e.latlng);if(areaPolygon) map.removeLayer(areaPolygon);if(areaLabel) map.removeLayer(areaLabel);areaPolygon=L.polygon(areaPoints,{color:'#1d7b1d',fillColor:'#1d7b1d',fillOpacity:0.3}).addTo(map);if(areaPoints.length>2){const a=polygonArea(areaPolygon.getLatLngs()[0]);const display=a>1e6?(a/1e6).toFixed(2)+" km²":a.toFixed(2)+" m²";areaLabel=L.popup({closeButton:false,autoClose:false,className:'measurement-popup'}).setLatLng(areaPolygon.getBounds().getCenter()).setContent(`<b>Total Area:</b> ${display}`).openOn(map);}}});

// Clear
document.getElementById('clearMeasure').addEventListener('click', ()=>{if(measureLine) map.removeLayer(measureLine);measurePoints=[];map.closePopup();if(areaPolygon) map.removeLayer(areaPolygon);areaPolygon=null;if(areaLabel) map.removeLayer(areaLabel);areaLabel=null;areaPoints=[];});

// Download
document.getElementById('downloadMapBtn').addEventListener('click', async()=>{
  const mapContainer=document.getElementById('map');
  await new Promise(r=>{const i=setInterval(()=>{const tiles=mapContainer.querySelectorAll('img.leaflet-tile');if(Array.from(tiles).every(t=>t.complete&&t.naturalHeight!==0)){clearInterval(i);r();}},500);});
  html2canvas(mapContainer,{useCORS:true}).then(canvas=>{const img=canvas.toDataURL('image/png');const pdf=new jspdf.jsPDF('landscape','mm','a4');const w=290,h=(canvas.height*w)/canvas.width;pdf.addImage(img,'PNG',10,10,w,h);pdf.save('CDA_Map_View.pdf');});
});

// Search Coordinates
document.getElementById('searchCoordBtn').addEventListener('click', ()=>{
  let input=prompt("Enter coordinates as 'latitude, longitude' (e.g., 33.6844, 73.0479):");if(!input) return;
  let parts=input.split(',').map(s=>parseFloat(s.trim()));if(parts.length!==2||parts.some(isNaN)){alert("Invalid coordinates.");return;}
  const [lat,lng]=parts;map.flyTo([lat,lng],16);L.popup().setLatLng([lat,lng]).setContent(`<b>Lat:</b> ${lat.toFixed(6)}<br><b>Lng:</b> ${lng.toFixed(6)}`).openOn(map);
});

// Fetch plots data
let plotsData=[];
fetch('get_plots.php').then(r=>r.json()).then(data=>{plotsData=data;populateDatalists(plotsData);});

function populateDatalists(data){
  const sectors=new Set(), subsectors=new Set(), plots=new Set(), streets=new Set();
  data.forEach(row=>{if(row.Sector) sectors.add(String(row.Sector).trim()); if(row.Subsector) subsectors.add(String(row.Subsector).trim()); if(row.Plot) plots.add(String(row.Plot).trim()); const s=row['Street_No/Road']??row.Street??row.street; if(s) streets.add(String(s).trim());});
  [['sectorsList',sectors],['subsectorsList',subsectors],['plotsList',plots],['streetsList',streets]].forEach(([id,set])=>{const dl=document.getElementById(id);dl.innerHTML='';Array.from(set).sort().forEach(v=>{const o=document.createElement('option'); o.value=v; dl.appendChild(o);});});
}

function searchAll() {
  const sector = document.getElementById('searchSector').value.trim().toLowerCase();
  const subsector = document.getElementById('searchSubsector').value.trim().toLowerCase();
  const plot = document.getElementById('searchPlot').value.trim().toLowerCase();
  const street = document.getElementById('searchStreet').value.trim().toLowerCase();

  if (!sector && !subsector && !plot && !street) { 
    alert('Enter at least one value!'); 
    return; 
  }

  const matches = plotsData.filter(item =>
    (!sector || String(item.Sector).toLowerCase() === sector) &&
    (!subsector || String(item.Subsector).toLowerCase() === subsector) &&
    (!plot || String(item.Plot).toLowerCase() === plot) &&
    (!street || String(item['Street_No/Road'] ?? item.Street ?? '').toLowerCase() === street)
  );

  if (matches.length > 0) {
    if (window.lastMarker) map.removeLayer(window.lastMarker);

    const m = matches[0],
          lat = parseFloat(m.Latitude ?? m.latitude),
          lon = parseFloat(m.Longitude ?? m.longitude);

    if (!isNaN(lat) && !isNaN(lon)) {
      const popup = `
        <b>Sector:</b> ${m.Sector ?? '-'}<br>
        <b>Subsector:</b> ${m.Subsector ?? '-'}<br>
        <b>Plot:</b> ${m.Plot ?? '-'}<br>
        <b>Type:</b> ${m.Type ?? '-'}<br>
        <b>Size:</b> ${m.Size ?? '-'}<br>
        <b>Street:</b> ${m['Street_No/Road'] ?? m.Street ?? '-'}<br>
        <b>Latitude:</b> ${lat.toFixed(6)}<br>
        <b>Longitude:</b> ${lon.toFixed(6)}
      `;

      const marker = L.marker([lat, lon]).addTo(map);
      window.lastMarker = marker;

      map.flyTo([lat, lon], 16, { duration: 1.5, easeLinearity: 0.25 });
      setTimeout(() => marker.bindPopup(popup).openPopup(), 500);
    } else {
      alert('No valid coordinates found!');
    }
  } else {
    alert('No matching result found!');
  }
}

// Sidebar toggle
const sidebar=document.getElementById('searchSidebar');
document.getElementById('toggleSidebarBtn').addEventListener('click', ()=>sidebar.classList.toggle('show'));
</script>
</body>
</html>
