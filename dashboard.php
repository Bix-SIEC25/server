<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Bix - EHPAD Patrol - Demo Dashboard</title>

    <!-- Google Fonts (match index) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Source+Serif+4:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

    <link rel="icon" type="image/x-icon" href="./favicon.png">
    <link rel="stylesheet" href="dashboard.css">
    <!-- <link rel="stylesheet" href="index.css"> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
</head>

<body>
    <!-- Decorative floating/background squares (non-interactive) -->
    <div class="bg-decor" aria-hidden="true">
        <!-- top-left cluster -->
        <div class="sq sq--tl-1 fC"></div>
        <div class="sq sq--tl-2 fA"></div>
        <div class="sq sq--tl-3 outline fB"></div>

        <!-- left mid -->
        <div class="sq sq--l-1 fA"></div>
        <div class="sq sq--l-2 outline fC"></div>

        <!-- right cluster -->
        <div class="sq sq--r-1 fB"></div>
        <div class="sq sq--r-2 outline fA"></div>
        <div class="sq sq--r-3 fC"></div>

        <!-- bottom -->
        <div class="sq sq--b-1 fB"></div>
        <div class="sq sq--b-2 outline fA"></div>

        <!-- scattered -->
        <div class="sq sq--s-1 fC"></div>
        <div class="sq sq--s-2 outline fB"></div>
    </div>

    <header>
        <div class="nav container">
            <div class="brand">
                <div class="logo" aria-hidden="true">
                    <img src="./logo.png" width="100%" height="100%" alt="Bix">
                </div>
                <div class="brand-text">
                    <div style="font-weight:800">Bix</div>
                    <div class="small muted">EHPAD Patrol - Demo Dashboard</div>
                </div>
            </div>

            <nav aria-label="Main navigation">
                <a href="./">Home</a>
                <a class="cta" href="./dashboard">Dashboard demo</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="hero-grid" aria-label="Dashboard main">
            <!-- left column: Hero / controls -->
            <div class="hero-left">
                <div class="eyebrow">Autonomous · Safe · Caring</div>
                <h1 class="title">EHPAD Patrol - Live demo</h1>
                <p class="subtitle">Demonstration dashboard.</p>

                <div class="hero-ctas">
                    <a class="btn primary" href="#contact" aria-label="Contact for investors">▶ Request a demo</a>
                    <a class="btn ghost" href="#about">Learn more</a>
                </div>
                <div class="card" style="margin-top:18px">
                    <h3 style="margin:0 0 8px 0">Live photo</h3>
                    <p class="small muted">Live photo from the Bix's car.</p>
                    <div class="media-wrap" id="livephoto-wrap">
                        <img id="live-photo" src="uploads/last.jpg" alt="last snapshot" />
                    </div>
                    <!-- <div class="small muted" style="margin-top:8px">If the snapshot doesn't appear, it does not exists on the server lol</div> -->
                </div>
            </div>

            <!-- right column: vitals + falls + fall visual -->
            <aside class="hero-right" aria-hidden="false">
                <div class="right-stack">
                    <!-- Fall visual / preview (hidden by default) -->
                    <div class="card fall-visual-card" id="fall-visual">
                        <h3 style="margin:0 0 10px 0">Fall visualization</h3>
                        <div id="fall-visual-content" class="no-data">No fall to display</div>
                    </div>

                    <div class="card overview-card">
                        <h3 style="margin:0 0 8px 0">Controls</h3>

                        <div class="filter-section">
                            <label for="resident-filter" style="font-weight: 600; color: var(--accent-500);">Filter by Resident:</label>
                            <select id="resident-filter" class="filter-select" onchange="filterByResident()">
                                <option value="">All Residents</option>
                            </select>
                            <button class="refresh-btn" onclick="loadAllData()">Refresh All</button>
                        </div>

                        <!-- <div class="small muted" style="margin-top:8px">
                        Trusted by caregivers - focused on reliability, privacy and human-centered design.
                    </div> -->
                    </div>

                    <div class="card" id="falls-card">
                        <div class="card-header">
                            <h2>Fall Alerts</h2>
                            <button class="refresh-btn" onclick="loadFalls()">Refresh</button>
                        </div>
                        <div id="falls-content" class="loading">Loading...</div>
                    </div>

                    <div class="card" id="vitals-card">
                        <div class="card-header">
                            <h2>Vital Signs</h2>
                            <button class="refresh-btn" onclick="loadVitals()">Refresh</button>
                        </div>
                        <div id="vitals-content" class="loading">Loading...</div>
                    </div>
                </div>
            </aside>
        </section>
    </main>

    <footer class="container" style="margin-top:40px;">
        <div class="small muted">© Bix - EHPAD Patrol - Demo dashboard</div>
    </footer>

    <script>
        let selectedDeviceId = '';
        let residents = [];

        // Load residents list
        async function loadResidents() {
            try {
                const response = await fetch('./raw_residents');
                residents = await response.json();

                const select = document.getElementById('resident-filter');
                // clear older options except first
                select.querySelectorAll('option:not(:first-child)').forEach(n => n.remove());

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
        }

        // Load fall alerts
        async function loadFalls() {
            const url = selectedDeviceId ? `falls_section.php?devid=${selectedDeviceId}` : 'falls_section.php';
            $('#falls-content').load(url);
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

        let socket;
        let tries = 0;

        const connect = function() {
            return new Promise((resolve, reject) => {
                const socketUrl = `wss://magictintin.fr/ws`;
                socket = new WebSocket(socketUrl);

                socket.onopen = (e) => {
                    resolve();
                    sendMsg("ping");
                    sendMsgChan("img");
                    sendMsgChan("admin");
                }

                socket.onmessage = (event) => {
                    const data = event.data;
                    console.log('websocket received', data);

                    // existing triggers reloads
                    if (data.includes("new vitals")) {
                        loadVitals();
                    } else if (data.includes("new fall")) {
                        loadFalls();
                    }

                    // update live photo when "img" is received
                    if (data === "newimg") {
                        updateLivePhoto();
                    }

                    // handle fall visualization message of form "visu_fall|Name"
                    if (data.startsWith("visu_fall")) {
                        const parts = data.split("|");
                        const name = parts[1] ? parts[1].trim() : "Unknown";
                        showFallVisualization(name);
                        // also reload falls list (optional)
                        loadFalls();
                    }
                }

                socket.onclose = (e) => {
                    console.log("WebSocket closed - reconnecting...");
                    // attempt reconnect after brief delay
                    setTimeout(() => connect(), 200);
                }

                socket.onerror = (e) => {
                    console.error("WebSocket error", e);
                    // resolve so page remains usable, and try reconnect a few times
                    resolve();
                    if (tries < 3) {
                        tries++;
                        setTimeout(() => connect(), 1000);
                    } else {
                        console.warn("WebSocket unable to stabilize; manual refresh recommended.");
                    }
                }
            });
        }

        // check if a websocket is open
        const isOpen = function(ws) {
            return ws && ws.readyState === ws.OPEN;
        }

        function sendMsg(message = 'ping') {
            if (isOpen(socket)) {
                socket.send(`bix/wristband:${message}`);
                console.log(`${message} sent to server (bix room, wristband group)`);
            }
        }

        function sendMsgChan(chan, message = 'ping') {
            if (isOpen(socket)) {
                socket.send(`bix/${chan}:${message}`);
                console.log(`${message} sent to server (bix room, ${chan} group)`);
            }
        }

        // update live-photo with cache busting
        function updateLivePhoto() {
            const wrp = document.getElementById('livephoto-wrap');
            const img = document.getElementById('live-photo');
            // force reload by adding timestamp query param
            const t = Date.now();
            img.src = `uploads/last.jpg?t=${t}`;
            img.onload = () => {
                // optionally add a subtle highlight when updated
                img.classList.add('just-updated-img');
                wrp.classList.add('just-updated');
                setTimeout(() => {
                    img.classList.remove('just-updated-img');
                    wrp.classList.remove('just-updated')
                }, 800);
            }
            img.onerror = () => {
                console.warn('Live photo not found at uploads/last.jpg');
            }
        }

        function showFallVisualization(residentName) {
            const container = document.getElementById('fall-visual');
            const content = document.getElementById('fall-visual-content');

            const previewImgPath = `uploads/visu_fall.jpg?t=${Date.now()}`;
            // create markup: image + overlay text + confirm/no buttons
            content.innerHTML = `
                <div class="media-wrap fall-preview-wrap">
                    <img id="fall-photo" src="${previewImgPath}" alt="fall preview" onerror="this.style.display='none';">
                </div>
                <div class="fall-meta">
                    <div class="alert-text"><strong>${escapeHtml(residentName)}</strong> has fallen.</div>
                    <div class="confirm-cta">
                        <div class="small muted">Do you confirm?</div>
                        <div class="confirm-buttons">
                            <button class="btn confirm" onclick="confirmFall('${escapeHtml(residentName)}')">Confirm</button>
                            <button class="btn ghost" onclick="denyFall('${escapeHtml(residentName)}')">No</button>
                        </div>
                    </div>
                </div>
            `;

            // fallback if image not available
            setTimeout(() => {
                const img = document.getElementById('fall-photo');
                if (!img || img.style.display === 'none') {
                    content.innerHTML = `
                        <div class="no-data">No image available for <strong>${escapeHtml(residentName)}</strong>, but the system reports a fall.</div>
                        <div style="margin-top:10px" class="confirm-buttons">
                            <button class="btn confirm" onclick="confirmFall('${escapeHtml(residentName)}')">Confirm</button>
                            <button class="btn ghost" onclick="denyFall('${escapeHtml(residentName)}')">No</button>
                        </div>
                    `;
                }
            }, 500);
        }

        // simple escape to avoid injection in the small context of this dashboard
        function escapeHtml(unsafe) {
            return String(unsafe).replace(/[&<>"'`=\/]/g, function(s) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;',
                    '/': '&#x2F;',
                    '`': '&#x60;',
                    '=': '&#x3D;'
                })[s];
            });
        }

        // stubs for confirm / deny buttons (do nothing for the moment)
        function confirmFall(name) {
            console.log('Confirm fall for', name);
            // future: send ajax or websocket ack
            // For now, remove the fall-visual content and show no-data text
            document.getElementById('fall-visual-content').innerHTML = '<div class="no-data">No fall to display</div>';
        }

        function denyFall(name) {
            console.log('Deny fall for', name);
            document.getElementById('fall-visual-content').innerHTML = '<div class="no-data">No fall to display</div>';
        }

        // Start websocket on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            connect();
        });
    </script>
</body>

</html>