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

let scenario = [];

const connect = function () {
    return new Promise((resolve, reject) => {
        const socketUrl = `wss://magictintin.fr/ws`;
        socket = new WebSocket(socketUrl);

        socket.onopen = (e) => {
            resolve();
            sendMsg("ping");
            sendMsgChan("img");
            sendMsgChan("admin");
            sendMsgChan("goto");
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
            else if (data === "newimg") {
                updateLivePhoto();
            }

            // handle fall visualization message of form "visu_fall|Name"
            else if (data.startsWith("visu_fall")) {
                const parts = data.split("|");
                const name = parts[1] ? parts[1].trim() : "Unknown";
                showFallVisualization(name);
                // also reload falls list (optional)
                loadFalls();
            } else if (data.includes(DENY) || data.includes(CONF)) {
                loadResidentCard("");
                document.getElementById('fall-visual-content').innerHTML = '<div class="no-data">No fall to display</div>';
            } else if (data.startsWith("setScenario:")) {
                parts = data.split(":");
                parts.shift();
                if (parts.length == 0) return;
                scenario = JSON.parse(parts.join(":").trim());
                
                loadScenario(scenario);
                
            } else if (data.startsWith("markStep:")) {
                parts = data.split(":");
                parts.shift();
                if (parts.length == 0) return;
                const step = parts.join(":").trim();

                markStep(step);
                
                // for (const state of scenario) {
                //     if (state.step && state.step == step) {

                //     }
                // }
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
const isOpen = function (ws) {
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
                            <button class="btn confirm" onclick="confirmFall('${escapeHtml(residentName)}')" ${(CONN) ? "" : "disabled"}>Confirm</button>
                            <button class="btn ghost" onclick="denyFall('${escapeHtml(residentName)}')" ${(CONN) ? "" : "disabled"}>No</button>
                        </div>
                    </div>
                </div>
            `;
    loadResidentCard(residentName);
    // fallback if image not available
    setTimeout(() => {
        const img = document.getElementById('fall-photo');
        if (!img || img.style.display === 'none') {
            content.innerHTML = `
                        <div class="no-data">No image available for <strong>${escapeHtml(residentName)}</strong>, but the system reports a fall.</div>
                        <div style="margin-top:10px" class="confirm-buttons">
                            <button class="btn confirm" onclick="confirmFall('${escapeHtml(residentName)}')" ${(CONN) ? "" : "disabled"}>Confirm</button>
                            <button class="btn ghost" onclick="denyFall('${escapeHtml(residentName)}')" ${(CONN) ? "" : "disabled"}>No</button>
                        </div>
                    `;
        }
    }, 500);
}

// simple escape to avoid injection in the small context of this dashboard
function escapeHtml(unsafe) {
    return String(unsafe).replace(/[&<>"'`=\/]/g, function (s) {
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
    loadResidentCard("");
    sendMsgChan(CHAN, CONF);
    document.getElementById('fall-visual-content').innerHTML = '<div class="no-data">No fall to display</div>';
}

function denyFall(name) {
    console.log('Deny fall for', name);
    loadResidentCard("");
    sendMsgChan(CHAN, DENY);
    document.getElementById('fall-visual-content').innerHTML = '<div class="no-data">No fall to display</div>';
}

// Start websocket on DOM ready
document.addEventListener('DOMContentLoaded', function () {
    connect();
});

async function loadResidentCard(name) {
    const container = document.getElementById('fall-card-container');
    if (!container) return;
    if (name == "") return container.innerHTML = "";

    // show loading state
    container.innerHTML = '<div class="loading">Loading resident card…</div>';

    const targetUrl = 'card.php?name=' + encodeURIComponent(name);

    try {
        const resp = await fetch(targetUrl, { credentials: 'same-origin' });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);

        const text = await resp.text();

        let injected = text;
        const trimmed = text.trim().toLowerCase();
        if (trimmed.startsWith('<!doctype') || trimmed.startsWith('<html') || trimmed.includes('<body')) {
            // parse and extract body
            const parser = new DOMParser();
            const doc = parser.parseFromString(text, 'text/html');
            // If card.php renders the card inside a specific root element (e.g. .card-root), prefer that:
            const root = doc.querySelector('.card-root') || doc.body;
            injected = root ? root.innerHTML : doc.body.innerHTML;
        }

        // insert returned markup. If you want to avoid scripts executing, this will insert HTML only.
        container.innerHTML = injected;

        // optionally scroll into view
        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    } catch (err) {
        console.error('Failed to load resident card:', err);
        container.innerHTML = '<div class="no-data">Unable to load resident card.</div>';
    }
}