<?php
// $scenarios = array(
//     "giovfall" => array(
//         array("step" => "Started"),
//         array("transition" => "patrolling"),
//         array("step" => "Fall via cam"),
//         array("transition" => "recognizing face"),
//         array("step" => "Giovanna detected"),
//         array("transition" => "Waiting confirmation"),
//         array("step" => "Confirmed"),
//         array("transition" => "Asking questions"),
//         array("step" => "No emergency"),
//     ),
//     "hugofake" => array(
//         array("step" => "Started"),
//         array("transition" => "patrolling"),
//         array("step" => "Fake necklace fall"),
//         array("transition" => "going to fall"),
//         array("step" => "Arrived"),
//         array("transition" => "Detecting QRCode"),
//         array("step" => "Hugo detected"),
//         array("transition" => "Waiting confirmation"),
//         array("step" => "Denied"),
//     ),
// );

$scenarios = [
    "giovfall" => [
        ["step" => "Started"],
        ["transition" => "patrolling"],
        ["step" => "Fall via cam"],
        ["transition" => "recognizing face"],
        ["step" => "Giovanna detected"],
        ["transition" => "Waiting confirmation"],
        ["step" => "Confirmed"],
        ["transition" => "Asking questions"],
        ["step" => "No emergency"],
    ],
    "hugofake" => [
        ["step" => "Started"],
        ["transition" => "patrolling"],
        ["step" => "Fake necklace fall"],
        ["transition" => "going to fall"],
        ["step" => "Arrived"],
        ["transition" => "Detecting QRCode"],
        ["step" => "Hugo detected"],
        ["transition" => "Waiting confirmation"],
        ["step" => "Denied"],
    ],
    "nothing" => [],
];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Scenario control</title>
    <style>
        :root {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial;
        }

        body {
            margin: 0;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: #f6f7f9;
            color: #111
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between
        }

        h1 {
            font-size: 20px;
            margin: 0
        }

        main {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 12px
        }

        .scenario {
            background: #fff;
            border: 1px solid #e0e0e6;
            border-radius: 10px;
            padding: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .04)
        }

        .scenario h2 {
            font-size: 18px;
            margin: 0 0 8px
        }

        .btn {
            display: inline-block;
            width: 100%;
            text-align: center;
            border: 0;
            border-radius: 10px;
            padding: 18px 12px;
            margin: 6px 0;
            font-size: 18px;
            font-weight: 600;
            background: #0073e6;
            color: white;
            cursor: pointer
        }

        .btn.secondary {
            background: #6b7280
        }

        .btn.small {
            padding: 10px;
            font-size: 14px
        }

        .steps {
            display: flex;
            flex-direction: column
        }

        aside#history {
            max-height: 320px;
            overflow: auto;
            background: #fff;
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #e0e0e6;
            overflow-x: hidden;
        }

        .meta {
            overflow-x: hidden;
        }

        .history-item {
            font-size: 13px;
            padding: 8px;
            border-bottom: 1px dashed #eee
        }

        .meta {
            font-size: 12px;
            color: #666
        }

        @media (min-width:1100px) {
            body {
                flex-direction: row
            }

            main {
                flex: 1
            }

            aside#side {
                width: 360px;
                flex-shrink: 0;
                display: flex;
                flex-direction: column;
                gap: 12px
            }
        }
    </style>
</head>

<body>
    <main id="scenarios"></main>

    <aside id="side">
        <div id="history" aria-live="polite">
            <strong>History</strong>
            <div id="historyList"></div>
            <div style="margin-top:8px;display:flex;gap:8px">
                <button id="clearHistory" class="btn small secondary">Clear history</button>
            </div>
        </div>
    </aside>

    <script>
        // scenarios object passed from PHP
        const scenarios = <?php echo json_encode($scenarios, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

        const main = document.getElementById('scenarios');
        const historyList = document.getElementById('historyList');

        function addHistory(entry) {
            const now = new Date();
            const node = document.createElement('div');
            node.className = 'history-item';
            node.innerHTML = `<div><strong>${escapeHtml(entry.label)}</strong></div><div class="meta">${now.toLocaleString()}</div><div class="meta">${escapeHtml(entry.note||'')}</div>`; // ${escapeHtml(entry.url)} - 
            historyList.prepend(node);

            // persist to localStorage (keep last 200)
            try {
                const h = JSON.parse(localStorage.getItem('scenario_history') || '[]');
                h.unshift({
                    t: now.toISOString(),
                    label: entry.label,
                    url: entry.url,
                    note: entry.note || ''
                });
                localStorage.setItem('scenario_history', JSON.stringify(h.slice(0, 200)));
            } catch (e) {
            }
        }

        function loadHistory() {
            try {
                const h = JSON.parse(localStorage.getItem('scenario_history') || '[]');
                h.reverse().forEach(item => {
                    const node = document.createElement('div');
                    node.className = 'history-item';
                    node.innerHTML = `<div><strong>${escapeHtml(item.label)}</strong></div><div class="meta">${new Date(item.t).toLocaleString()}</div><div class="meta">${escapeHtml(item.note||'')}</div>`; // ${substr(escapeHtml(item.url), 0, 20)} 
                    historyList.prepend(node);
                });
            } catch (e) {
                /* ignore */
            }
        }

        document.getElementById('clearHistory').addEventListener('click', () => {
            localStorage.removeItem('scenario_history');
            historyList.innerHTML = '';
        });

        function escapeHtml(s) {
            if (!s) return '';
            return String(s).replace(/[&<>\"']/g, function(c) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": "&#39;"
                } [c];
            });
        }

        async function sendRequest(url, label) {
            let note = '';
            try {
                const resp = await fetch(url, {
                    method: 'GET',
                    mode: 'cors'
                });
                let text = '';
                try {
                    text = await resp.text();
                } catch (e) {
                    text = `Response received (status ${resp.status})`;
                }
                    note = `status: ${resp.status} / ${resp.type} / ${text.length}`;

                if (text.length < 5)
                    note = `status: ${resp.status} / ${text}`;
                addHistory({
                    label,
                    url,
                    note
                });
                return {
                    success: true,
                    respText: text
                };
            } catch (err) {
                try {
                    const img = new Image();
                    img.src = url + (url.includes('?') ? '&' : '?') + '_=' + Date.now();
                    note = 'sent (beacon fallback, no response available)';
                    addHistory({
                        label,
                        url,
                        note
                    });
                    return {
                        success: true,
                        note
                    };
                } catch (e) {
                    note = 'failed to send: ' + String(e);
                    addHistory({
                        label,
                        url,
                        note
                    });
                    return {
                        success: false,
                        note
                    };
                }
            }
        }

        function makeScenarioCard(name, steps) {
            const card = document.createElement('section');
            card.className = 'scenario';
            const title = document.createElement('h2');
            title.textContent = name;
            card.appendChild(title);

            // Set scenario button
            const setBtn = document.createElement('button');
            setBtn.className = 'btn';
            setBtn.textContent = 'Set scenario';
            setBtn.addEventListener('click', () => {
                const s = steps;
                const url = 'scenario_set?name=' + encodeURIComponent(name) + '&s=' + encodeURIComponent(JSON.stringify(s));
                sendRequest(url, `scenario_set:${name}`).then(() => {
                    /* no-op */
                });
            });
            card.appendChild(setBtn);

            // Steps
            const stepsWrap = document.createElement('div');
            stepsWrap.className = 'steps';
            steps.forEach(obj => {
                if (obj.step) {
                    const btn = document.createElement('button');
                    btn.className = 'btn secondary';
                    btn.textContent = obj.step;
                    btn.setAttribute('aria-label', `Send mark ${obj.step}`);
                    btn.addEventListener('click', () => {
                        const url = 'add_mark?m=' + encodeURIComponent(obj.step);
                        sendRequest(url, `add_mark:${obj.step}`).then(() => {
                            /* no-op */
                        });
                    });
                    stepsWrap.appendChild(btn);
                }
            });
            card.appendChild(stepsWrap);
            return card;
        }

        Object.keys(scenarios).forEach(k => {
            const card = makeScenarioCard(k, scenarios[k]);
            main.appendChild(card);
        });

        loadHistory();
    </script>
</body>

</html>