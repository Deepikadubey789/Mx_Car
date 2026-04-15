@extends(CarRentalsHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #fleet-map { height: 600px; border-radius: 1rem; border: 1px solid var(--soft-card-border); }
    .map-card { background: var(--soft-card-bg); padding: 1.5rem; border-radius: 1rem; }
</style>

<div class="map-card shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="ti ti-map-pin-cog text-primary me-2"></i>Live Fleet Tracking</h5>
        <span class="badge bg-success-lt" id="refresh-indicator">Live Updates Active</span>
    </div>
    <div id="fleet-map"></div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        const map = L.map('fleet-map').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let markers = {};

        // Helper function to create a colored circular marker
        function createCustomIcon(color) {
            return L.divIcon({
                className: 'custom-div-icon',
                html: `<div style="
                    background-color: ${color}; 
                    width: 16px; 
                    height: 16px; 
                    border-radius: 50%; 
                    border: 3px solid white; 
                    box-shadow: 0 0 10px rgba(0,0,0,0.5);
                "></div>`,
                iconSize: [20, 20],
                iconAnchor: [10, 10], // Center the icon
                popupAnchor: [0, -10]
            });
        }

        function updateLocations() {
            fetch('{{ route("car-rentals.vendor.fleet-locations") }}')
                .then(res => res.json())
                .then(data => {
                    data.forEach(car => {
                        const popupContent = `
                            <strong>${car.name}</strong><br>
                            Plate: ${car.plate}<br>
                            Speed: ${car.speed} mph<br>
                            Status: <span style="color: ${car.status_color}; font-weight: bold;">${car.event}</span><br>
                            <small>Last ping: ${car.last_ping}</small>
                        `;

                        // Create the specific colored icon for this car
                        const icon = createCustomIcon(car.status_color);

                        if (markers[car.id]) {
                            // Update position, popup, AND the icon color
                            markers[car.id].setLatLng([car.lat, car.lng])
                                         .setPopupContent(popupContent)
                                         .setIcon(icon);
                        } else {
                            // Create new marker with the colored icon
                            markers[car.id] = L.marker([car.lat, car.lng], { icon: icon })
                                             .addTo(map)
                                             .bindPopup(popupContent);
                        }
                    });

                    // Fit bounds to show all cars on first load
                    if (data.length > 0 && Object.keys(markers).length === data.length) {
                        const group = new L.featureGroup(Object.values(markers));
                        map.fitBounds(group.getBounds().pad(0.1));
                    }
                })
                .catch(error => console.error("Error fetching fleet locations:", error));
        }

        // Initial load
        updateLocations();
        
        // Refresh every 10 seconds
        setInterval(updateLocations, 10000); 
    });
</script>
@stop