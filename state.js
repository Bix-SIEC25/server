(function(){
  const config = {
    mapUrl: 'maps/gei_0.png',   // path to map image (adjust if needed)
    fetchIntervalMs: 1000,          // polling interval for /state.php
    // Real-world coordinates for corners (edit these to match your map)
    topLeft:     { x: -36.57, y:  19.93 },
    topRight:    { x:  27.26, y:   19.93 },
    bottomLeft:  { x:  -36.57, y:  -35.01 },
    bottomRight: { x:  27.26, y:   -35.01 }
  };
  // ------------------------------------------------------------------------------------

  const stateKeys = ['wait_car','qr','face','dialog','fall_ia','mov_car','wait_image_verif'];

  // UI skeleton
  const toggle = document.createElement('div');
  toggle.id = 'robot-map-toggle';
  toggle.innerText = 'Status';
  document.body.appendChild(toggle);

  const panel = document.createElement('div');
  panel.id = 'robot-map-panel';
  panel.style.display = 'none';
  panel.innerHTML = `
    <div class="header">
      <h3>Bix status</h3>
      <div>
        <button id="robot-map-close" class="small">Close</button>
      </div>
    </div>
    <div id="robot-map-main">
      <canvas id="robot-map-canvas" width="560" height="440"></canvas>
      <div id="robot-map-info">
        <div><strong>Position</strong></div>
        <div id="robot-pos-text">x: — , y: — , dir: —</div>
        <div><strong>States</strong></div>
        <div id="robot-states"></div>
        
      </div>
    </div>
  `; 

  /*
   * <div id="robot-map-controls">
         <button id="center-image" class="small">Center</button>
     </div>
   */
  document.body.appendChild(panel);

  const canvas = panel.querySelector('#robot-map-canvas');
  const ctx = canvas.getContext('2d');

  // image + transform
  const mapImage = new Image();
  mapImage.src = config.mapUrl;
  let imageLoaded = false;
  let imageDrawRect = null; // {x,y,w,h} on canvas
  // affine transform parameters: matrix M (3x3) mapping [wx,wy,1] -> [px,py,1]
  // stored as array [a,b,c,d,e,f] where:
  // px = a*wx + b*wy + c
  // py = d*wx + e*wy + f
  let affine = null;
  let affineInverse = null; // 3x3 inverse matrix (for pixel->world)

  // state
  let latestState = null;
  let pollTimer = null;

  // state indicators container
  const statesContainer = panel.querySelector('#robot-states');
  function renderStateIndicators(info) {
    statesContainer.innerHTML = '';
    stateKeys.forEach(k=>{
      const val = info && (k in info) ? !!Number(info[k]) : false;
      const row = document.createElement('div');
      row.className = 'state-row';
      const label = document.createElement('div');
      label.innerText = k;
      label.style.flex='1';
      const dot = document.createElement('div');
      dot.className = 'ind-dot ' + (val? 'on':'off');
      row.appendChild(label);
      row.appendChild(dot);
      statesContainer.appendChild(row);
    });
  }
  renderStateIndicators(null);

  toggle.addEventListener('click', ()=> panel.style.display = 'flex');
  panel.querySelector('#robot-map-close').addEventListener('click', ()=> panel.style.display = 'none');
//   panel.querySelector('#center-image').addEventListener('click', ()=>{
//     if (!imageLoaded) return;
//     // re-calc drawRect from image and redraw (no change to affine)
//     drawScene();
//   });

  // draw map and robot
  function drawScene() {
    ctx.clearRect(0,0,canvas.width,canvas.height);
    if (!imageLoaded) {
      // placeholder
      ctx.fillStyle = '#ddd';
      ctx.fillRect(0,0,canvas.width,canvas.height);
      ctx.fillStyle = '#666';
      ctx.font = '14px Arial';
      ctx.fillText('Loading map image...', 20, 30);
      return;
    }

    // fit image to canvas preserving aspect ratio and draw centered
    const iw = mapImage.width, ih = mapImage.height;
    const cw = canvas.width, ch = canvas.height;
    const scale = Math.min(cw/iw, ch/ih);
    const drawW = iw * scale, drawH = ih * scale;
    const offsetX = (cw - drawW)/2;
    const offsetY = (ch - drawH)/2;
    imageDrawRect = { x: offsetX, y: offsetY, w: drawW, h: drawH };
    ctx.drawImage(mapImage, offsetX, offsetY, drawW, drawH);

    // if affine not yet computed, compute now using user corner coordinates
    if (!affine) {
      computeAffineFromCorners();
    }

    // draw robot if we have state
    if (latestState && affine) {
      const p = worldToPixel(latestState.x, latestState.y);

      const ux = Math.cos(latestState.dir);
      const uy = Math.sin(latestState.dir);
      const smallStep = 0.25; // meters
      const p2 = worldToPixel(latestState.x + ux*smallStep, latestState.y + uy*smallStep);
      const ang = Math.atan2(p2.y - p.y, p2.x - p.x);

      // point
      ctx.save();
      ctx.translate(p.x, p.y);
      ctx.rotate(ang);
      ctx.beginPath();
      ctx.fillStyle = '#ff5722';
      ctx.strokeStyle = '#800';
      ctx.lineWidth = 2;
      ctx.arc(0,0,6,0,Math.PI*2);
      ctx.fill();
      ctx.stroke();

      // direction arrow
      ctx.beginPath();
      ctx.moveTo(7,0);
      ctx.lineTo(18,0);
      ctx.lineTo(14,-6);
      ctx.moveTo(18,0);
      ctx.lineTo(14,6);
      ctx.strokeStyle = '#202020';
      ctx.lineWidth = 2;
      ctx.stroke();

      ctx.restore();

      // overlay textual box
      ctx.fillStyle = 'rgba(255,255,255,0.85)';
      ctx.fillRect(6,6,260,34);
      ctx.fillStyle = '#000';
      ctx.font = '12px Arial';
      ctx.fillText(`x: ${latestState.x.toFixed(3)}  y: ${latestState.y.toFixed(3)}  dir: ${latestState.dir.toFixed(3)} rad`, 10, 26);
    }
  }

  function computeAffineFromCorners() {
    if (!imageDrawRect) {
      // image not drawn yet; call after drawScene sets imageDrawRect
      return;
    }
    // pixel coordinates of image corners (canvas coords)
    const px_tl = { x: imageDrawRect.x, y: imageDrawRect.y };
    const px_tr = { x: imageDrawRect.x + imageDrawRect.w, y: imageDrawRect.y };
    const px_bl = { x: imageDrawRect.x, y: imageDrawRect.y + imageDrawRect.h };
    const px_br = { x: imageDrawRect.x + imageDrawRect.w, y: imageDrawRect.y + imageDrawRect.h };

    const worldPts = [
      { x: config.topLeft.x,  y: config.topLeft.y },
      { x: config.topRight.x, y: config.topRight.y },
      { x: config.bottomLeft.x,y: config.bottomLeft.y },
      { x: config.bottomRight.x,y: config.bottomRight.y }
    ];
    const pixelPts = [ px_tl, px_tr, px_bl, px_br ];

    // Build normal least-squares system to solve for 6 unknowns [a,b,c,d,e,f]
    // For each correspondence: px = a*wx + b*wy + c ; py = d*wx + e*wy + f
    // We assemble matrix A and vector B such that A * U = B
    // Here U = [a,b,c,d,e,f]^T, size 6. A is (2n x 6).

    const n = worldPts.length;
    // Build normal matrix (6x6) and rhs (6)
    const ATA = new Array(6).fill(0).map(()=> new Array(6).fill(0));
    const ATb = new Array(6).fill(0);

    for (let i=0;i<n;i++) {
      const wx = worldPts[i].x;
      const wy = worldPts[i].y;
      const px = pixelPts[i].x;
      const py = pixelPts[i].y;

      // rows for px equation: [wx, wy, 1, 0, 0, 0] * U = px
      const rowPx = [wx, wy, 1, 0, 0, 0];
      // rows for py equation: [0,0,0, wx, wy, 1] * U = py
      const rowPy = [0,0,0, wx, wy, 1];

      // accumulate ATA and ATb for px row
      for (let r=0;r<6;r++){
        for (let c=0;c<6;c++){
          ATA[r][c] += rowPx[r] * rowPx[c];
        }
        ATb[r] += rowPx[r] * px;
      }
      // accumulate ATA and ATb for py row
      for (let r=0;r<6;r++){
        for (let c=0;c<6;c++){
          ATA[r][c] += rowPy[r] * rowPy[c];
        }
        ATb[r] += rowPy[r] * py;
      }
    }

    // solve ATA * U = ATb for U using Gaussian elimination
    const U = solveLinearSystem(ATA, ATb);
    if (!U) {
    //   console.error('Affine solve failed; check corner coordinates');
      affine = null;
      affineInverse = null;
      return;
    }
    affine = { a: U[0], b: U[1], c: U[2], d: U[3], e: U[4], f: U[5] };
    affineInverse = invertAffineMatrix(affine);
    // console.info('Affine computed', affine);
  }

  // convert world to pixel using affine
  function worldToPixel(wx, wy) {
    if (!affine) return { x: 0, y: 0 };
    const px = affine.a * wx + affine.b * wy + affine.c;
    const py = affine.d * wx + affine.e * wy + affine.f;
    return { x: px, y: py };
  }
  // convert pixel to world using inverse affine (if available)
  function pixelToWorld(px, py) {
    if (!affineInverse) return { x: 0, y: 0 };
    const wx = affineInverse[0]*px + affineInverse[1]*py + affineInverse[2];
    const wy = affineInverse[3]*px + affineInverse[4]*py + affineInverse[5];
    return { x: wx, y: wy };
  }

  // numeric helpers ----------------------------------------------------
  // Solve linear system A x = b, where A is NxN (square) and b length N.
  // Returns x array or null on failure. Uses Gaussian elimination with partial pivot.
  function solveLinearSystem(Aorig, borig) {
    const n = Aorig.length;
    // deep copy
    const A = new Array(n);
    for (let i=0;i<n;i++) A[i] = Aorig[i].slice();
    const b = borig.slice();

    for (let i=0;i<n;i++) {
      // pivot
      let maxRow = i;
      let maxVal = Math.abs(A[i][i]);
      for (let r=i+1; r<n; r++) {
        if (Math.abs(A[r][i]) > maxVal) { maxVal = Math.abs(A[r][i]); maxRow = r; }
      }
      if (maxVal < 1e-12) return null; // singular
      // swap
      if (maxRow !== i) {
        const tmp = A[i]; A[i] = A[maxRow]; A[maxRow] = tmp;
        const tb = b[i]; b[i] = b[maxRow]; b[maxRow] = tb;
      }
      // normalize and eliminate
      const pivot = A[i][i];
      for (let c=i; c<n; c++) A[i][c] /= pivot;
      b[i] /= pivot;
      for (let r=0; r<n; r++) {
        if (r === i) continue;
        const factor = A[r][i];
        for (let c=i; c<n; c++) A[r][c] -= factor * A[i][c];
        b[r] -= factor * b[i];
      }
    }
    return b; // now b contains solution
  }

  // invert affine 2x3 matrix to produce 2x3 inverse mapping px->world:
  // Given affine {a,b,c,d,e,f} representing 3x3 M = [[a,b,c],[d,e,f],[0,0,1]]
  // compute inverse M^{-1} and return flattened [m00,m01,m02, m10,m11,m12] such that:
  // [wx,wy,1]^T = M^{-1} * [px,py,1]^T and wx = m00*px + m01*py + m02, wy = m10*px + m11*py + m12
  function invertAffineMatrix(aff) {
    const a = aff.a, b = aff.b, c = aff.c;
    const d = aff.d, e = aff.e, f = aff.f;
    const det = a*e - b*d;
    if (Math.abs(det) < 1e-12) return null;
    const inv00 =  e / det;
    const inv01 = -b / det;
    const inv02 = (b*f - c*e) / det;
    const inv10 = -d / det;
    const inv11 =  a / det;
    const inv12 = (c*d - a*f) / det;
    return [inv00, inv01, inv02, inv10, inv11, inv12];
  }
  // --------------------------------------------------------------------

  // fetch state from /state.php and update
  async function fetchState() {
    try {
      const res = await fetch('state.php', { cache: 'no-store' });
      if (!res.ok) throw new Error('HTTP status ' + res.status);
      const json = await res.json();
      // convert numeric flags to booleans
      stateKeys.forEach(k => { if (k in json) json[k] = !!Number(json[k]); });
      latestState = json;
      updateUIFromState();
      drawScene();
    } catch (err) {
      // silently ignore fetch errors (keep last known state)
      // console.warn('Failed to fetch /state.php', err);
    }
  }

  function updateUIFromState() {
    if (!latestState) return;
    panel.querySelector('#robot-pos-text').innerText =
      `x: ${latestState.x.toFixed(3)}  y: ${latestState.y.toFixed(3)}  dir: ${latestState.dir.toFixed(3)} rad`;
    renderStateIndicators(latestState);
  }

  // image load handling
  mapImage.onload = function() {
    imageLoaded = true;
    // draw image and compute affine
    drawScene();
    // compute affine now that imageDrawRect is available
    computeAffineFromCorners();
    // poll state
    if (!pollTimer) {
      fetchState();
      pollTimer = setInterval(fetchState, config.fetchIntervalMs);
    }
  };
  mapImage.onerror = function() {
    imageLoaded = false;
    drawScene();
  };

  // initial draw
  drawScene();
//   console.info('Robot Map Panel loaded. Edit topLeft/topRight/bottomLeft/bottomRight in the script to match your map.');

  // expose a small debug method on window for convenience (optional)
  window.__robotMapDebug = {
    recompute: () => { affine = null; affineInverse = null; drawScene(); computeAffineFromCorners(); drawScene(); /*console.info('Recomputed affine:', affine);*/ },
    worldToPixel: (wx,wy)=> worldToPixel(wx,wy),
    pixelToWorld: (px,py)=> pixelToWorld(px,py),
    config
  };

})();