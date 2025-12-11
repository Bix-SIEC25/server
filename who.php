<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Waiting for QR code...</title>
  <style>
    :root{--bg:#081125;--card:#0f1724;--accent:#3b82f6;color-scheme:dark}
    html,body{height:100%;margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial}
    body{display:flex;align-items:center;justify-content:center;background:linear-gradient(180deg,#061221 0%, #071028 100%);color:#e6eef8}
    .stage{width:520px;max-width:92%;background:linear-gradient(180deg,rgba(255,255,255,0.02),rgba(255,255,255,0.01));border:1px solid rgba(255,255,255,0.04);padding:28px;border-radius:12px;box-shadow:0 8px 30px rgba(2,6,23,0.6);text-align:center}
    h1{margin:0 0 12px;font-size:20px}
    p{margin:0 0 18px;color:rgba(230,238,248,0.8)}
    .dotline{display:flex;gap:8px;justify-content:center;margin-bottom:18px}
    .dot{width:12px;height:12px;border-radius:50%;background:linear-gradient(180deg,#2dd4bf,#60a5fa);opacity:.95;box-shadow:0 6px 18px rgba(59,130,246,0.12);animation:blink 1.6s infinite}
    @keyframes blink{0%{opacity:.25}50%{opacity:1}100%{opacity:.25}}
    .ws-info{font-size:13px;color:rgba(230,238,248,0.6);word-break:break-all}
    .status{margin-top:18px;padding:10px;border-radius:8px;background:rgba(255,255,255,0.02);font-size:13px}
    .tryagain{margin-top:14px;display:inline-block;padding:8px 12px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);cursor:pointer}
  </style>
</head>
<body>
  <main class="stage" id="stage">
    <h1>Waiting for QR code scan</h1>
    <div class="dotline" aria-hidden="true">
      <div class="dot"></div>
      <div class="dot" style="animation-delay:.2s"></div>
      <div class="dot" style="animation-delay:.4s"></div>
    </div>
    <p class="ws-info" id="connecting_message">Connecting to WebSocket...</strong></p>
    <div class="status" id="status">Initializing...</div>
    <button class="tryagain" id="reconnectBtn" style="display:none">Retry connection</button>
  </main>

  <script>
    (function(){
      const wsUrl = 'wss://magictintin.fr/ws';
      let ws;
      const statusEl = document.getElementById('status');
      const reconnectBtn = document.getElementById('reconnectBtn');
      const stage = document.getElementById('stage');

      function setStatus(t){ statusEl.textContent = t; if (t == "Connected" && document.getElementById("connecting_message")) document.getElementById("connecting_message").style.display = "none";}

      function connect(){
        setStatus('Connecting to WebSocket...');
        reconnectBtn.style.display = 'none';
        try {
          ws = new WebSocket(wsUrl);
        } catch (err) {
          console.error('WebSocket constructor error', err);
          setStatus('WebSocket not available in this environment.');
          reconnectBtn.style.display = '';
          return;
        }

        ws.addEventListener('open', () => {
          setStatus('Connected  subscribing to QR and Face channel');
          try{ ws.send('bix/log_QRNode:ping'); }catch(e){console.warn(e)}
          try{ ws.send('bix/log_FaceRecognitionNode:ping'); }catch(e){console.warn(e)}
        });

        ws.addEventListener('message', (ev) => {
          console.log('ws message', ev.data);
          if (ev.data == "ping") return setStatus('Connected'); //console.log("ping received");
          setStatus('Message received  loading resident card...');

          let payload = ev.data.substring(2);
          try {
            const parsed = JSON.parse(payload);
            if (parsed && typeof parsed === 'object') {
              if (parsed.name) payload = parsed.name;
              else if (parsed.device_id) payload = String(parsed.device_id);
              else if (parsed.id) payload = String(parsed.id);
              else if (parsed.msg) payload = parsed.msg;
            }
          } catch (e) {
            // not JSON, keep as-is
          }

          // sanitize simple surrounding quotes
          payload = payload.replace(/^"|"$/g, '');

          const targetUrl = 'card.php?name=' + encodeURIComponent(payload);
          fetch(targetUrl, { credentials: 'same-origin' })
            .then(r => r.text())
            .then(html => {
              // replace entire document body this will replace the current page with the card's HTML
              document.open();
              document.write(html);
              document.close();
              // update history so URL shows the card page
              try { history.replaceState(null, '', targetUrl); } catch (e) {}
            })
            .catch(err => {
              console.error('Failed to load card:', err);
              setStatus('Failed to load card page. See console for details.');
            });
        });

        ws.addEventListener('close', (ev) => {
          console.warn('ws closed', ev);
          setStatus('WebSocket connection closed.');
          reconnectBtn.style.display = '';
          connect();
        });

        ws.addEventListener('error', (err) => {
          console.error('ws error', err);
          setStatus('WebSocket error occurred.');
          reconnectBtn.style.display = '';
        });
      }

      reconnectBtn.addEventListener('click', () => { connect(); });

      connect();

    })();
  </script>
</body>
</html>
