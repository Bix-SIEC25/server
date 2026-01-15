<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Logs Viewer</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" crossorigin="anonymous"></script>
    <!-- <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet"> -->
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-+/aZx+6Qm0b8f1g6rZrSx8qz1q3QYx2qR+7mZx0bKro=" crossorigin="anonymous"></script> -->
    <link rel="stylesheet" href="logs.css">
</head>

<body>
    <div class="wrap">
        <header>
            <div class="logo">LV</div>
            <div>
                <h1>Logs Viewer</h1>
                <p class="lead">Bix's car logs</p>
            </div>
        </header>

        <div class="card">
            <div class="controls">
                <div class="group">
                    <label class="meta">Sender</label>
                    <input type="text" id="filter-sender" placeholder="e.g. my-service">
                </div>
                <div class="group">
                    <label class="meta">Level</label>
                    <select id="filter-type">
                        <option value="">All</option>
                        <option value="0">Error</option>
                        <option value="1">Warning</option>
                        <option value="2">Trace</option>
                        <option value="3">Debug</option>
                    </select>
                </div>

                <div class="group">
                    <button id="btn-fetch" class="small">Fetch</button>
                    <button id="btn-clear" class="secondary small">Clear</button>
                </div>

                <div class="group" style="margin-left:auto;align-items:center">
                    <label class="meta" style="margin-right:8px">Auto-refresh</label>
                    <input type="number" id="refresh-interval" min="5" value="10" style="width:80px"> s
                    <button id="btn-toggle-auto" class="secondary small" style="margin-left:8px">Start</button>
                </div>
            </div>

            <div id="status">Idle. Last update: <span id="last-updated">-</span></div>

            <div id="logs" style="margin-top:12px">
                <div class="meta">No logs loaded yet - click <strong>Fetch</strong>.</div>
            </div>
        </div>

    </div>

    <script>
        const logsContainer = $('#logs');
        const status = $('#status');
        let autoTimer = null;

        function buildUrl() {
            const sender = $('#filter-sender').val().trim();
            const type = $('#filter-type').val();
            const parts = [];
            if (sender) parts.push('sender=' + encodeURIComponent(sender));
            if (type !== '') parts.push('type=' + encodeURIComponent(type));
            const q = parts.length ? ('?' + parts.join('&')) : '';
            return 'logs_section.php' + q;
        }

        function setStatus(txt) {
            status.text(txt);
        }

        function fetchLogs() {
            const url = buildUrl();
            setStatus('Loading...');
            logsContainer.load(url, function(response, statusText, xhr) {
                const now = new Date();
                $('#last-updated').text(now.toLocaleString());
                if (statusText === 'error') {
                    logsContainer.html('<div class="meta">Failed to load logs: ' + (xhr.status ? xhr.status + ' ' + xhr.statusText : 'unknown') + '</div>');
                    setStatus('Error loading logs');
                } else {
                    setStatus('Loaded from ' + url + '');
                    enhanceTable();
                }
            });
        }

        function enhanceTable() {
            $('#logs table').find('tbody td').each(function() {
                $(this).css('word-break', 'break-word');
            });
        }

        function startAuto() {
            const seconds = parseInt($('#refresh-interval').val(), 10) || 10;
            stopAuto();
            autoTimer = setInterval(fetchLogs, seconds * 1000);
            $('#btn-toggle-auto').text('Stop');
            setStatus('Auto-refresh every ' + seconds + 's - running');
        }

        function stopAuto() {
            if (autoTimer) clearInterval(autoTimer);
            autoTimer = null;
            $('#btn-toggle-auto').text('Start');
        }
        currenttimeout = 0;
        $(function() {
            $('#btn-fetch').on('click', function() {
                fetchLogs();
            });
	    $('#filter-sender').on('keypress', function () {
		    //console.log("timeout", currenttimeout);
		    if (currenttimeout)
    		        clearTimeout(currenttimeout);
		    currenttimeout = setTimeout(() => fetchLogs(), 200);
	    });
            $('#btn-clear').on('click', function() {
                $('#filter-sender').val('');
                $('#filter-type').val('');
                logsContainer.html('<div class="meta">Filters cleared - click Fetch to reload.</div>');
                $('#last-updated').text('-');
                setStatus('Idle');
            });
            $('#btn-toggle-auto').on('click', function() {
                if (autoTimer) {
                    stopAuto();
                    setStatus('Auto-refresh stopped');
                } else {
                    startAuto();
                }
            });

            $('#filter-sender').on('keypress', function(e) {
                if (e.which === 13) fetchLogs();
            });
        });

        let socket;
        let tries = 0;

        const connect = function() {
            // Return a promise, which will wait for the socket to open
            return new Promise((resolve, reject) => {

                const socketUrl = `wss://magictintin.fr:8443`

                socket = new WebSocket(socketUrl);

                socket.onopen = (e) => {
                    resolve();
                    sendMsg("ping");
                }

                socket.onmessage = (data) => {
                    console.log('websocket sent', data.data); // data.data
                    if (data.data.includes("new log"))
                        fetchLogs();
                }

                socket.onclose = (e) => {
                    // Return an error if any occurs
                    // console.log('Disconnected from websocket', e);
                    console.log("Reconnecting to websocket...");
                    fetchLogs();
                    setTimeout(() => {
                        connect();
                    }, 1000);
                }

                socket.onerror = (e) => {
                    // Return an error if any occurs
                    console.log(e);
                    resolve();
                    // Try to connect again
                    if (tries < 3) {
                        tries++;
                        setTimeout(() => {
                            connect();
                        }, 1000);
                    } else
                        console.log("REFRESH THE PAGE");

                }
            });
        }

        // check if a websocket is open
        const isOpen = function(ws) {
            return ws.readyState === ws.OPEN
        }

        function sendMsg(message = 'ping') {
            if (isOpen(socket)) {
                socket.send(`bix/logs:${message}`);
                console.log(`${message} sent to server (bix room, logs group)`);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Connect to the websocket
            connect();
        });
    </script>
</body>

</html>
