<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Tracking — GoRide</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        :root {
            --ink:      #0a0a12;
            --ink-2:    #1c1c2e;
            --ink-3:    #2e2e45;
            --surface:  #13131f;
            --card:     #1a1a2e;
            --border:   rgba(255,255,255,0.07);
            --gold:     #f6ba02;
            --gold-2:   #ffdd57;
            --green:    #00e676;
            --red:      #ff5252;
            --blue:     #448aff;
            --text:     #e8e8f0;
            --muted:    #7b7b9a;
            --radius:   16px;
            --font:     'Sora', sans-serif;
            --mono:     'JetBrains Mono', monospace;
        }

        * { margin:0; padding:0; box-sizing:border-box; }

        html, body {
            height: 100%;
            font-family: var(--font);
            background: var(--ink);
            color: var(--text);
            overflow: hidden;
        }

        /* ── LAYOUT ───────────────────────────────── */
        .app-wrap {
            display: flex;
            flex-direction: column;
            height: 100dvh;
            position: relative;
        }

        /* ── TOP BAR ──────────────────────────────── */
        .topbar {
            position: absolute;
            top: 0; left: 0; right: 0;
            z-index: 600;
            padding: 14px 16px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            pointer-events: none;
        }

        .topbar > * { pointer-events: all; }

        .btn-back {
            width: 42px; height: 42px;
            border-radius: 12px;
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--text);
            display: flex; align-items: center; justify-content: center;
            font-size: 17px;
            cursor: pointer;
            text-decoration: none;
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
            transition: background 0.2s;
        }
        .btn-back:hover { background: var(--ink-3); }

        .topbar-info {
            flex: 1;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 9px 14px;
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .trip-id-label {
            font-size: 11px;
            font-family: var(--mono);
            color: var(--gold);
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .status-pill {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            transition: color 0.3s;
        }
        .status-pill .dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--muted);
            transition: background 0.3s, box-shadow 0.3s;
        }
        .status-pill.connected { color: var(--green); }
        .status-pill.connected .dot {
            background: var(--green);
            box-shadow: 0 0 8px var(--green);
            animation: blink 1.8s infinite;
        }
        @keyframes blink {
            0%,100% { opacity:1; } 50% { opacity:0.3; }
        }

        /* ── MAP ──────────────────────────────────── */
        #map {
            flex: 1;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        /* Leaflet override – dark tiles already dark, but attribution */
        .leaflet-control-attribution {
            background: rgba(10,10,18,0.7) !important;
            color: var(--muted) !important;
            font-size: 9px !important;
        }

        /* ── BOTTOM PANEL ─────────────────────────── */
        .bottom-panel {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            z-index: 600;
            padding: 0 14px 20px;
        }

        .panel-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 -8px 40px rgba(0,0,0,0.5);
            backdrop-filter: blur(16px);
        }

        /* drag handle */
        .panel-handle {
            width: 40px; height: 4px;
            background: var(--border);
            border-radius: 10px;
            margin: 12px auto 0;
        }

        .panel-body {
            padding: 14px 16px 16px;
        }

        /* driver row */
        .driver-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .driver-avatar-wrap {
            position: relative;
            flex-shrink: 0;
        }

        .driver-avatar {
            width: 48px; height: 48px;
            border-radius: 14px;
            object-fit: cover;
            background: var(--ink-3);
            border: 2px solid var(--border);
        }

        .avatar-status {
            position: absolute;
            bottom: -2px; right: -2px;
            width: 13px; height: 13px;
            background: var(--green);
            border: 2px solid var(--card);
            border-radius: 50%;
        }

        .driver-info { flex: 1; min-width: 0; }

        .driver-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .driver-sub {
            font-size: 11px;
            color: var(--muted);
            margin-top: 2px;
        }

        .driver-rating {
            display: flex;
            align-items: center;
            gap: 3px;
            font-size: 12px;
            font-weight: 700;
            color: var(--gold);
        }

        .driver-actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 38px; height: 38px;
            border-radius: 11px;
            border: 1px solid var(--border);
            background: var(--ink-3);
            color: var(--text);
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }
        .action-btn:hover { background: var(--gold); color: #000; }

        /* route strip */
        .route-strip {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 13px;
            padding: 12px 14px;
            margin-bottom: 12px;
        }

        .route-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            position: relative;
        }

        .route-dot-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 3px;
            gap: 0;
        }

        .r-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .r-dot.green { background: var(--green); box-shadow: 0 0 8px var(--green); }
        .r-dot.red   { background: var(--red);   box-shadow: 0 0 8px var(--red); }

        .r-line {
            width: 2px;
            flex: 1;
            min-height: 16px;
            background: repeating-linear-gradient(
                to bottom,
                var(--muted) 0, var(--muted) 4px,
                transparent 4px, transparent 8px
            );
            margin: 4px 0;
        }

        .route-text-col { flex: 1; display: flex; flex-direction: column; gap: 16px; }

        .route-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--muted);
        }

        .route-place {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            line-height: 1.3;
            margin-top: 2px;
        }

        /* meta chips */
        .meta-chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .chip {
            display: flex;
            align-items: center;
            gap: 5px;
            background: var(--ink-3);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
            flex: 1;
            min-width: 80px;
        }
        .chip i { font-size: 13px; }
        .chip .chip-label { font-size: 10px; color: var(--muted); display: block; font-weight: 500; }
        .chip.gold { border-color: rgba(246,186,2,0.3); }
        .chip.gold i { color: var(--gold); }
        .chip.green-c { border-color: rgba(0,230,118,0.2); }
        .chip.green-c i { color: var(--green); }
        .chip.blue-c { border-color: rgba(68,138,255,0.2); }
        .chip.blue-c i { color: var(--blue); }

        /* passenger coords (debug/info strip) */
        .pax-coords {
            display: flex;
            align-items: center;
            gap: 7px;
            background: rgba(68,138,255,0.07);
            border: 1px solid rgba(68,138,255,0.15);
            border-radius: 10px;
            padding: 7px 12px;
            margin-top: 10px;
            font-size: 11px;
            font-family: var(--mono);
            color: var(--blue);
        }
        .pax-coords i { font-size: 13px; }

        /* ── CUSTOM MARKER ────────────────────────── */
        .car-marker-wrap {
            position: relative;
        }

        /* ── ARRIVAL TOAST ───────────────────────── */
        .toast-wrap {
            position: absolute;
            top: 80px; left: 50%;
            transform: translateX(-50%);
            z-index: 700;
            width: calc(100% - 28px);
            max-width: 460px;
            pointer-events: none;
        }

        .toast {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.5);
            backdrop-filter: blur(16px);
            opacity: 0;
            transform: translateY(-12px);
            transition: opacity 0.35s, transform 0.35s;
            pointer-events: all;
        }

        .toast.show { opacity: 1; transform: translateY(0); }

        .toast-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .toast-icon.green-bg { background: rgba(0,230,118,0.15); color: var(--green); }
        .toast-icon.gold-bg  { background: rgba(246,186,2,0.15);  color: var(--gold); }
        .toast-icon.blue-bg  { background: rgba(68,138,255,0.15); color: var(--blue); }

        .toast-text { flex: 1; }
        .toast-title { font-size: 13px; font-weight: 700; }
        .toast-sub   { font-size: 11px; color: var(--muted); margin-top: 1px; }

        /* ── LOADING OVERLAY ─────────────────────── */
        .map-overlay {
            position: absolute;
            inset: 0;
            background: var(--ink);
            z-index: 800;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 16px;
            transition: opacity 0.6s;
        }

        .map-overlay.hidden { opacity: 0; pointer-events: none; }

        .loader-ring {
            width: 56px; height: 56px;
            border: 3px solid var(--border);
            border-top-color: var(--gold);
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .loader-text {
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
        }

        /* ── RESPONSIVE ──────────────────────────── */
        @media (min-width: 640px) {
            .bottom-panel { max-width: 480px; left: 50%; transform: translateX(-50%); padding-bottom: 28px; }
            .topbar { max-width: 480px; left: 50%; transform: translateX(-50%); }
            .toast-wrap { max-width: 460px; }
        }
    </style>
</head>
<body>

<div class="app-wrap">

    <!-- Loading Overlay -->
    <div class="map-overlay" id="mapOverlay">
        <div class="loader-ring"></div>
        <span class="loader-text">Connecting to driver...</span>
    </div>

    <!-- Top Bar -->
    <div class="topbar">
        <a href="{{ url()->previous() }}" class="btn-back"><i class="bi bi-arrow-left"></i></a>
        <div class="topbar-info">
            <span class="trip-id-label">TRIP #{{ $trip->id ?? $tripId }}</span>
            <div class="status-pill" id="statusPill">
                <span class="dot"></span>
                <span id="statusText">Connecting</span>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="toast-wrap">
        <div class="toast" id="toast">
            <div class="toast-icon" id="toastIcon"></div>
            <div class="toast-text">
                <div class="toast-title" id="toastTitle"></div>
                <div class="toast-sub"   id="toastSub"></div>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div id="map"></div>

    <!-- Bottom Panel -->
    <div class="bottom-panel">
        <div class="panel-card">
            <div class="panel-handle"></div>
            <div class="panel-body">

                <!-- Driver Row -->
                <div class="driver-row">
                    <div class="driver-avatar-wrap">
                        <img class="driver-avatar"
                             src="{{ $driver->photo_url ?? asset('images/default-driver.png') }}"
                             alt="Driver" id="driverAvatar"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($driver->name ?? "Driver") }}&background=1a1a2e&color=f6ba02&bold=true'">
                        <span class="avatar-status"></span>
                    </div>
                    <div class="driver-info">
                        <div class="driver-name">{{ $driver->name ?? 'Your Driver' }}</div>
                        <div class="driver-sub">{{ $vehicle->number_plate ?? '' }} · {{ $vehicle->model ?? '' }}</div>
                    </div>
                    <div class="driver-rating">
                        <i class="bi bi-star-fill"></i>
                        {{ number_format($driver->ratings ?? 4.8, 1) }}
                    </div>
                    <div class="driver-actions">
                        @if(!empty($driver->mobile))
                        <a href="tel:{{ $driver->mobile }}" class="action-btn">
                            <i class="bi bi-telephone-fill"></i>
                        </a>
                        @endif
                        <button class="action-btn" onclick="centerMap()">
                            <i class="bi bi-crosshair2"></i>
                        </button>
                    </div>
                </div>

                <!-- Route Strip -->
                <div class="route-strip">
                    <div class="route-row">
                        <div class="route-dot-col">
                            <span class="r-dot green"></span>
                            <span class="r-line"></span>
                            <span class="r-dot red"></span>
                        </div>
                        <div class="route-text-col">
                            <div>
                                <span class="route-label">Pickup</span>
                                <div class="route-place">{{ $trip->from_place ?? 'Pickup location' }}</div>
                            </div>
                            <div>
                                <span class="route-label">Destination</span>
                                <div class="route-place">{{ $trip->to_place ?? 'Drop location' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meta Chips -->
                <div class="meta-chips">
                    <div class="chip gold">
                        <i class="bi bi-currency-rupee"></i>
                        <div>
                            <span>₹{{ number_format($trip->fare ?? 0, 0) }}</span>
                            <span class="chip-label">Fare</span>
                        </div>
                    </div>
                    <div class="chip green-c">
                        <i class="bi bi-clock-fill"></i>
                        <div>
                            <span id="etaText">—</span>
                            <span class="chip-label">ETA</span>
                        </div>
                    </div>
                    <div class="chip blue-c">
                        <i class="bi bi-geo-fill"></i>
                        <div>
                            <span id="distText">—</span>
                            <span class="chip-label">Distance</span>
                        </div>
                    </div>
                </div>

                <!-- Passenger Coords (live) -->
                <div class="pax-coords" id="paxCoords" style="display:none">
                    <i class="bi bi-person-fill"></i>
                    <span id="paxCoordsText">Passenger: —</span>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Socket.IO -->
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

@php
    $coordinates = json_decode($trip->from_to_co ?? '{}');
@php
<script>
// ─── DATA FROM BLADE ──────────────────────────────────────────────────────
const TRIP_ID      = {{ $tripId ?? ($trip->id ?? 0) }};
const CUST_TOKEN   = "{{ $customerToken ?? '' }}";
const SOCKET_URL   = "https://node.goride.net.in";
// const TILE_URL     = "https://maps.g-ride.in/tile/{z}/{x}/{y}.png"; // your own OSM endpoint

// const TILE_URL = "https://maps.g-ride.in/{z}/{x}/{y}.png";
const TILE_URL = "https://maps.g-ride.in/osm/{z}/{x}/{y}.png";
// const TILE_URL = "https://maps.g-ride.in/styles/osm-bright/{z}/{x}/{y}.png";

// Passenger's last known position from server
const PAX_LAT      = {{ $passenger->lat ?? 13.0827 }};
const PAX_LNG      = {{ $passenger->lng ?? 80.2707 }};


// Pickup / drop coords (for markers)
const PICKUP_LAT   = {{ $coordinates->from_lat ?? 13.0827 }};
const PICKUP_LNG   = {{ $coordinates->from_lng ?? 80.2707 }};
const DROP_LAT     = {{ $coordinates->to_lat   ?? 13.0900 }};
const DROP_LNG     = {{ $coordinates->to_lng   ?? 80.2800 }};

// ─── MAP INIT ────────────────────────────────────────────────────────────
const map = L.map('map', {
    center: [PAX_LAT, PAX_LNG],
    zoom: 15,
    zoomControl: false,
    attributionControl: true,
});

L.tileLayer(TILE_URL, {
    attribution: '© <a href="https://openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19,
}).addTo(map);

// Add zoom control at bottom-right, above panel
L.control.zoom({ position: 'bottomright' }).addTo(map);

// ─── CUSTOM ICONS ────────────────────────────────────────────────────────
function makeIcon(svg, size = [36,36]) {
    return L.divIcon({
        html: svg,
        iconSize: size,
        iconAnchor: [size[0]/2, size[1]/2],
        popupAnchor: [0, -size[1]/2],
        className: '',
    });
}

const driverIcon = makeIcon(`
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40">
  <circle cx="20" cy="20" r="18" fill="#1a1a2e" stroke="#f6ba02" stroke-width="2.5"/>
  <text x="20" y="26" text-anchor="middle" font-size="18" fill="#f6ba02">🚗</text>
</svg>
`, [40,40]);

const paxIcon = makeIcon(`
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40">
  <circle cx="20" cy="20" r="18" fill="#1a1a2e" stroke="#448aff" stroke-width="2.5"/>
  <text x="20" y="26" text-anchor="middle" font-size="18" fill="#448aff">🧑</text>
</svg>
`, [40,40]);

const pickupIcon = makeIcon(`
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 40">
  <path d="M16 0C7.16 0 0 7.16 0 16c0 11 16 24 16 24S32 27 32 16C32 7.16 24.84 0 16 0z" fill="#00e676"/>
  <circle cx="16" cy="16" r="6" fill="#fff"/>
</svg>
`, [32,40]);

const dropIcon = makeIcon(`
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 40">
  <path d="M16 0C7.16 0 0 7.16 0 16c0 11 16 24 16 24S32 27 32 16C32 7.16 24.84 0 16 0z" fill="#ff5252"/>
  <circle cx="16" cy="16" r="6" fill="#fff"/>
</svg>
`, [32,40]);

// ─── MARKERS ─────────────────────────────────────────────────────────────
const driverMarker = L.marker([PAX_LAT, PAX_LNG], { icon: driverIcon }).addTo(map);
const paxMarker    = L.marker([PAX_LAT, PAX_LNG], { icon: paxIcon    }).addTo(map)
                      .bindPopup('Passenger');

L.marker([PICKUP_LAT, PICKUP_LNG], { icon: pickupIcon }).addTo(map).bindPopup('Pickup');
L.marker([DROP_LAT,   DROP_LNG  ], { icon: dropIcon   }).addTo(map).bindPopup('Destination');

// ─── HELPERS ─────────────────────────────────────────────────────────────
function centerMap() {
    map.setView(driverMarker.getLatLng(), 15);
}

function showToast(icon, iconClass, title, sub, duration = 4000) {
    const t = document.getElementById('toast');
    document.getElementById('toastIcon').innerHTML  = `<i class="bi bi-${icon}"></i>`;
    document.getElementById('toastIcon').className  = `toast-icon ${iconClass}`;
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastSub').textContent   = sub;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), duration);
}

function haversineKm(lat1, lng1, lat2, lng2) {
    const R = 6371, dLat = (lat2-lat1)*Math.PI/180, dLng = (lng2-lng1)*Math.PI/180;
    const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}

function updateChips(dLat, dLng) {
    const km  = haversineKm(dLat, dLng, DROP_LAT, DROP_LNG);
    const eta = Math.round(km / 0.4);          // rough 24 km/h avg in city
    document.getElementById('distText').textContent = km >= 1 ? km.toFixed(1)+' km' : Math.round(km*1000)+' m';
    document.getElementById('etaText').textContent  = eta < 1 ? '<1 min' : eta+' min';
}

// ─── SOCKET.IO ───────────────────────────────────────────────────────────
const socket = io(SOCKET_URL, {
    transports: ['websocket'],
    query: {
        token: CUST_TOKEN,
        platform: 'development',
        user_type: 'customer',
    },
});

socket.on('connect', () => {
    document.getElementById('statusPill').classList.add('connected');
    document.getElementById('statusText').textContent = 'Live';
    socket.emit('join_trip', { trip_id: TRIP_ID });
    document.getElementById('mapOverlay').classList.add('hidden');
});

socket.on('disconnect', () => {
    document.getElementById('statusPill').classList.remove('connected');
    document.getElementById('statusText').textContent = 'Reconnecting…';
    showToast('wifi-off', 'gold-bg', 'Connection lost', 'Trying to reconnect…');
});

socket.on('joined_trip', (data) => {
    console.log('Joined trip', data);
});

socket.on('driver_location', (data) => {
    const lat = parseFloat(data.lat);
    const lng = parseFloat(data.lng);
    driverMarker.setLatLng([lat, lng]);
    map.panTo([lat, lng], { animate: true, duration: 0.8 });
    updateChips(lat, lng);
});

socket.on('driver_arrived', () => {
    showToast('car-front-fill', 'green-bg', 'Driver has arrived!', 'Your driver is waiting at the pickup point.', 6000);
});

socket.on('trip_started', () => {
    showToast('play-circle-fill', 'gold-bg', 'Trip started', 'Enjoy your ride! You are on your way.', 5000);
});

socket.on('trip_completed', () => {
    showToast('check-circle-fill', 'green-bg', 'Trip completed', 'You have arrived at your destination.', 8000);
    setTimeout(() => {
        window.location.href = "{{ route('trip.completed', $tripId ?? 0) ?? '#' }}";
    }, 6000);
});

socket.on('error', (err) => console.error('Socket error:', err));

// ─── PASSENGER MARKER (from server-side coords) ───────────────────────
if (PAX_LAT && PAX_LNG) {
    paxMarker.setLatLng([PAX_LAT, PAX_LNG]);
    const paxDiv = document.getElementById('paxCoords');
    paxDiv.style.display = 'flex';
    document.getElementById('paxCoordsText').textContent =
        `Passenger: ${PAX_LAT.toFixed(5)}, ${PAX_LNG.toFixed(5)}`;
}

// Hide overlay after 6s fallback
setTimeout(() => document.getElementById('mapOverlay').classList.add('hidden'), 6000);
</script>

</body>
</html>