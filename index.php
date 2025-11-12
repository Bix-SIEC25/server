<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wristband Gateway Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        h1 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(600px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .card h2 {
            color: #667eea;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card h2::before {
            content: '';
            width: 4px;
            height: 24px;
            background: #667eea;
            border-radius: 2px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        tr:hover {
            background: #f8f9ff;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .badge-danger {
            background: #fee;
            color: #c33;
        }
        
        .badge-warning {
            background: #ffeaa7;
            color: #d63031;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .refresh-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .refresh-btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .no-data {
            text-align: center;
            color: #999;
            padding: 40px;
            font-style: italic;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #667eea;
        }
        
        .filter-section {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .filter-select {
            padding: 10px 15px;
            border: 2px solid #667eea;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            background: white;
            color: #333;
            min-width: 200px;
        }
        
        .filter-select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        
        .resident-badge {
            display: inline-block;
            padding: 4px 8px;
            background: #667eea;
            color: white;
            border-radius: 12px;
            font-size: 0.8em;
            margin-left: 5px;
        }
        
        /* Animation for recent falls */
        .recent-fall {
            animation: blink-red 1s ease-in-out infinite;
        }
        
        @keyframes blink-red {
            0%, 100% {
                background-color: #ff4444;
                opacity: 1;
            }
            50% {
                background-color: #ff0000;
                opacity: 0.7;
            }
        }
        
        .recent-fall td {
            color: white;
            font-weight: bold;
        }
        
        .recent-fall:hover {
            background-color: #ff4444 !important;
        }
    </style>
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
                const response = await fetch('/api/residents');
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
            const container = document.getElementById('vitals-content');
            container.innerHTML = '<div class="loading">Loading...</div>';
            
            try {
                const url = selectedDeviceId ? `/api/vitals?device_id=${selectedDeviceId}` : '/api/vitals';
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.length === 0) {
                    container.innerHTML = '<div class="no-data">No vital signs data available</div>';
                    return;
                }
                
                let html = '<table><thead><tr>';
                html += '<th>Resident</th><th>SpO2</th><th>Heart Rate</th><th>Board Temperature</th><th>Timestamp</th>';
                html += '</tr></thead><tbody>';
                
                data.forEach(record => {
                    const spo2Status = record.spo2 < 90 ? 'badge-danger' : record.spo2 < 95 ? 'badge-warning' : 'badge-success';
                    const hrStatus = record.heart_rate < 60 || record.heart_rate > 100 ? 'badge-warning' : 'badge-success';
                    
                    html += '<tr>';
                    html += `<td><strong>${record.name || 'Unknown'}</strong><span class="resident-badge">${record.device_id}</span></td>`;
                    html += `<td><span class="badge ${spo2Status}">${record.spo2}%</span></td>`;
                    html += `<td><span class="badge ${hrStatus}">${record.heart_rate} BPM</span></td>`;
                    html += `<td>${record.temperature ? record.temperature + '°C' : 'N/A'}</td>`;
                    html += `<td>${new Date(record.timestamp).toLocaleString()}</td>`;
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                container.innerHTML = html;
                
            } catch (error) {
                container.innerHTML = '<div class="no-data">Error loading data</div>';
                console.error('Error:', error);
            }
        }
        
        // Load fall alerts
        async function loadFalls() {
            const container = document.getElementById('falls-content');
            container.innerHTML = '<div class="loading">Loading...</div>';
            
            try {
                const url = selectedDeviceId ? `/api/falls?device_id=${selectedDeviceId}` : '/api/falls';
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.length === 0) {
                    container.innerHTML = '<div class="no-data">No fall alerts</div>';
                    return;
                }
                
                const currentTime = new Date();
                const tenMinutesAgo = new Date(currentTime.getTime() - 10 * 60 * 1000);
                
                let html = '<table><thead><tr>';
                html += '<th>Resident</th><th>Jerk Magnitude</th><th>Timestamp</th>';
                html += '</tr></thead><tbody>';
                
                data.forEach(record => {
                    const severity = record.jerkmagnitude > 5 ? 'badge-danger' : record.jerkmagnitude > 3 ? 'badge-warning' : 'badge-success';
                    const fallTime = new Date(record.timestamp);
                    const isRecent = fallTime > tenMinutesAgo;
                    
                    html += `<tr${isRecent ? ' class="recent-fall"' : ''}>`;
                    html += `<td><strong>${record.name || 'Unknown'}</strong><span class="resident-badge">${record.device_id}</span></td>`;
                    html += `<td><span class="badge ${severity}">${record.jerkmagnitude.toFixed(2)}</span></td>`;
                    html += `<td>${new Date(record.timestamp).toLocaleString()}${isRecent ? ' 🚨' : ''}</td>`;
                    html += '</tr>';
                });
                
                html += '</tbody></table>';
                container.innerHTML = html;
                
            } catch (error) {
                container.innerHTML = '<div class="no-data">Error loading data</div>';
                console.error('Error:', error);
            }
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