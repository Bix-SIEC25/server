<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wristbands Dashboard</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel="icon" type="image/x-icon" href="./favicon.png">
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <div class="container">
        <h1>Wristband Gateway Dashboard</h1>

        <div class="card" style="margin-bottom: 20px;">
            <div class="filter-section">
                <label for="resident-filter" style="font-weight: 600; color: #667eea;">Filter by Resident:</label>
                <select id="resident-filter" class="filter-select" onchange="filterByResident()">
                    <option value="">All Residents</option>
                </select>
                <button class="refresh-btn" onclick="loadAllData()">Refresh All</button>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Vital Signs Section -->
            <div class="card">
                <h2>Vital Signs</h2>
                <button class="refresh-btn" onclick="loadVitals()">Refresh</button>
                <div id="vitals-content" class="loading">Loading...</div>
            </div>

            <!-- Fall Alerts Section -->
            <div class="card">
                <h2>Fall Alerts</h2>
                <button class="refresh-btn" onclick="loadFalls()">Refresh</button>
                <div id="falls-content" class="loading">Loading...</div>
            </div>
        </div>
    </div>

    <script>
        let selectedDeviceId = '';
        let residents = [];

        // Load residents list
        async function loadResidents() {
            try {
                // $('#lobby').load('includes/updt/lobby.php');

                const response = await fetch('./raw_residents');
                residents = await response.json();

                const select = document.getElementById('resident-filter');
                residents.forEach(resident => {
                    const option = document.createElement('option');
                    option.value = resident.device_id;
                    option.textContent = `${resident.name} (Device ${resident.device_id})`;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading residents:', error);
            }
        }

        // Filter by resident
        function filterByResident() {
            const select = document.getElementById('resident-filter');
            selectedDeviceId = select.value;
            loadAllData();
        }

        // Load all data
        function loadAllData() {
            loadVitals();
            loadFalls();
        }

        // Load vitals data
        async function loadVitals() {
            const url = selectedDeviceId ? `vitals_section.php?devid=${selectedDeviceId}` : 'vitals_section.php';
            $('#vitals-content').load(url);

            // const container = document.getElementById('vitals-content');
            // container.innerHTML = '<div class="loading">Loading...</div>';

            // try {
            //     const url = selectedDeviceId ? `/api/vitals?device_id=${selectedDeviceId}` : '/api/vitals';
            //     const response = await fetch(url);
            //     const data = await response.json();

            //     if (data.length === 0) {
            //         container.innerHTML = '<div class="no-data">No vital signs data available</div>';
            //         return;
            //     }

            //     let html = '<table><thead><tr>';
            //     html += '<th>Resident</th><th>SpO2</th><th>Heart Rate</th><th>Board Temperature</th><th>Timestamp</th>';
            //     html += '</tr></thead><tbody>';

            //     data.forEach(record => {
            //         const spo2Status = record.spo2 < 90 ? 'badge-danger' : record.spo2 < 95 ? 'badge-warning' : 'badge-success';
            //         const hrStatus = record.heart_rate < 60 || record.heart_rate > 100 ? 'badge-warning' : 'badge-success';

            //         html += '<tr>';
            //         html += `<td><strong>${record.name || 'Unknown'}</strong><span class="resident-badge">${record.device_id}</span></td>`;
            //         html += `<td><span class="badge ${spo2Status}">${record.spo2}%</span></td>`;
            //         html += `<td><span class="badge ${hrStatus}">${record.heart_rate} BPM</span></td>`;
            //         html += `<td>${record.temperature ? record.temperature + '°C' : 'N/A'}</td>`;
            //         html += `<td>${new Date(record.timestamp).toLocaleString()}</td>`;
            //         html += '</tr>';
            //     });

            //     html += '</tbody></table>';
            //     container.innerHTML = html;

            // } catch (error) {
            //     container.innerHTML = '<div class="no-data">Error loading data</div>';
            //     console.error('Error:', error);
            // }
        }

        // Load fall alerts
        async function loadFalls() {
            const url = selectedDeviceId ? `falls_section.php?devid=${selectedDeviceId}` : 'falls_section.php';
            $('#falls-content').load(url);

            // const container = document.getElementById('falls-content');
            // container.innerHTML = '<div class="loading">Loading...</div>';

            // try {
            //     const url = selectedDeviceId ? `/api/falls?device_id=${selectedDeviceId}` : '/api/falls';
            //     const response = await fetch(url);
            //     const data = await response.json();

            //     if (data.length === 0) {
            //         container.innerHTML = '<div class="no-data">No fall alerts</div>';
            //         return;
            //     }

            //     const currentTime = new Date();
            //     const tenMinutesAgo = new Date(currentTime.getTime() - 10 * 60 * 1000);

            //     let html = '<table><thead><tr>';
            //     html += '<th>Resident</th><th>Jerk Magnitude</th><th>Timestamp</th>';
            //     html += '</tr></thead><tbody>';

            //     data.forEach(record => {
            //         const severity = record.jerkmagnitude > 5 ? 'badge-danger' : record.jerkmagnitude > 3 ? 'badge-warning' : 'badge-success';
            //         const fallTime = new Date(record.timestamp);
            //         const isRecent = fallTime > tenMinutesAgo;

            //         html += `<tr${isRecent ? ' class="recent-fall"' : ''}>`;
            //         html += `<td><strong>${record.name || 'Unknown'}</strong><span class="resident-badge">${record.device_id}</span></td>`;
            //         html += `<td><span class="badge ${severity}">${record.jerkmagnitude.toFixed(2)}</span></td>`;
            //         html += `<td>${new Date(record.timestamp).toLocaleString()}${isRecent ? ' 🚨' : ''}</td>`;
            //         html += '</tr>';
            //     });

            //     html += '</tbody></table>';
            //     container.innerHTML = html;

            // } catch (error) {
            //     container.innerHTML = '<div class="no-data">Error loading data</div>';
            //     console.error('Error:', error);
            // }
        }

        // Auto-refresh every 10 seconds
        setInterval(() => {
            loadVitals();
            loadFalls();
        }, 10000);

        // Initial load
        loadResidents();
        loadVitals();
        loadFalls();
    </script>
</body>

</html>