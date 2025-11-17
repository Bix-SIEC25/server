<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Bix — EHPAD Patrol · Autonomous Care Mobility</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Source+Serif+4:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

  <meta name="description" content="Bix — engineers building EHPAD Patrol, a compact autonomous vehicle that helps retirement homes and caregivers.">

  <style>
    /* =========================
       THEME VARIABLES (change here)
       ========================= */
    :root{
      /* Primary palette: soft medical / retirement home tones */
      --primary-500: #2F7A7A;   /* teal-blue */
      --primary-700: #1F5B5B;
      --accent-500: #E6B27A;    /* warm sand/retirement accent */
      --muted-100: #F7FAFB;     /* background */
      --muted-200: #EEF5F6;
      --muted-400: #C6D8DA;
      --text-900: #072029;
      --card-bg: #FFFFFF;
      --glass: rgba(255,255,255,0.75);

      /* layout */
      --max-width: 1200px;
      --space: 20px;
      --radius-sm: 8px;
      --radius-lg: 16px;
      --shadow-soft: 0 8px 24px rgba(12, 40, 44, 0.08);
    }

    /* Reset & base */
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      color:var(--text-900);
      background: linear-gradient(180deg, var(--muted-100) 0%, var(--muted-200) 100%);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      line-height:1.5;
    }

    .container{
      max-width:var(--max-width);
      margin:0 auto;
      padding: 32px;
    }

    /* NAV */
    header {
      position:sticky;
      top:0;
      backdrop-filter: blur(6px);
      background: linear-gradient(180deg, rgba(255,255,255,0.75), rgba(255,255,255,0.55));
      border-bottom: 1px solid rgba(12,40,44,0.04);
      z-index:50;
    }
    .nav {
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:20px;
      padding:12px 24px;
      max-width:var(--max-width);
      margin:0 auto;
    }
    .brand {
      display:flex;
      gap:12px;
      align-items:center;
      font-weight:700;
      letter-spacing:0.2px;
    }
    .logo {
      width:48px;height:48px;border-radius:10px;
      display:grid;place-items:center;
      background:linear-gradient(135deg,var(--primary-500),var(--primary-700));
      color:white;
      box-shadow: 0 6px 18px rgba(31,91,91,0.14);
      flex-shrink:0;
    }
    nav a{ text-decoration:none; color:var(--text-900); font-weight:600; font-size:0.95rem; padding:8px 12px; border-radius:8px;}
    nav a.cta{ background:var(--primary-500); color:white; box-shadow:var(--shadow-soft); }
    nav a:hover{ opacity:0.92; transform:translateY(-1px); transition:0.18s ease; }

    /* HERO */
    .hero {
      display:grid;
      grid-template-columns: 1fr 420px;
      gap:40px;
      align-items:center;
      padding:64px 0;
      max-width:var(--max-width);
      margin:0 auto;
    }
    .hero-left {
      padding:18px;
    }
    .eyebrow {
      display:inline-flex;
      gap:10px;
      align-items:center;
      font-weight:600;
      color:var(--primary-700);
      background:linear-gradient(90deg, rgba(47,122,122,0.06), rgba(230,178,122,0.03));
      padding:8px 12px;border-radius:999px;font-size:0.85rem;
      border:1px solid rgba(31,91,91,0.06);
    }
    .title {
      font-size: clamp(2rem, 4.5vw, 3.2rem);
      margin:18px 0 14px 0;
      line-height:1.02;
      font-weight:800;
      letter-spacing:-0.6px;
      color:var(--text-900);
    }
    .subtitle {
      color: #274141;
      margin-bottom:20px;
      font-size:1.05rem;
      max-width:54ch;
      opacity:0.95;
    }
    .hero-ctas{ display:flex; gap:12px; margin-top:18px; align-items:center; }
    .btn {
      display:inline-flex;
      align-items:center;
      gap:10px;
      padding:12px 18px;
      border-radius:10px;
      font-weight:700;
      cursor:pointer;
      border:none;
      text-decoration:none;
    }
    .btn.primary {
      background: linear-gradient(90deg, var(--primary-500), var(--primary-700));
      color:white;
      box-shadow: 0 10px 30px rgba(47,122,122,0.12);
    }
    .btn.ghost {
      background:transparent;
      border: 1px solid rgba(12,40,44,0.07);
      color:var(--text-900);
    }

    /* Right card with floating rectangles */
    .hero-right {
      position:relative;
      height:360px;
      display:flex;
      align-items:center;
      justify-content:center;
    }
    .device {
      width:100%;
      height:90%;
      background:linear-gradient(180deg, rgba(255,255,255,0.9), var(--card-bg));
      border-radius:14px;
      box-shadow: var(--shadow-soft);
      border: 1px solid rgba(12,40,44,0.04);
      padding:22px;
      overflow:hidden;
      position:relative;
      display:grid;
      grid-template-rows: auto 1fr;
    }
    .device .topbar {
      display:flex; gap:10px; align-items:center;
    }
    .device .topbar .dot { width:10px;height:10px;border-radius:2px;background:var(--muted-400); }
    .device .content {
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:12px;
      margin-top:14px;
    }
    .card-rect {
      background: linear-gradient(180deg, rgba(47,122,122,0.06), rgba(230,178,122,0.04));
      border-radius:8px;
      padding:12px;
      min-height:80px;
      box-shadow: 0 8px 20px rgba(12,40,44,0.05);
      display:flex; flex-direction:column; gap:6px;
      border-left:6px solid rgba(47,122,122,0.12);
    }

    /* decorative floating squares */
    .floater {
      position:absolute;
      width:120px;height:120px;border-radius:10px;
      background: linear-gradient(180deg, rgba(47,122,122,0.10), rgba(230,178,122,0.06));
      transform:rotate(6deg);
      box-shadow: 0 20px 40px rgba(31,91,91,0.05);
      animation: float 6s ease-in-out infinite;
      border: 1px solid rgba(12,40,44,0.03);
    }
    .floater.one{ right: -30px; top: -20px; width:140px;height:140px; animation-delay:0s; }
    .floater.two{ left: -40px; bottom: -20px; width:100px;height:100px; animation-delay:1.4s; transform:rotate(-4deg);}
    @keyframes float {
      0% { transform: translateY(0) rotate(6deg); }
      50% { transform: translateY(-10px) rotate(4deg); }
      100% { transform: translateY(0) rotate(6deg); }
    }

    /* Features */
    .features {
      display:grid;
      grid-template-columns: repeat(3, 1fr);
      gap:20px;
      margin-top:36px;
    }
    .feature {
      background:var(--card-bg);
      border-radius:12px;
      padding:18px;
      box-shadow: var(--shadow-soft);
      border-left:6px solid var(--accent-500);
      min-height:140px;
      display:flex;flex-direction:column;gap:8px;
    }
    .feature h4{ margin:0; font-size:1.05rem }
    .feature p{ margin:0; color:#355555; opacity:0.9 }

    /* About + Team + CTA strip */
    .about {
      display:grid;
      grid-template-columns: 1fr 380px;
      gap:28px;
      margin-top:64px;
      align-items:start;
    }
    .card {
      background:linear-gradient(180deg, rgba(255,255,255,0.85), var(--card-bg));
      padding:22px; border-radius:14px; box-shadow:var(--shadow-soft);
    }
    .team-grid {
      display:grid; grid-template-columns: repeat(2,1fr); gap:12px;
    }
    .person {
      display:flex; gap:12px; align-items:center;
    }
    .avatar {
      width:56px;height:56px;border-radius:10px;background:linear-gradient(180deg,var(--primary-500),var(--primary-700));
      color:white; display:grid; place-items:center; font-weight:800;
    }

    /* Contact form */
    form .row{ display:flex; gap:12px; }
    input, textarea {
      width:100%;
      padding:12px 14px;
      border-radius:10px;
      border:1px solid rgba(12,40,44,0.07);
      background:transparent;
      font-size:0.95rem;
    }
    textarea { min-height:120px; resize:vertical; }

    footer {
      margin-top:64px;
      padding:36px 0;
      color:#083033;
      border-top:1px solid rgba(12,40,44,0.04);
    }
    .flex { display:flex; gap:12px; align-items:center; }
    .muted { color:#375a5a; opacity:0.8; font-size:0.95rem; }

    /* Responsive */
    @media (max-width:980px){
      .hero { grid-template-columns: 1fr; padding:28px 0; gap:28px; }
      .about { grid-template-columns: 1fr; }
      .features { grid-template-columns: 1fr 1fr; }
      .nav { padding:12px; }
    }
    @media (max-width:560px){
      .features { grid-template-columns: 1fr; }
      .brand .text { display:none; }
    }

    /* little utility */
    .small { font-size:0.85rem; opacity:0.85; }
  </style>
</head>
<body>

<header>
  <div class="nav">
    <div class="brand">
      <div class="logo" aria-hidden="true">
        <!-- simple B monogram -->
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <rect x="0.5" y="0.5" width="23" height="23" rx="6" fill="white" opacity="0.06"/>
          <path d="M7 6h4a3 3 0 0 1 0 6H7V6zM7 12h5a3 3 0 0 1 0 6H7v-6z" fill="white"/>
        </svg>
      </div>
      <div>
        <div style="font-weight:800">Bix</div>
        <div class="small muted">EHPAD Patrol — Autonomous care mobility</div>
      </div>
    </div>

    <nav aria-label="Main navigation">
      <a href="#about">About</a>
      <a href="#features">Solutions</a>
      <a href="#team">Team</a>
      <a class="cta" href="#contact">Contact</a>
    </nav>
  </div>
</header>

<main>
  <section class="hero container" aria-label="Hero">
    <div class="hero-left">
      <div class="eyebrow">Autonomous · Safe · Caring</div>
      <h1 class="title">EHPAD Patrol — small autonomous assistants for safer, smoother care</h1>
      <p class="subtitle">We build compact autonomous vehicles that support staff in retirement homes — transporting supplies, assisting rounds, and providing sensors & alerting to keep residents safe and reduce staff workload.</p>

      <div class="hero-ctas">
        <a class="btn primary" href="#contact" aria-label="Contact for investors">
          <!-- icon -->
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="opacity:0.95" xmlns="http://www.w3.org/2000/svg"><path d="M3 8v10a3 3 0 0 0 3 3h12" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 3H7a3 3 0 0 0-3 3v2" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Request a demo
        </a>

        <a class="btn ghost" href="#about">Learn more</a>
      </div>

      <div style="margin-top:18px" class="small muted">
        Trusted by caregivers, built by engineers — focused on reliability, privacy, and human-centered design.
      </div>
    </div>

    <div class="hero-right" aria-hidden="true">
      <div class="floater one"></div>
      <div class="floater two"></div>

      <div class="device" role="img" aria-label="EHPAD Patrol dashboard preview">
        <div class="topbar">
          <div style="font-weight:700">EHPAD Patrol</div>
          <div style="flex:1"></div>
          <div class="small muted">v0.9 · prototype</div>
        </div>

        <div class="content">
          <div class="card-rect">
            <strong>Live sensor</strong>
            <span class="small">SpO₂ · Heart Rate</span>
            <div style="margin-top:auto; font-weight:800; font-size:1.6rem; color:var(--primary-700)">98% · 72 bpm</div>
          </div>

          <div class="card-rect">
            <strong>Rounds</strong>
            <span class="small">Next scheduled: 14:30</span>
            <div style="margin-top:auto; display:flex; gap:10px; align-items:center;">
              <div style="padding:6px 10px; border-radius:8px; background:rgba(31,91,91,0.08); font-weight:700;">Active</div>
              <div style="font-size:0.9rem; color:#28585A">ETA 2 min</div>
            </div>
          </div>

          <div class="card-rect">
            <strong>Safety</strong>
            <span class="small">Obstacle detection & fall alert</span>
            <div style="margin-top:auto;">Realtime alerts push to staff dashboard and mobile app.</div>
          </div>

          <div class="card-rect">
            <strong>Logistics</strong>
            <span class="small">Medicine & supplies transport</span>
            <div style="margin-top:auto;">Autonomous routes with supervised hand-off.</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features -->
  <section id="features" class="container">
    <div style="display:flex; justify-content:space-between; align-items:end; gap:12px;">
      <div>
        <h2 style="margin:0 0 8px 0;">Solutions designed for care teams</h2>
        <div class="small muted">Practical, safe and easy to integrate.</div>
      </div>
      <div class="small muted">Prototypes · Pilots · Deployment support</div>
    </div>

    <div class="features" aria-hidden="false">
      <div class="feature card">
        <h4>Resident safety</h4>
        <p>Fall detection, double-ID verification, and proximity monitoring. Alerts routed to the right caregiver instantly.</p>
      </div>

      <div class="feature card">
        <h4>Staff efficiency</h4>
        <p>Automated supply delivery and routine rounds free caregivers to focus on residents’ needs.</p>
      </div>

      <div class="feature card">
        <h4>Data & privacy</h4>
        <p>Local-first processing for vital signals, and privacy-by-design data handling — only what’s necessary is transmitted.</p>
      </div>
    </div>
  </section>

  <!-- About + Team -->
  <section id="about" class="container about">
    <div>
      <div class="card">
        <h3 style="margin-top:0">Who we are</h3>
        <p class="small muted">Bix is a compact interdisciplinary engineering team building EHPAD Patrol — a small autonomous car that assists retirement homes with rounds, deliveries, and resident monitoring. We combine robotics, embedded systems, and privacy-focused cloud services to deliver practical solutions that caregivers actually want to use.</p>

        <ul style="margin-top:14px; padding-left:18px; color:#274141">
          <li>Robotics & navigation tuned for indoor care environments</li>
          <li>Wearables and sensor fusion (SpO₂, heart-rate, accelerometer)</li>
          <li>Secure, auditable data pipelines for staff workflows</li>
        </ul>
      </div>

      <div style="margin-top:18px; display:flex; gap:12px; align-items:center;">
        <a class="btn primary" href="#contact">Talk to us — investors & pilots</a>
        <a class="btn ghost" href="#team">Meet the team</a>
      </div>
    </div>

    <aside>
      <div class="card">
        <h4 style="margin:0 0 8px 0;">Team</h4>
        <div class="team-grid">
          <div class="person">
            <div class="avatar">VS</div>
            <div>
              <div style="font-weight:700">Valentin Servieres</div>
              <div class="small muted">Lead Robotics Engineer</div>
            </div>
          </div>

          <div class="person">
            <div class="avatar">HR</div>
            <div>
              <div style="font-weight:700">Hugo Rosfelder</div>
              <div class="small muted">Systems & Firmware</div>
            </div>
          </div>

          <div class="person">
            <div class="avatar">GS</div>
            <div>
              <div style="font-weight:700">Gaspar Salzenstein</div>
              <div class="small muted">Navigation & Controls</div>
            </div>
          </div>

          <div class="person">
            <div class="avatar">GC</div>
            <div>
              <div style="font-weight:700">Giovanna A. C. Grigolon</div>
              <div class="small muted">Product & UX</div>
            </div>
          </div>
        </div>
      </div>
    </aside>
  </section>

  <!-- Team expanded and contact -->
  <section id="team" class="container" style="margin-top:48px;">
    <h3 style="margin-bottom:12px;">Our approach</h3>
    <div class="card" style="display:grid; grid-template-columns:1fr 320px; gap:20px;">
      <div>
        <p class="small muted">We iterate with caregivers from day one. Prototypes are tested on-site, with attention to non-intrusive assistance and safe interaction. Our autonomous stack is built to prioritize predictable, explainable behavior — essential in sensitive care environments.</p>

        <h4 style="margin-top:18px;">Pilot program</h4>
        <p class="small muted">We run pilot deployments with custom route setups, staff training and dedicated support. Investors and care-center partners get early access to roadmap features and integration support.</p>
      </div>

      <div>
        <h4 style="margin-top:0">Contact</h4>
        <form id="contactForm" class="card" action="contact.php" method="post" aria-label="Contact form">
          <label class="small muted" for="name">Name</label>
          <input id="name" name="name" required placeholder="Your full name">

          <label class="small muted" for="email" style="margin-top:8px">Email</label>
          <input id="email" name="email" type="email" required placeholder="you@org.com">

          <label class="small muted" for="role" style="margin-top:8px">I am</label>
          <input id="role" name="role" placeholder="Investor / Care center / Press / Other">

          <label class="small muted" for="message" style="margin-top:8px">Message</label>
          <textarea id="message" name="message" placeholder="Tell us about your interest..." required></textarea>

          <div style="display:flex; gap:10px; margin-top:12px;">
            <button type="submit" class="btn primary">Send message</button>
            <button type="button" onclick="startDemo()" class="btn ghost">Request demo</button>
          </div>

          <div id="formResult" class="small muted" style="margin-top:10px; display:none;"></div>
        </form>
      </div>
    </div>
  </section>

  <footer class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
      <div class="flex">
        <div class="logo" style="width:42px;height:42px;border-radius:8px;font-size:0.95rem;">B</div>
        <div style="margin-left:10px">
          <div style="font-weight:700">Bix · EHPAD Patrol</div>
          <div class="small muted">Engineering team • Autonomous care mobility</div>
        </div>
      </div>

      <div class="small muted">© <span id="year"></span> Bix — Built with care. <span style="margin-left:12px">Privacy & Terms</span></div>
    </div>
  </footer>
</main>

<script>
  // small scripts: set year, demo flow, form UX enhancement
  document.getElementById('year').textContent = new Date().getFullYear();

  function startDemo(){
    // simple CTA flow
    alert("Thanks — we will contact you to schedule a demo. Or use the form to give details.");
    // In a production site you'd open a modal or navigate to scheduler.
  }

  // form submission UX: If contact.php exists on server it will handle post; but we provide AJAX fallback
  const form = document.getElementById('contactForm');
  form.addEventListener('submit', async function(e){
    if (!form.action || form.action.endsWith('contact.php')) {
      // let the browser submit normally to contact.php if server handles it.
      // But we also show immediate feedback to the user.
      const result = document.getElementById('formResult');
      result.style.display = 'block';
      result.textContent = 'Sending...';
      // after a short delay, leave to server:
      setTimeout(()=> result.textContent = 'If your server is configured, the message will be sent. Otherwise use email: hello@bix.example', 800);
      return;
    }
    // Otherwise, example AJAX post (for APIs)
    e.preventDefault();
    const fd = new FormData(form);
    try {
      const res = await fetch(form.action, { method:'POST', body:fd });
      const text = await res.text();
      document.getElementById('formResult').style.display = 'block';
      document.getElementById('formResult').textContent = 'Thanks — we received your message.';
    } catch(err){
      document.getElementById('formResult').style.display = 'block';
      document.getElementById('formResult').textContent = 'There was an error sending your message. Please email hello@bix.example';
    }
  });
</script>

</body>
</html>
