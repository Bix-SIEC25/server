<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Bix - EHPAD Patrol</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&family=Source+Serif+4:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <link href="index.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="./favicon.png">
    <meta name="description" content="Bix - engineers building EHPAD Patrol, a compact autonomous vehicle that helps retirement homes and caregivers.">
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
        <div class="nav">
            <div class="brand">
                <div class="logo" aria-hidden="true">
                    <!-- simple B monogram -->
                    <!-- <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect x="0.5" y="0.5" width="23" height="23" rx="6" fill="white" opacity="0.06" />
                        <path d="M7 6h4a3 3 0 0 1 0 6H7V6zM7 12h5a3 3 0 0 1 0 6H7v-6z" fill="white" />
                    </svg> -->
                    <img src="./logo.png" width="100%" height="100%">
                </div>
                <div>
                    <div style="font-weight:800">Bix</div>
                    <div class="small muted">EHPAD Patrol - Autonomous care mobility</div>
                </div>
            </div>

            <nav aria-label="Main navigation">
                <a href="#about">About</a>
                <a href="#features">Solutions</a>
                <a href="#team">Team</a>
                <a href="#contact">Contact</a>
                <a class="cta" href="./dashboard">Dashboard demo</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero container" aria-label="Hero">
            <div class="hero-left">
                <div class="eyebrow">Autonomous · Safe · Caring</div>
                <h1 class="title">EHPAD Patrol - small autonomous assistants for safer, smoother care</h1>
                <p class="subtitle">We build compact autonomous vehicles that support staff in retirement homes - measuring vital parameters & alerting to keep residents safe and reduce staff workload.</p>

                <div class="hero-ctas">
                    <a class="btn primary" href="#contact" aria-label="Contact for investors">
                        <!-- icon -->
                        <!-- <svg width="18" height="18" viewBox="0 0 24 24" fill="none" style="opacity:0.95" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 8v10a3 3 0 0 0 3 3h12" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M17 3H7a3 3 0 0 0-3 3v2" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg> -->
                        ▶ Request a demo
                    </a>

                    <a class="btn ghost" href="#about">Learn more</a>
                </div>

                <div style="margin-top:18px" class="small muted">
                    Trusted by caregivers, built by engineers - focused on reliability, privacy, and human-centered design.
                </div>
            </div>

            <div class="hero-right" aria-hidden="true">
                <!-- decorative floating small squares inside hero (kept) -->
                <div class="floater one"></div>
                <div class="floater two"></div>

                <div class="device" role="img" aria-label="EHPAD Patrol dashboard preview">
                    <div class="topbar">
                        <div style="font-weight:700">EHPAD Patrol news</div>
                        <div style="flex:1"></div>
                        <div class="small muted">updated a few days ago</div>
                    </div>

                    <div class="content">
                        <div class="card-rect">
                            <strong>SP0 review</strong>
                            <span class="small">The project is ready to start!</span>
                            <!-- <div class="small-val">Soon</div> -->
                            <div class="small-val">€100</div>
                            <span class="small">worth of equipment ordered</span>
                        </div>
                        <div class="card-rect">
                            <strong>SP1 review</strong>
                            <span class="small">The review will take place the 18th at 3p.m.</span>
                            <!-- <div class="small-val">Soon</div> -->
                            <div class="small-val">1st demo</div>
                            <span class="small">emergency stop</span>
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
                <div class="small muted">Prototypes · Deployment support</div>
            </div>

            <div class="features" aria-hidden="false">
                <div class="feature card">
                    <h4>Resident safety</h4>
                    <p>Fall detection, facial recognition, and proximity monitoring. Alerts routed to the right caregiver instantly.</p>
                </div>

                <div class="feature card">
                    <h4>Staff efficiency</h4>
                    <p>Automated supply delivery and routine rounds free caregivers to focus on residents' needs.</p>
                </div>

                <div class="feature card">
                    <h4>Data & privacy</h4>
                    <p>Local-first processing for vital signals, and privacy-by-design data handling - local server deployment.</p>
                </div>
            </div>
        </section>

        <!-- About + Team -->
        <section id="about" class="container about">
            <div>
                <div class="card">
                    <h3 style="margin-top:0">Who we are</h3>
                    <p class="small muted">Bix is a compact interdisciplinary engineering team building EHPAD Patrol - a small autonomous car that assists retirement homes with rounds, fall detection, and resident monitoring. We combine robotics, embedded systems, and privacy-focused services to deliver practical solutions that caregivers actually want to use.</p>

                    <ul style="margin-top:14px; padding-left:18px; color:#274141">
                        <li>Robotics & navigation tuned for indoor care environments</li>
                        <li>Wearables and sensor fusion (SpO₂, heart-rate, accelerometer)</li>
                        <!-- <li>Secure, auditable data pipelines for staff workflows</li> -->
                    </ul>
                </div>

                <div style="margin-top:18px; display:flex; gap:12px; align-items:center;">
                    <a class="btn primary" href="#contact">Talk to us - investors & clients</a>
                    <a class="btn ghost" href="#team">Meet the team</a>
                </div>
            </div>

            <aside>
                <div class="card">
                    <h4 style="margin:0 0 8px 0;">Team</h4>
                    <div class="team-grid">

                        <div class="person">
                            <div class="avatar">Ba</div>
                            <div>
                                <div style="font-weight:700">Batiste</div>
                                <div class="small muted">AI recognition</div>
                            </div>
                        </div>

                        <div class="person">
                            <div class="avatar">Co</div>
                            <div>
                                <div style="font-weight:700">Cornelius</div>
                                <div class="small muted">SLAM & Navigation</div>
                            </div>
                        </div>

                        <div class="person">
                            <div class="avatar">Ga</div>
                            <div>
                                <div style="font-weight:700">Gaspar</div>
                                <div class="small muted">Navigation & Controls</div>
                            </div>
                        </div>

                        <div class="person">
                            <div class="avatar">Gi</div>
                            <div>
                                <div style="font-weight:700">Giovanna</div>
                                <div class="small muted">AI recognition</div>
                            </div>
                        </div>

                        <div class="person">
                            <div class="avatar">Hu</div>
                            <div>
                                <div style="font-weight:700">Hugo</div>
                                <div class="small muted">AI recognition</div>
                            </div>
                        </div>

                        <div class="person">
                            <div class="avatar">Sa</div>
                            <div>
                                <div style="font-weight:700">Sammy</div>
                                <div class="small muted">Wristband</div>
                            </div>
                        </div>

                        <div class="person">
                            <div class="avatar">Va</div>
                            <div>
                                <div style="font-weight:700">Valentin</div>
                                <div class="small muted">Software developper</div>
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
                    <p class="small muted">We iterate with caregivers from day one. Prototypes are tested on-site, with attention to non-intrusive assistance and safe interaction. Our autonomous stack is built to prioritize predictable, explainable behavior - essential in sensitive care environments.</p>

                    <h4 style="margin-top:18px;">Warning</h4>
                    <p class="small muted">The project is still in heavy development so you may not have demonstration until January 2026.</p>
                    <!-- <h4 style="margin-top:18px;">Pilot program</h4>
                    <p class="small muted">We run pilot deployments with custom route setups, staff training and dedicated support. Investors and care-center partners get early access to roadmap features and integration support.</p> -->
                </div>

                <div id="contact" aria-label="Contact area">
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
                    <div class="logo" style="width:42px;height:42px;border-radius:8px;font-size:0.95rem;">
                        <!-- B -->
                    <img src="./logo.png" width="100%" height="100%">

                    </div>
                    <div style="margin-left:10px">
                        <div style="font-weight:700">Bix · EHPAD Patrol</div>
                        <div class="small muted">Engineering team</div>
                    </div>
                </div>

                <div class="small muted">© <span id="year"></span> Bix - Built with care. <span style="margin-left:12px">Privacy & Terms</span></div>
            </div>
        </footer>
    </main>

    <script>
        // small scripts: set year, demo flow, form UX enhancement
        document.getElementById('year').textContent = new Date().getFullYear();

        function startDemo() {
            // simple CTA flow
            alert("Thanks - we will contact you to schedule a demo. Or use the form to give details.");
            // In a production site you'd open a modal or navigate to scheduler.
        }

        // form submission UX: If contact.php exists on server it will handle post; but we provide AJAX fallback
        const form = document.getElementById('contactForm');
        form.addEventListener('submit', async function(e) {
            if (!form.action || form.action.endsWith('contact.php')) {
                // let the browser submit normally to contact.php if server handles it.
                // But we also show immediate feedback to the user.
                const result = document.getElementById('formResult');
                result.style.display = 'block';
                result.textContent = 'Sending...';
                // after a short delay, leave to server:
                setTimeout(() => result.textContent = 'If your server is configured, the message will be sent. Otherwise use email: hello@bix.example', 800);
                return;
            }
            // Otherwise, example AJAX post (for APIs)
            e.preventDefault();
            const fd = new FormData(form);
            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: fd
                });
                const text = await res.text();
                document.getElementById('formResult').style.display = 'block';
                document.getElementById('formResult').textContent = 'Thanks - we received your message.';
            } catch (err) {
                document.getElementById('formResult').style.display = 'block';
                document.getElementById('formResult').textContent = 'There was an error sending your message. Please email hello@bix.example';
            }
        });
    </script>

</body>

</html>