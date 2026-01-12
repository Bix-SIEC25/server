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

                <!-- <div class="hero-ctas">
                    <a class="btn primary" href="#contact" aria-label="Contact for investors">▶ Request a demo</a>
                    <a class="btn ghost" href="#about">Learn more</a>
                </div> -->

                <!-- Fall visual / preview (hidden by default) -->
                <div class="card fall-visual-card" id="fall-visual">
                    <h3 style="margin:0 0 10px 0">Fall visualization</h3>
                    <div id="fall-visual-content" class="no-data">No fall to display</div>
                </div>

                <div id="fall-card-container" class="card-resident" aria-live="polite" style="margin-top:12px;"></div>

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

    <script src="dashboard.js"></script>
</body>

</html>