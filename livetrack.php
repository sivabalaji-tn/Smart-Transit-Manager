<?php

date_default_timezone_set('Asia/Kolkata');
include 'includes/db.php';

$route_id = isset($_GET['route_id']) ? intval($_GET['route_id']) : 0;
$bus_id   = isset($_GET['bus_id']) ? intval($_GET['bus_id']) : 0;

if ($route_id === 0 || $bus_id === 0) {
    header("Location: index.php?error=Missing route or bus selection.");
    exit;
}

// default map center (if no stops found)
$initialLat = 11.0582;
$initialLon = 77.3883;
$route_number = 'N/A';
$bus_number = 'N/A';
$stops = [];

try {
    // Fetch route/bus basic info
    $sql = "SELECT r.route_number, b.bus_number
            FROM routes r
            JOIN buses b ON r.route_id = b.route_id
            WHERE r.route_id = ? AND b.bus_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $route_id, $bus_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $route_number = htmlspecialchars($row['route_number']);
        $bus_number = htmlspecialchars($row['bus_number']);
    }
    $stmt->close();

    // Fetch stops with coordinates ordered by sequence_number
    $sql = "SELECT stop_id, stop_name, latitude, longitude, sequence_number
            FROM route_stops
            WHERE route_id = ?
            ORDER BY sequence_number ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $route_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        // Keep as numeric strings to avoid JSON float precision surprises, JS will parse as needed
        $r['latitude'] = (string)$r['latitude'];
        $r['longitude'] = (string)$r['longitude'];
        $stops[] = $r;
    }
    $stmt->close();

    if (count($stops) > 0) {
        // Use first stop for initial center
        $initialLat = (float)$stops[0]['latitude'];
        $initialLon = (float)$stops[0]['longitude'];
    }

} catch (Exception $e) {
    // In production, log the exception
}

$conn->close();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Live Track - <?= htmlentities($route_number) ?> (Bus: <?= htmlentities($bus_number) ?>)</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
  :root{
    --bg: #0f1720;
    --card: #13171b;
    --muted: #9aa3ad;
    --accent: #0d6efd;
    --warn: #ffd580;
  }
  html,body{height:100%;}
  body { background: var(--bg); color: #e9eef6; font-family: Inter, system-ui, sans-serif; }
  .main-container{ background: var(--card); border-radius: 12px; padding: 16px; box-shadow: 0 6px 20px rgba(0,0,0,0.6); }
  /* Layout */
  #map { height: 75vh; min-height: 360px; border-radius:8px; }
  #log-panel { height: 75vh; min-height: 360px; overflow-y:auto; background:#0b0e11; border-radius:8px; padding: 12px; }
  .log-entry { padding: 10px; border-radius:8px; margin-bottom:10px; background: linear-gradient(90deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); border: 1px solid rgba(255,255,255,0.03); }
  .log-entry:last-child { border: 1px solid transparent; }
  .log-time { font-size: 0.8rem; color: var(--muted); margin-right:8px; }
  .log-text { font-size: 0.95rem; color: #fff; }
  .log-success{ color: #99ffb3; font-weight:600; }
  .log-warning{ color: var(--warn); font-weight:700; }
  .log-danger{ color: #ff9b9b; font-weight:700; }
  .text-info-accent { color: #5bc0de !important; }
  /* Emergency button */
  #emergency-btn{ position: fixed; right: 18px; bottom: 110px; z-index: 1400; }
  .floating-badge{ position: fixed; right: 18px; bottom: 20px; z-index:1400; }
  /* Mobile adjustments: logs become bottom collapsible */
  @media (max-width: 991px) {
    #map { height: 62vh; min-height: 320px; }
    #log-panel { height: auto; max-height: 32vh; }
    #emergency-btn{ bottom: 130px; right: 14px; }
  }
  /* small helper */
  .transparent-icon { background: transparent; border: none; }
</style>
</head>
<body>

<div class="container py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">Live Tracking — Route: <span class="text-info-accent"><?= htmlentities($route_number) ?></span></h4>
      <small class="text-muted">Bus: <?= htmlentities($bus_number) ?></small>
    </div>
    <div>
      <a class="btn btn-sm btn-outline-light" href="index.php"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
  </div>

  <div class="main-container">
    <div class="row g-3">
      <div class="col-lg-8 col-12">
        <div id="map"></div>
      </div>

      <div class="col-lg-4 col-12">
        <div id="log-panel" aria-live="polite" aria-atomic="true">
          <h6 class="text-warning"><i class="bi bi-clock-history"></i> LIVE LOGS</h6>
          <div id="log-feed" class="mt-2">
            <div class="text-muted small">Waiting for bus to start...</div>
          </div>
        </div>
      </div>

      <div class="col-12 mt-3">
        <div class="bg-dark p-2 rounded text-muted small">
          Note: Logs show when driver starts and when approaching/arriving stops (within 100m). Emergency events are shown immediately.
        </div>
      </div>
    </div>
  </div>
</div>

<button id="emergency-btn" class="btn btn-danger btn-lg rounded-circle shadow" title="Report Emergency">
  <i class="bi bi-exclamation-octagon-fill"></i>
</button>

<div class="floating-badge">
  <div id="bus-status-badge" class="badge bg-secondary p-2 rounded-pill shadow">Status: <span id="status-text">Offline</span></div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
/* --- Config & Data passed from PHP --- */
const stops = <?= json_encode($stops, JSON_NUMERIC_CHECK) ?>; // [{stop_id, stop_name, latitude, longitude, sequence_number}, ...]
const busId = <?= json_encode($bus_id) ?>;
const initialLat = <?= json_encode($initialLat) ?>;
const initialLon = <?= json_encode($initialLon) ?>;
const arrivalDistanceMeters = 100; // 100 meters proximity threshold
const apiBusLocation = 'get-bus-location.php?bus_id=' + encodeURIComponent(busId);
const apiReportEmergency = 'report-emergency.php'; // POST { bus_id, lat, lon }

/* --- State --- */
let map = null;
let busMarker = null;
let busMarkerCircle = null;
let prevStatus = null;
let tripStarted = false;
let stopVisited = new Array(stops.length).fill(false);
let pollingInterval = 1500; // ms
let pollingTimer = null;
let latestLocation = { lat: null, lon: null }; 
let lastFetchErrorLogged = false;

/* --- Custom Icons --- */

// 1. Bus Icon (Moving Vehicle)
const BusIcon = L.divIcon({
    className: 'custom-bus-icon transparent-icon', // Use transparent-icon helper
    html: '<i class="bi bi-bus-front-fill text-danger fs-3"></i>', // Prominent RED bus icon
    iconSize: [30, 30],
    iconAnchor: [15, 30] // Anchor icon correctly to its tip/bottom center
});

// 2. Stop Icon (Static Star/Dot)
const StopIcon = L.divIcon({
    className: 'custom-stop-icon transparent-icon',
    html: '<i class="bi bi-geo-alt-fill text-warning fs-5"></i>', // Small YELLOW location marker
    iconSize: [20, 20],
    iconAnchor: [10, 20]
});


/* --- DOM refs --- */
/* Log feed helper */
const logFeedContainer = document.getElementById('log-feed'); // Reference the inner container

function addLog(messageHtml, level='info') {
    const time = new Date().toLocaleTimeString('en-IN', { hour12:false });
    const div = document.createElement('div');
    div.className = 'log-entry';
    const timeSpan = `<span class="log-time">${time}</span>`;
    const textClass = (level === 'success' ? 'log-success' : (level === 'warn' ? 'log-warning' : (level === 'danger' ? 'log-danger' : '')) );
    div.innerHTML = `<div class="small">${timeSpan}<span class="log-text ${textClass}"> ${messageHtml}</span></div>`;
    
    // Fix: Target the specific log feed container
    const waiting = logFeedContainer.querySelector('.text-muted');
    if (waiting) waiting.remove();
    logFeedContainer.prepend(div); // Prepend new log
}
// ...

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>"'`=\/]/g, function(s) {
        return ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;' })[s];
    });
}

function haversine(lat1, lon1, lat2, lon2) {
    const R = 6371000;
    const toRad = v => v * Math.PI / 180;
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
             Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
             Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

/* --- Initialize Map & Route --- */
function initMapAndRoute() {
    map = L.map('map', { zoomControl: true }).setView([initialLat, initialLon], (stops.length > 1 ? 13 : 15));
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19, attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const routeCoords = [];
    stops.forEach((s, idx) => {
        const lat = parseFloat(s.latitude);
        const lon = parseFloat(s.longitude);
        if (isNaN(lat) || isNaN(lon)) return;
        
        // Use the distinct StopIcon here
        const marker = L.marker([lat, lon], { title: s.stop_name, icon: StopIcon }).addTo(map);
        marker.bindPopup(`<strong>${escapeHtml(s.stop_name)}</strong><br/>Stop ${s.sequence_number ?? (idx+1)}`);

        routeCoords.push([lat, lon]);
        s.approachLogged = false;
    });

    if (routeCoords.length > 1) {
        L.polyline(routeCoords, { color: '#0d6efd', weight: 5, opacity: 0.8 }).addTo(map);
        const bounds = L.latLngBounds(routeCoords);
        map.fitBounds(bounds, { padding:[40,40] });
    } else if (routeCoords.length === 1) {
        map.setView(routeCoords[0], 15);
    }
}

/* --- Update bus location from API --- */
function updateBusLocation() {
    fetch(apiBusLocation + '&_=' + Date.now())
      .then(r => r.json())
      .then(data => {
        lastFetchErrorLogged = false;
        // expected: { current_lat, current_lon, status, timestamp, emergency }
        const lat = parseFloat(data.current_lat) || 0;
        const lon = parseFloat(data.current_lon) || 0;
        const status = data.status || 'Offline'; 
        const ts = data.timestamp || new Date().toISOString();
        const emergencyFlag = !!data.emergency;

        statusTextEl.innerText = status;

        // Save last known for emergency fallback
        if (!isNaN(lat) && !isNaN(lon) && lat !== 0 && lon !== 0) {
            latestLocation.lat = lat;
            latestLocation.lon = lon;
        }

        // If not active or coords invalid, mark offline
        if (status !== 'Active' || isNaN(lat) || isNaN(lon) || lat === 0 || lon === 0) {
            if (busMarker) { map.removeLayer(busMarker); busMarker = null; }
            if (busMarkerCircle) { map.removeLayer(busMarkerCircle); busMarkerCircle = null; }

            if (prevStatus !== status) {
                addLog(`<span class="text-danger">STATUS:</span> Bus is ${escapeHtml(status)}.`, 'danger');
            }
            prevStatus = status;
            return;
        }

        // Active: Trip start detection
        if (!tripStarted && prevStatus !== 'Active') {
            tripStarted = true;
            addLog(`<span class="log-success">TRIP START:</span> Bus started tracking.`, 'success');
        }
        prevStatus = status;

        // Place or move marker
        const latLng = L.latLng(lat, lon);
        if (busMarker) {
            busMarker.setLatLng(latLng);
            busMarker.getPopup().setContent(`<strong>Bus Location</strong><br/>${new Date(ts).toLocaleTimeString()}`);
        } else {
            // Use the distinct BusIcon here
            busMarker = L.marker(latLng, { title: 'Bus Current Location', icon: BusIcon }).addTo(map);
            busMarker.bindPopup(`<strong>Bus Location</strong><br/>${new Date(ts).toLocaleTimeString()}`).openPopup();
        }
        // add small circle for visibility
        if (busMarkerCircle) {
            busMarkerCircle.setLatLng(latLng);
        } else {
            busMarkerCircle = L.circle(latLng, { radius: 12, color:'#0d6efd', fillColor:'#0d6efd', fillOpacity:0.9 }).addTo(map);
        }

        // Check stops in order (Logging Logic)
        for (let i = 0; i < stops.length; i++) {
            if (stopVisited[i]) continue;
            const s = stops[i];
            const stopLat = parseFloat(s.latitude);
            const stopLon = parseFloat(s.longitude);
            if (isNaN(stopLat) || isNaN(stopLon)) continue;
            const d = haversine(lat, lon, stopLat, stopLon);
            
            if (d <= arrivalDistanceMeters) {
                addLog(`<span class="log-warning">ARRIVED:</span> Reached ${escapeHtml(s.stop_name)} (Stop ${s.sequence_number ?? (i+1)})`, 'warn');
                stopVisited[i] = true;
                break; 
            } else if (d <= (arrivalDistanceMeters * 2) && d > arrivalDistanceMeters) {
                if (!s.approachLogged) {
                    addLog(`<span class="log-text text-info-accent">APPROACHING:</span> ${escapeHtml(s.stop_name)} — ${Math.round(d)} m away`);
                    s.approachLogged = true;
                }
                break;
            } else {
                if (s.approachLogged) {
                    s.approachLogged = false;
                }
            }
        }
        
        // Emergency flag logic (if set by API)
        if (emergencyFlag) {
            addLog(`<span class="log-danger">EMERGENCY REPORTED</span> — Driver pressed emergency.`, 'danger');
            const emMarker = L.circle([lat, lon], { radius: 50, color:'#ff4d4f', fillColor:'#ff4d4f', fillOpacity: 0.15 }).addTo(map);
            setTimeout(()=>{ if (map && map.hasLayer(emMarker)) map.removeLayer(emMarker); }, 45_000);
        }

      })
      .catch(err => {
        if (!lastFetchErrorLogged) {
            addLog('Tracking server unreachable. Retrying...', 'danger');
            lastFetchErrorLogged = true;
        }
        console.error('updateBusLocation error', err);
      });
}

/* --- Start and stop polling --- */
function startPolling() {
    if (pollingTimer) clearInterval(pollingTimer);
    pollingTimer = setInterval(updateBusLocation, pollingInterval);
    updateBusLocation(); // initial fetch
}
function stopPolling() {
    if (pollingTimer) clearInterval(pollingTimer);
    pollingTimer = null;
}

/* --- Emergency button wiring --- */
document.getElementById('emergency-btn').addEventListener('click', () => {
    let lat = null, lon = null;
    if (busMarker) {
        const ll = busMarker.getLatLng();
        lat = ll.lat; lon = ll.lng;
    } else if (latestLocation.lat && latestLocation.lon) {
        lat = latestLocation.lat; lon = latestLocation.lon;
    } else {
        addLog('<span class="log-danger">EMERGENCY:</span> No known bus location. Cannot report.', 'danger');
        return;
    }
    reportEmergency(lat, lon);
});

function reportEmergency(lat, lon) {
    addLog(`<span class="log-danger">EMERGENCY SENT:</span> marking location (${lat.toFixed(5)}, ${lon.toFixed(5)})`, 'danger');

    const emIcon = L.divIcon({ html: '<i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>', className:'transparent-icon', iconSize:[30,30] });
    const emMarker = L.marker([lat, lon], { title:'Emergency', icon: emIcon }).addTo(map);

    fetch(apiReportEmergency, {
        method: 'POST',
        headers: { 'Content-Type':'application/json' },
        body: JSON.stringify({ bus_id: busId, lat: lat, lon: lon })
    })
    .then(r => r.json())
    .then(resp => {
        if (resp && resp.success) {
            addLog(`<span class="log-danger">EMERGENCY RECORDED:</span> ${escapeHtml(resp.message || 'Server logged emergency.')}`, 'danger');
        } else {
            addLog(`<span class="log-danger">EMERGENCY FAILED:</span> ${escapeHtml(resp.message || 'Server error.')}`, 'danger');
        }
    })
    .catch(err => {
        console.error(err);
        addLog('<span class="log-danger">EMERGENCY ERROR:</span> Could not contact server.', 'danger');
    });

    setTimeout(()=>{ if (map && map.hasLayer(emMarker)) map.removeLayer(emMarker); }, 60_000);
}

/* --- Kick things off --- */
initMapAndRoute();
startPolling();

</script>
</body>
</html>
