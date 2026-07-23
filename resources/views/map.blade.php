<!DOCTYPE html>
<html>
<head>
    <title>Track Order #{{ $order->id }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/maplibre-gl@3.6.2/dist/maplibre-gl.css" />
    <script src="https://cdn.jsdelivr.net/npm/maplibre-gl@3.6.2/dist/maplibre-gl.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f4f6f8;
            color: #1e293b;
        }

        html, body, #map {
            height: 100%;
            width: 100%;
        }

        .routing-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 10;
            width: 360px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 16px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            max-height: calc(100% - 40px);
            overflow-y: auto;
            box-sizing: border-box;
        }

        .custom-marker {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 3px solid #ffffff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            cursor: pointer;
            box-sizing: border-box;
        }

        /* Pulsing current location dot for shipper */
        .shipper-marker {
            width: 22px;
            height: 22px;
            background-color: #0f766e; /* Teal */
            border: 3px solid #ffffff;
            border-radius: 50%;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            box-sizing: border-box;
            position: relative;
        }

        .shipper-marker::after {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            border-radius: 50%;
            border: 3px solid #0f766e;
            animation: shipper-pulse 1.8s infinite ease-out;
        }

        @keyframes shipper-pulse {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(2.8); opacity: 0; }
        }

        .recenter-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
            width: 44px;
            height: 44px;
            background-color: #ffffff;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            transition: all 0.2s ease;
        }

        .recenter-btn:hover {
            color: #0f766e;
            transform: scale(1.05);
        }

        .badge-status {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .copy-link-btn {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .copy-link-btn:hover {
            background-color: #e2e8f0;
        }

        @media (max-width: 600px) {
            .routing-panel {
                top: auto;
                bottom: 20px;
                left: 10px;
                right: 10px;
                width: calc(100% - 20px);
                max-height: 50%;
            }
        }
    </style>
</head>
<body>
    <div id="map"></div>

    <button id="recenter-btn" class="recenter-btn" title="Recenter route">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line></svg>
    </button>

    <div class="routing-panel">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h4 class="fw-bold text-dark m-0">Delivery Details</h4>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm py-1 fw-semibold">Back</a>
        </div>

        <div class="card border-0 bg-light p-3 rounded-3">
            <div class="mb-2">
                <span class="text-muted small">Order ID:</span>
                <strong class="text-dark">#{{ $order->id }}</strong>
            </div>
            <div class="mb-2">
                <span class="text-muted small">Product:</span>
                <strong class="text-dark">{{ $order->product->name ?? 'Product' }} x{{ $order->quantity }}</strong>
            </div>
            <div class="mb-2">
                <span class="text-muted small">Customer:</span>
                <strong class="text-dark">{{ $order->customer->name ?? 'N/A' }}</strong>
            </div>
            <div class="mb-0">
                <span class="text-muted small">Delivery Address:</span>
                <span class="text-dark d-block small fw-medium mt-1">{{ $order->delivery_address }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between my-2">
            <span class="text-muted small fw-semibold">Order Status:</span>
            <span id="order-status-badge" class="badge-status bg-primary text-white">
                @if($order->status === 'waiting')
                    Waiting
                @elseif($order->status === 'accepted')
                    Accepted
                @elseif($order->status === 'delivering')
                    Delivering
                @elseif($order->status === 'completed')
                    Completed
                @else
                    {{ ucfirst($order->status) }}
                @endif
            </span>
        </div>

        <div id="route-details" class="card border-0 bg-light p-3 rounded-3">
            <h6 class="fw-bold text-secondary mb-2 small text-uppercase letter-spacing-5">Route Details</h6>
            <div class="row g-2 text-center">
                <div class="col-6">
                    <div class="bg-white p-2 rounded border border-light">
                        <span class="text-muted d-block small">Distance</span>
                        <strong id="route-distance" class="text-primary">-</strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="bg-white p-2 rounded border border-light">
                        <span class="text-muted d-block small">Duration</span>
                        <strong id="route-time" class="text-primary">-</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Share Link -->
        <div class="mb-2">
            <label class="form-label small text-muted fw-bold">Share live tracking link</label>
            <div class="input-group">
                <input type="text" id="share-link-input" class="form-control form-control-sm" readonly>
                <button class="btn btn-sm btn-outline-secondary copy-link-btn" type="button" id="btn-copy-link" title="Copy link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a1 1 0 1 1 2 0v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1a1 1 0 1 1 0 2z"/>
                    </svg>
                </button>
            </div>
        </div>

        @if(Auth::check() && Auth::id() === $order->shipper_id && $order->status !== 'completed')
            <div class="mt-2">
                <form action="{{ route('orders.complete', $order->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                        Complete Delivery
                    </button>
                </form>
            </div>
        @endif
    </div>

    <script>
        const apiUrl = 'https://rsapi.goong.io';
        const mapUrl = 'https://tiles.goong.io/assets/';
        const apiKey = '9eQP8x4c9ulw5j1ycK7iAyTejVvUmHS7T9opYswn';
        const mapKey = 'NDYPjCSusn6YSEozAla1mgcP7pTXoFU3tIamrb9M';

        const orderId = @json($order->id);
        const isShipper = @json(Auth::check() && Auth::id() === $order->shipper_id);
        const csrfToken = @json(csrf_token());

        // Delivery coordinates (end point)
        const deliveryLat = @json($order->delivery_lat);
        const deliveryLng = @json($order->delivery_lng);
        const deliveryCoords = [deliveryLng, deliveryLat];

        // Shipper coordinates (start point)
        let shipperLat = @json($order->shipper_lat);
        let shipperLng = @json($order->shipper_lng);
        let shipperCoords = shipperLat && shipperLng ? [shipperLng, shipperLat] : null;

        const zoom = 15;
        let map = null;
        let shipperMarker = null;
        let customerMarker = null;
        let activePopups = [];
        let currentRouteCoords = null;
        let watchId = null;

        document.addEventListener('DOMContentLoaded', function () {
            // Set shareable link input value
            const shareInput = document.getElementById('share-link-input');
            if (shareInput) {
                shareInput.value = window.location.href;
            }

            // Copy link event listener
            const copyBtn = document.getElementById('btn-copy-link');
            if (copyBtn) {
                copyBtn.addEventListener('click', function () {
                    const input = document.getElementById('share-link-input');
                    input.select();
                    input.setSelectionRange(0, 99999);
                    navigator.clipboard.writeText(input.value);
                    
                    const btn = this;
                    const originalHTML = btn.innerHTML;
                    btn.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="green" viewBox="0 0 16 16">
                          <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                        </svg>
                    `;
                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                    }, 2000);
                });
            }

            if (typeof maplibregl === 'undefined') {
                console.error('MapLibre GL library failed to load.');
                alert('Không thể tải thư viện bản đồ MapLibre GL. Vui lòng làm mới trang.');
                return;
            }

            // Initialize Goong Map
            const mapCenter = shipperCoords ? shipperCoords : deliveryCoords;
            map = new maplibregl.Map({
                container: 'map',
                style: `${mapUrl}goong_map_web.json?api_key=${mapKey}`,
                center: mapCenter,
                zoom: zoom
            });

            map.on('load', function () {
            // Add Customer/End Marker
            const customerEl = document.createElement('div');
            customerEl.className = 'custom-marker';
            customerEl.style.backgroundColor = '#f43f5e'; // Rose Red
            customerMarker = new maplibregl.Marker({ element: customerEl })
                .setLngLat(deliveryCoords)
                .addTo(map);

            // Add Shipper/Start Marker
            if (shipperCoords) {
                const shipperEl = document.createElement('div');
                shipperEl.className = 'shipper-marker';
                shipperMarker = new maplibregl.Marker({ element: shipperEl })
                    .setLngLat(shipperCoords)
                    .addTo(map);

                // Fetch initial route
                fetchDirections(`${shipperLat},${shipperLng}`, `${deliveryLat},${deliveryLng}`, true);
            } else {
                map.flyTo({ center: deliveryCoords, zoom: zoom });
            }

            // Differentiate watcher vs poller/realtime listener
            if (isShipper) {
                startWatchingLocation();
            } else {
                startRealtimeListener();
            }
        });

        // Fetch directions via Goong API
        function fetchDirections(sCoords, eCoords, shouldRecenter = false) {
            const apiLink = `${apiUrl}/Direction?origin=${sCoords}&destination=${eCoords}&vehicle=bike&api_key=${apiKey}`;

            fetch(apiLink)
                .then(response => response.json())
                .then(data => {
                    if (data.routes && data.routes.length > 0) {
                        const route = data.routes[0].overview_polyline.points;
                        const distance = data.routes[0].legs[0].distance.text;
                        const time = data.routes[0].legs[0].duration.text;
                        const decodedRoute = decodePolyline(route);
                        displayRoute(decodedRoute, distance, time, shouldRecenter);
                    }
                })
                .catch(error => {
                    console.error('Error fetching directions:', error);
                });
        }

        // Decode Goong Polyline
        function decodePolyline(encoded) {
            var points = [];
            var index = 0, len = encoded.length;
            var lat = 0, lng = 0;

            while (index < len) {
                var b, shift = 0, result = 0;
                do {
                    b = encoded.charAt(index++).charCodeAt(0) - 63;
                    result |= (b & 0x1f) << shift;
                    shift += 5;
                } while (b >= 0x20);
                var dlat = ((result & 1) ? ~(result >> 1) : (result >> 1));
                lat += dlat;

                shift = 0;
                result = 0;
                do {
                    b = encoded.charAt(index++).charCodeAt(0) - 63;
                    result |= (b & 0x1f) << shift;
                    shift += 5;
                } while (b >= 0x20);
                var dlng = ((result & 1) ? ~(result >> 1) : (result >> 1));
                lng += dlng;

                points.push([lng * 1e-5, lat * 1e-5]);
            }
            return points;
        }

        // Display route line on map
        function displayRoute(route, distance, time, shouldRecenter = false) {
            currentRouteCoords = route;

            if (map.getSource('route')) {
                map.removeLayer('route');
                map.removeSource('route');
            }

            map.addSource('route', {
                'type': 'geojson',
                'data': {
                    'type': 'Feature',
                    'properties': {},
                    'geometry': {
                        'type': 'LineString',
                        'coordinates': route
                    }
                }
            });

            map.addLayer({
                'id': 'route',
                'type': 'line',
                'source': 'route',
                'layout': {
                    'line-join': 'round',
                    'line-cap': 'round'
                },
                'paint': {
                    'line-color': '#0f766e',
                    'line-width': 6,
                    'line-opacity': 0.85
                }
            });

            // Update route specs
            document.getElementById('route-distance').textContent = distance;
            document.getElementById('route-time').textContent = time;

            // Reset midpoint popups
            activePopups.forEach(p => p.remove());
            activePopups = [];
            
            const midPoint = route[Math.floor(route.length / 2)];
            const popup = new maplibregl.Popup({ closeButton: false, offset: 15 })
                .setLngLat(midPoint)
                .setHTML(`
                    <div style="font-family: inherit; font-size: 0.75rem; padding: 2px 4px; line-height: 1.3;">
                        <span style="color:#0f766e; font-weight:700;">Delivery Route</span><br>
                        Remaining: <strong>${distance}</strong> (~${time})
                    </div>
                `)
                .addTo(map);
            activePopups.push(popup);

            if (shouldRecenter) {
                recenterMap();
            }
        }

        // Recenter Map Click Handler
        document.getElementById('recenter-btn').addEventListener('click', recenterMap);
        function recenterMap() {
            if (currentRouteCoords && currentRouteCoords.length > 0) {
                map.fitBounds(currentRouteCoords.reduce(function (bounds, coord) {
                    return bounds.extend(coord);
                }, new maplibregl.LngLatBounds(currentRouteCoords[0], currentRouteCoords[0])), {
                    padding: 50
                });
            } else {
                map.flyTo({ center: deliveryCoords, zoom: zoom });
            }
        }

        // ================= SHIPPER FLOW (WATCH & UPDATE) =================
        function startWatchingLocation() {
            if (!navigator.geolocation) {
                console.warn('Geolocation is not supported by this browser.');
                return;
            }

            watchId = navigator.geolocation.watchPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    if (shipperLat !== lat || shipperLng !== lng) {
                        shipperLat = lat;
                        shipperLng = lng;
                        shipperCoords = [lng, lat];

                        if (shipperMarker) {
                            shipperMarker.setLngLat(shipperCoords);
                        } else {
                            const shipperEl = document.createElement('div');
                            shipperEl.className = 'shipper-marker';
                            shipperMarker = new maplibregl.Marker({ element: shipperEl })
                                .setLngLat(shipperCoords)
                                .addTo(map);
                        }

                        // Redraw route dynamically without map flying
                        fetchDirections(`${lat},${lng}`, `${deliveryLat},${deliveryLng}`, false);

                        // Save coordinate updates on server
                        postLocationUpdate(lat, lng);
                    }
                },
                function (error) {
                    console.error('Error getting location dynamically:', error);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 8000,
                    maximumAge: 0
                }
            );
        }

        function postLocationUpdate(lat, lng) {
            const apiUpdateUrl = `/orders/${orderId}/update-location`;
            fetch(apiUpdateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    shipper_lat: lat,
                    shipper_lng: lng
                })
            })
            .then(res => res.json())
            .then(data => {
                console.log('Location updated on server successfully:', data);
            })
            .catch(err => {
                console.error('Failed to post location to server:', err);
            });
        }

        // ================= CUSTOMER / GUEST FLOW (REALTIME REVERB ECHO + POLLING FALLBACK) =================
        function startRealtimeListener() {
            if (typeof window.Echo !== 'undefined') {
                console.log('Listening for realtime location updates on channel: order.' + orderId);
                window.Echo.channel(`order.${orderId}`)
                    .listen('.ShipperLocationUpdated', function (data) {
                        console.log('Realtime location update received:', data);
                        updateMapWithOrderData(data);
                    })
                    .listen('ShipperLocationUpdated', function (data) {
                        console.log('Realtime location update received:', data);
                        updateMapWithOrderData(data);
                    });
            } else {
                console.warn('Laravel Echo is not available. Falling back to HTTP polling.');
                startPollingLocation();
            }
        }

        function updateMapWithOrderData(orderData) {
            // Update status badge
            const statusBadge = document.getElementById('order-status-badge');
            if (statusBadge && orderData.status) {
                let statusText = orderData.status;
                if (orderData.status === 'waiting') statusText = 'Waiting';
                else if (orderData.status === 'accepted') statusText = 'Accepted';
                else if (orderData.status === 'delivering') statusText = 'Delivering';
                else if (orderData.status === 'completed') statusText = 'Completed';
                statusBadge.textContent = statusText;
            }

            // Update shipper location
            const newLat = parseFloat(orderData.shipper_lat);
            const newLng = parseFloat(orderData.shipper_lng);

            if (newLat && newLng && (shipperLat !== newLat || shipperLng !== newLng)) {
                shipperLat = newLat;
                shipperLng = newLng;
                shipperCoords = [newLng, newLat];

                if (shipperMarker) {
                    shipperMarker.setLngLat(shipperCoords);
                } else {
                    const shipperEl = document.createElement('div');
                    shipperEl.className = 'shipper-marker';
                    shipperMarker = new maplibregl.Marker({ element: shipperEl })
                        .setLngLat(shipperCoords)
                        .addTo(map);
                }

                // Redraw route dynamically
                fetchDirections(`${newLat},${newLng}`, `${deliveryLat},${deliveryLng}`, false);
            }
        }

        function startPollingLocation() {
            setInterval(function () {
                const apiGetDataUrl = `/api/orders/${orderId}`;
                fetch(apiGetDataUrl)
                    .then(res => res.json())
                    .then(orderData => {
                        updateMapWithOrderData(orderData);
                    })
                    .catch(err => {
                        console.error('Failed to poll shipper location:', err);
                    });
            }, 5000);
        }
        });
    </script>
</body>
</html>
