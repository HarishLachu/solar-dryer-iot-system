<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['Category'] != 1) {
    header('Location: Login/login.php'); exit;
}
$farmerName = $_SESSION['Name'] ?? 'Farmer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Solar Drying Area — Farm Management</title>
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<!--[if lte IE 8]><script src="css/ie/html5shiv.js"></script><![endif]-->
<link rel="stylesheet" href="login.css"/>
<script src="js/jquery.min.js"></script>
<script src="js/skel.min.js"></script>
<script src="js/skel-layers.min.js"></script>
<script src="js/init.js"></script>
<noscript>
  <link rel="stylesheet" href="css/skel.css" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/style-xlarge.css" />
</noscript>
<link rel="stylesheet" href="indexFooter.css" />
<!--[if lte IE 8]><link rel="stylesheet" href="css/ie/v8.css" /><![endif]-->
<style>
/* ===== BASE ===================================================== */
*{box-sizing:border-box;}
body { background:#1a1a2e; color:#e0e0e0; font-family:'Segoe UI',sans-serif; min-height:100vh; }
.page-wrap { max-width:1400px; margin:0 auto; padding:20px 16px 60px; }

/* ===== TOP BAR ================================================== */
.top-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:24px; }
.top-bar h1 { font-size:1.6rem; font-weight:700; color:#ffd700; margin:0; }
.top-bar h1 span { color:#fff; }
.wokwi-btn { background:#7c3aed; color:#fff; border:none; padding:8px 18px; border-radius:8px; font-size:.85rem; cursor:pointer; text-decoration:none; }
.wokwi-btn:hover { background:#6d28d9; color:#fff; }

/* ===== STATUS BAR =============================================== */
.status-bar { background:#0f3460; border-radius:10px; padding:10px 18px; display:flex; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:20px; font-size:.84rem; }
.live-dot { width:10px; height:10px; border-radius:50%; background:#22c55e; display:inline-block; animation:pulse 1.2s infinite; }
.live-dot.offline { background:#ef4444; animation:none; }
@keyframes pulse { 0%,100%{opacity:1}50%{opacity:.3} }
.status-badge { background:#1e3a5f; padding:3px 10px; border-radius:20px; color:#93c5fd; }

/* ===== SECTION HEADERS ========================================== */
.section-hdr { color:#ffd700; font-size:1.08rem; font-weight:700; margin:28px 0 14px; padding-bottom:6px; border-bottom:2px solid #1e4080; }

/* ===== ALERT BANNERS ============================================ */
#alertsZone { margin-bottom:16px; }
.alert-banner { border-radius:10px; padding:12px 18px; margin-bottom:8px; display:flex; align-items:center; justify-content:space-between; font-size:.88rem; font-weight:600; animation:slideIn .3s ease; }
@keyframes slideIn { from{transform:translateX(-20px);opacity:0} to{transform:translateX(0);opacity:1} }
.alert-DANGER    { background:#7f1d1d; border-left:4px solid #ef4444; color:#fca5a5; }
.alert-OVERHEAT  { background:#78350f; border-left:4px solid #f59e0b; color:#fcd34d; }
.alert-TOO_COLD  { background:#1e3a5f; border-left:4px solid #60a5fa; color:#bfdbfe; }
.alert-HIGH_HUMIDITY { background:#14532d; border-left:4px solid #4ade80; color:#bbf7d0; }
.alert-close-btn { background:none; border:none; color:inherit; cursor:pointer; font-size:1.1rem; opacity:.7; }
.alert-close-btn:hover { opacity:1; }

/* ===== DANGER OVERLAY =========================================== */
#dangerOverlay { display:none; position:fixed; inset:0; z-index:9999; background:rgba(127,29,29,.92); flex-direction:column; align-items:center; justify-content:center; animation:dangerFlash .5s ease infinite alternate; }
#dangerOverlay.visible { display:flex; }
@keyframes dangerFlash { from{background:rgba(127,29,29,.92)} to{background:rgba(185,28,28,.96)} }
#dangerOverlay .dng-icon { font-size:5rem; animation:shake .4s infinite; }
@keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-8px)} 75%{transform:translateX(8px)} }
#dangerOverlay h2 { color:#fca5a5; font-size:2.2rem; font-weight:900; margin:12px 0 8px; text-transform:uppercase; letter-spacing:4px; }
#dangerOverlay p { color:#fde68a; font-size:1.1rem; max-width:500px; text-align:center; }
#dangerOverlay .dng-close { margin-top:20px; background:#991b1b; color:#fff; border:2px solid #ef4444; padding:10px 30px; border-radius:10px; font-weight:700; cursor:pointer; font-size:1rem; }
#dangerOverlay .dng-close:hover { background:#7f1d1d; }
#dangerOverlay .dng-temp { font-size:3.5rem; font-weight:900; color:#fff; margin:6px 0; }

/* ===== CARDS GRID =============================================== */
.cards-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-bottom:20px; }
.sensor-card { background:linear-gradient(135deg,#0f3460,#16213e); border-radius:14px; padding:20px 18px; text-align:center; border:1px solid #1e4080; position:relative; overflow:hidden; transition:border-color .3s,box-shadow .3s; }
.sensor-card.alert-glow { border-color:#ef4444; box-shadow:0 0 20px rgba(239,68,68,.4); }
.sensor-card .label { font-size:.78rem; color:#93c5fd; letter-spacing:1px; text-transform:uppercase; margin-bottom:6px; }
.sensor-card .value { font-size:2.4rem; font-weight:800; color:#fff !important; -webkit-text-fill-color:#fff !important; line-height:1.1; }
.sensor-card .unit  { font-size:.9rem; color:#7dd3fc; margin-top:2px; }
.sensor-card .sub   { font-size:.76rem; color:#64748b; margin-top:4px; }
.card-temp  .value { color:#f97316 !important; -webkit-text-fill-color:#f97316 !important; }
.card-humid .value { color:#38bdf8 !important; -webkit-text-fill-color:#38bdf8 !important; }
.card-soil  .value { color:#4ade80 !important; -webkit-text-fill-color:#4ade80 !important; }
.card-fan   .value { color:#a78bfa !important; -webkit-text-fill-color:#a78bfa !important; }

/* ===== FAN ANIMATION ============================================ */
.fan-wrap { position:relative; width:90px; height:90px; margin:6px auto; }
.fan-svg { width:90px; height:90px; }
.fan-blades { transform-origin:45px 45px; }
.fan-blades.spinning { animation:spin 0.5s linear infinite; }
.fan-blades.slowing  { animation:spin 2.5s linear infinite; }
.fan-blades.stopped  { animation:none; }
@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
.fan-status-lbl { font-size:.72rem; color:#64748b; margin-top:2px; }

/* ===== SENSOR VISUALIZATION ===================================== */
.sensor-viz { background:#16213e; border-radius:14px; padding:20px; border:1px solid #1e4080; margin-bottom:20px; }
.sensor-viz h5 { color:#ffd700; font-size:.95rem; margin-bottom:14px; }
.viz-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:18px; }
.viz-item { background:#0f1e40; border-radius:12px; padding:16px; border:1px solid #1e3a5f; position:relative; overflow:hidden; }
.viz-item h6 { color:#93c5fd; font-size:.82rem; margin-bottom:10px; font-weight:700; }
.viz-item .viz-reading { font-size:1.5rem; font-weight:800; color:#fff !important; -webkit-text-fill-color:#fff !important; margin-top:8px; }
.viz-item .viz-desc { font-size:.74rem; color:#64748b; margin-top:6px; }
.viz-item svg { display:block; margin:0 auto 8px; }
.data-pulse { animation:dataPulse 2s ease-in-out infinite; }
@keyframes dataPulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.6;transform:scale(1.05)} }
.wire-anim { stroke-dasharray:6,4; animation:wireDash 1s linear infinite; }
@keyframes wireDash { to{stroke-dashoffset:-10;} }

/* ===== HOW-IT-WORKS ============================================= */
.hiw-card { background:#16213e; border-radius:14px; padding:20px; border:1px solid #1e4080; margin-bottom:20px; }
.hiw-card h5 { color:#ffd700; font-size:.95rem; margin-bottom:16px; }
.hiw-steps { display:flex; gap:0; overflow-x:auto; padding-bottom:8px; }
.hiw-step { flex:0 0 140px; text-align:center; position:relative; padding:16px 8px 10px; }
.hiw-step .step-num { width:32px; height:32px; border-radius:50%; background:#1e3a5f; color:#93c5fd; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:.85rem; border:2px solid #2d4a7a; transition:all .4s; }
.hiw-step.active .step-num { background:#7c3aed; color:#fff; border-color:#a78bfa; box-shadow:0 0 15px rgba(124,58,237,.5); transform:scale(1.15); }
.hiw-step .step-icon { font-size:1.8rem; margin:8px 0 4px; }
.hiw-step .step-lbl { font-size:.7rem; color:#93c5fd; line-height:1.2; }
.hiw-step.active .step-lbl { color:#fff; font-weight:600; }
.hiw-arrow { flex:0 0 30px; display:flex; align-items:center; justify-content:center; color:#60a5fa; font-size:1.2rem; }
.hiw-arrow.flow { animation:arrowPulse 1s ease infinite; }
@keyframes arrowPulse { 0%,100%{opacity:.4;transform:translateX(0)} 50%{opacity:1;transform:translateX(4px)} }
.hiw-narrative { margin-top:14px; background:#0f1e40; border-radius:10px; padding:14px; font-size:.84rem; color:#c4b5fd; min-height:50px; border-left:3px solid #7c3aed; line-height:1.5; }

/* ===== CROP GUIDE =============================================== */
.crop-guide { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:16px; margin-bottom:20px; }
.crop-card { background:#16213e; border-radius:14px; padding:18px; border:1px solid #1e4080; position:relative; }
.crop-card h6 { color:#ffd700; font-size:.92rem; margin-bottom:10px; }
.crop-card .crop-icon { font-size:2rem; float:left; margin-right:10px; }
.crop-detail { display:grid; grid-template-columns:1fr 1fr; gap:5px 12px; font-size:.78rem; margin-top:10px; }
.crop-detail dt { color:#64748b; }
.crop-detail dd { color:#e2e8f0 !important; -webkit-text-fill-color:#e2e8f0 !important; font-weight:600; margin:0; }
.crop-card .crop-tip { font-size:.74rem; color:#4ade80; margin-top:10px; border-top:1px solid #1e3a5f; padding-top:8px; font-style:italic; }
.crop-gauge { height:6px; border-radius:99px; background:#0f2044; margin-top:4px; overflow:hidden; }
.crop-gauge .fill { height:100%; border-radius:99px; transition:width .6s; }

/* ===== CONTROL PANEL ============================================ */
.control-panel { background:linear-gradient(135deg,#0f3460,#16213e); border-radius:14px; padding:22px; border:1px solid #1e4080; margin-bottom:20px; }
.control-panel h4 { color:#ffd700; margin-bottom:18px; font-size:1.05rem; }
.cp-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:14px; align-items:end; }
.cp-group label { font-size:.78rem; color:#93c5fd; letter-spacing:.5px; margin-bottom:4px; display:block; }
.cp-group select,.cp-group input { width:100%; background:#0f2044; border:1px solid #1e4080; border-radius:8px; padding:8px 10px; color:#fff !important; -webkit-text-fill-color:#fff !important; font-size:.88rem; }
.cp-group select:focus,.cp-group input:focus { outline:none; border-color:#7c3aed; }
.cp-group select option { background:#0f2044; }
.btn-start { background:#16a34a; color:#fff; border:none; padding:10px 22px; border-radius:8px; cursor:pointer; font-weight:700; font-size:.9rem; width:100%; }
.btn-start:hover { background:#15803d; }
.btn-stop  { background:#dc2626; color:#fff; border:none; padding:10px 22px; border-radius:8px; cursor:pointer; font-weight:700; font-size:.9rem; width:100%; }
.btn-stop:hover  { background:#b91c1c; }
.btn-emergency { background:#7f1d1d; color:#fca5a5; border:1px solid #ef4444; padding:8px 16px; border-radius:8px; cursor:pointer; font-size:.82rem; width:100%; margin-top:6px; }
.btn-emergency:hover { background:#991b1b; }

/* ===== PROGRESS BAR ============================================= */
.session-progress { background:#0f3460; border-radius:14px; padding:16px 20px; margin-bottom:20px; border:1px solid #1e4080; display:none; }
.session-progress.visible { display:block; }
.prog-header { display:flex; justify-content:space-between; margin-bottom:8px; font-size:.82rem; color:#93c5fd; }
.prog-bar-bg { background:#0f2044; border-radius:99px; height:16px; overflow:hidden; }
.prog-bar-fill { height:100%; background:linear-gradient(90deg,#16a34a,#22c55e); border-radius:99px; transition:width .5s ease; }
.prog-details { display:flex; gap:20px; flex-wrap:wrap; margin-top:10px; font-size:.8rem; color:#7dd3fc; }
.prog-details span b { color:#fff; }

/* ===== CHARTS =================================================== */
.charts-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(350px,1fr)); gap:16px; margin-bottom:20px; }
.chart-card { background:#16213e; border-radius:14px; padding:16px; border:1px solid #1e4080; }
.chart-card h5 { color:#93c5fd; font-size:.85rem; margin-bottom:12px; }
.chart-card .chart-wrap { position:relative; height:180px; width:100%; }

/* ===== TABLE ==================================================== */
.table-card { background:#16213e; border-radius:14px; padding:16px; border:1px solid #1e4080; margin-bottom:20px; }
.table-card h5 { color:#93c5fd; font-size:.85rem; margin-bottom:12px; }
.tbl { width:100%; border-collapse:collapse; font-size:.82rem; }
.tbl th { background:#0f3460; color:#93c5fd; padding:8px 12px; text-align:left; }
.tbl td { padding:7px 12px; color:#e2e8f0 !important; -webkit-text-fill-color:#e2e8f0 !important; border-bottom:1px solid #1e3a5f; }
.tbl tr:hover td { background:#0f2044; }
.fan-on  { color:#4ade80 !important; -webkit-text-fill-color:#4ade80 !important; font-weight:700; }
.fan-off { color:#64748b !important; -webkit-text-fill-color:#64748b !important; }

/* ===== HISTORY TABLE ============================================ */
.history-card { background:#16213e; border-radius:14px; padding:16px; border:1px solid #1e4080; margin-bottom:20px; }
.history-card h5 { color:#93c5fd; font-size:.85rem; margin-bottom:12px; }
.badge-running   { background:#166534; color:#86efac; padding:2px 8px; border-radius:20px; font-size:.75rem; }
.badge-completed { background:#1e3a5f; color:#7dd3fc; padding:2px 8px; border-radius:20px; font-size:.75rem; }
.badge-stopped   { background:#4b1113; color:#fca5a5; padding:2px 8px; border-radius:20px; font-size:.75rem; }

/* ===== ARCHITECTURE DIAGRAM ===================================== */
.arch-card { background:#16213e; border-radius:14px; padding:20px; border:1px solid #1e4080; margin-bottom:20px; }
.arch-card h5 { color:#ffd700; font-size:.95rem; margin-bottom:16px; }
.arch-svg { width:100%; max-width:960px; margin:0 auto; display:block; overflow:visible; }
@keyframes flowDot { 0%{offset-distance:0%} 100%{offset-distance:100%} }
.flow-dot { fill:#60a5fa; offset-distance:0%; animation:flowDot 2s linear infinite; }
.flow-dot-green { fill:#4ade80; offset-distance:0%; animation:flowDot 2.5s linear infinite; }

/* ===== DEMO GUIDE =============================================== */
.demo-card { background:linear-gradient(135deg,#1e3a5f,#16213e); border-radius:14px; padding:22px; border:1px solid #2d4a7a; margin-bottom:20px; }
.demo-card h5 { color:#ffd700; font-size:1rem; margin-bottom:14px; cursor:pointer; }
.demo-card h5:hover { color:#fde68a; }
.demo-steps { counter-reset:demo; }
.demo-step { position:relative; padding:12px 14px 12px 52px; margin-bottom:10px; background:#0f1e40; border-radius:10px; border-left:3px solid #7c3aed; font-size:.84rem; color:#e2e8f0; line-height:1.5; }
.demo-step::before { counter-increment:demo; content:counter(demo); position:absolute; left:14px; top:12px; width:26px; height:26px; border-radius:50%; background:#7c3aed; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.8rem; }
.demo-step b { color:#ffd700; }
.demo-step code { background:#0f3460; padding:1px 6px; border-radius:4px; color:#7dd3fc; font-size:.8rem; }

/* ===== RESPONSIVE =============================================== */
@media(max-width:600px){
  .top-bar h1{font-size:1.2rem;}
  .sensor-card .value{font-size:1.8rem;}
  .hiw-steps{flex-wrap:wrap;}
  .hiw-step{flex:0 0 100px;}
}
</style>
</head>
<body>
<?php include 'menu.php'; ?>
<!-- DANGER OVERLAY -->
<div id="dangerOverlay">
  <div class="dng-icon">🚨</div>
  <h2>SYSTEM HALTED</h2>
  <div class="dng-temp" id="dngTemp">--°C</div>
  <p id="dngMsg">Temperature exceeds danger limit! Fan has been automatically stopped to protect the crop.</p>
  <button class="dng-close" onclick="closeDanger()">✕ Acknowledge &amp; Dismiss</button>
</div>

<div class="page-wrap">

  <!-- TOP BAR -->
  <div class="top-bar">
    <h1>☀️ Solar Drying Area <span>Dashboard</span></h1>
    <div style="display:flex;gap:10px;align-items:center;">
      <span style="color:#64748b;font-size:.8rem;">Farmer: <?= htmlspecialchars($farmerName) ?></span>
      <a href="https://wokwi.com/projects/457011489532622849" target="_blank" class="wokwi-btn">▶ Simulate on Wokwi</a>
    </div>
  </div>

  <!-- STATUS BAR -->
  <div class="status-bar">
    <span class="live-dot" id="liveDot"></span>
    <span id="connLabel" style="color:#22c55e;font-weight:600;">Connecting…</span>
    <span class="status-badge" id="lastUpdate">No data yet</span>
    <span class="status-badge" id="sessionBadge" style="display:none;background:#1e3a0f;color:#86efac;">● Session Active</span>
    <span class="status-badge" id="fanBadge" style="display:none;background:#2e1065;color:#c4b5fd;">Fan: OFF</span>
    <span style="flex:1"></span>
    <span style="color:#475569;font-size:.78rem;">Refresh: every 5s via SSE</span>
  </div>

  <!-- ALERT BANNERS -->
  <div id="alertsZone"></div>

  <!-- ============================================================ -->
  <!-- 1. HOW IT WORKS — Animated Step-by-Step                     -->
  <!-- ============================================================ -->
  <div class="section-hdr">📡 How the Arduino IoT System Works</div>
  <div class="hiw-card">
    <h5>Step-by-Step Data Flow — Watch the animation below</h5>
    <div class="hiw-steps" id="hiwSteps">
      <div class="hiw-step" data-step="1"><div class="step-num">1</div><div class="step-icon">🌡️</div><div class="step-lbl">Sensor reads<br>temperature</div></div>
      <div class="hiw-arrow">→</div>
      <div class="hiw-step" data-step="2"><div class="step-num">2</div><div class="step-icon">🔢</div><div class="step-lbl">Converts to<br>digital (binary)</div></div>
      <div class="hiw-arrow">→</div>
      <div class="hiw-step" data-step="3"><div class="step-num">3</div><div class="step-icon">📟</div><div class="step-lbl">ESP32 reads<br>via GPIO pin</div></div>
      <div class="hiw-arrow">→</div>
      <div class="hiw-step" data-step="4"><div class="step-num">4</div><div class="step-icon">📶</div><div class="step-lbl">WiFi sends<br>HTTP POST</div></div>
      <div class="hiw-arrow">→</div>
      <div class="hiw-step" data-step="5"><div class="step-num">5</div><div class="step-icon">🌐</div><div class="step-lbl">ngrok tunnel<br>to localhost</div></div>
      <div class="hiw-arrow">→</div>
      <div class="hiw-step" data-step="6"><div class="step-num">6</div><div class="step-icon">🗄️</div><div class="step-lbl">PHP saves<br>to MySQL</div></div>
      <div class="hiw-arrow">→</div>
      <div class="hiw-step" data-step="7"><div class="step-num">7</div><div class="step-icon">📊</div><div class="step-lbl">SSE pushes<br>to browser</div></div>
      <div class="hiw-arrow">→</div>
      <div class="hiw-step" data-step="8"><div class="step-num">8</div><div class="step-icon">⚡</div><div class="step-lbl">Dashboard<br>displays live</div></div>
      <div class="hiw-arrow" style="color:#4ade80;">⟲</div>
      <div class="hiw-step" data-step="9"><div class="step-num">9</div><div class="step-icon">🎛️</div><div class="step-lbl">User sends<br>command</div></div>
      <div class="hiw-arrow" style="color:#4ade80;">→</div>
      <div class="hiw-step" data-step="10"><div class="step-num">10</div><div class="step-icon">🌀</div><div class="step-lbl">Arduino polls<br>& controls fan</div></div>
    </div>
    <div class="hiw-narrative" id="hiwNarrative">
      <strong>▶ Watching animation...</strong> Each step lights up in sequence showing real-time data flow.
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- 2. SENSOR VISUALIZATION — Virtual Hardware Images            -->
  <!-- ============================================================ -->
  <div class="section-hdr">🔧 Virtual Sensor Hardware (Wokwi Simulation)</div>
  <div class="sensor-viz">
    <h5>Since we don't have physical sensors, here is the virtual hardware running in Wokwi simulator</h5>
    <div class="viz-grid">
      <!-- DHT22 -->
      <div class="viz-item">
        <h6>DHT22 Temperature &amp; Humidity Sensor</h6>
        <svg width="120" height="95" viewBox="0 0 120 95">
          <rect x="10" y="5" width="100" height="70" rx="6" fill="#f0f0f0" stroke="#999" stroke-width="2"/>
          <rect x="25" y="75" width="8" height="16" rx="2" fill="#888"/>
          <rect x="56" y="75" width="8" height="16" rx="2" fill="#888"/>
          <rect x="87" y="75" width="8" height="16" rx="2" fill="#888"/>
          <text x="60" y="28" text-anchor="middle" fill="#333" font-size="10" font-weight="bold">DHT22</text>
          <text x="60" y="45" text-anchor="middle" fill="#0066cc" font-size="9">AM2302</text>
          <circle cx="35" cy="58" r="8" fill="none" stroke="#cc3300" stroke-width="1.5"/>
          <line x1="35" y1="55" x2="35" y2="52" stroke="#cc3300" stroke-width="2" class="data-pulse"/>
          <circle cx="85" cy="58" r="8" fill="none" stroke="#0066cc" stroke-width="1.5"/>
          <text x="85" y="61" text-anchor="middle" fill="#0066cc" font-size="7">💧</text>
          <!-- Data wire -->
          <line x1="56" y1="91" x2="56" y2="95" stroke="#00cc44" stroke-width="2" class="wire-anim"/>
        </svg>
        <div class="viz-reading" style="color:#f97316" id="vizTemp">-- °C</div>
        <div class="viz-reading" style="color:#38bdf8;font-size:1.1rem;" id="vizHumid">-- %</div>
        <div class="viz-desc">Reads temperature (-40~80°C) and humidity (0-100%) using capacitive sensor. Communicates via one-wire digital protocol on <b>GPIO 4</b>.</div>
      </div>

      <!-- Soil Moisture / Potentiometer -->
      <div class="viz-item">
        <h6>Soil Moisture Sensor (Simulated via Potentiometer)</h6>
        <svg width="120" height="85" viewBox="0 0 120 85">
          <rect x="35" y="5" width="50" height="50" rx="25" fill="#1a5276" stroke="#2980b9" stroke-width="2"/>
          <line x1="60" y1="55" x2="60" y2="75" stroke="#888" stroke-width="3"/>
          <circle cx="60" cy="30" r="15" fill="none" stroke="#4ade80" stroke-width="2" stroke-dasharray="4,3"/>
          <line x1="60" y1="30" x2="72" y2="22" stroke="#ffd700" stroke-width="2" class="data-pulse"/>
          <circle cx="60" cy="30" r="3" fill="#ffd700"/>
          <!-- Pins -->
          <rect x="30" y="75" width="8" height="10" fill="#888" rx="1"/>
          <rect x="56" y="75" width="8" height="10" fill="#888" rx="1"/>
          <rect x="82" y="75" width="8" height="10" fill="#888" rx="1"/>
          <text x="34" y="83" fill="#ccc" font-size="6">VCC</text>
          <text x="57" y="83" fill="#ccc" font-size="6">SIG</text>
          <text x="82" y="83" fill="#ccc" font-size="6">GND</text>
        </svg>
        <div class="viz-reading" style="color:#4ade80" id="vizSoil">-- %</div>
        <div class="viz-desc">Potentiometer simulates a capacitive soil sensor in Wokwi. Analog value (0-4095) is mapped to 0-100% moisture. Connected to <b>GPIO 34</b> (ADC).</div>
      </div>

      <!-- ESP32 Board -->
      <div class="viz-item">
        <h6>ESP32 DevKit C V4 — Microcontroller</h6>
        <svg width="140" height="100" viewBox="0 0 140 100">
          <rect x="10" y="5" width="120" height="80" rx="4" fill="#1a1a2e" stroke="#3498db" stroke-width="2"/>
          <!-- Chip -->
          <rect x="40" y="20" width="55" height="35" rx="3" fill="#2c3e50" stroke="#555" stroke-width="1"/>
          <text x="67" y="40" text-anchor="middle" fill="#eee" font-size="8" font-weight="bold">ESP32</text>
          <text x="67" y="52" text-anchor="middle" fill="#7dd3fc" font-size="6">WiFi + BLE</text>
          <!-- USB -->
          <rect x="55" y="82" width="30" height="14" rx="2" fill="#555" stroke="#888" stroke-width="1"/>
          <text x="70" y="92" text-anchor="middle" fill="#ccc" font-size="6">USB</text>
          <!-- LEDs -->
          <circle cx="20" cy="15" r="3" fill="#22c55e" id="vizLed1" class="data-pulse"/>
          <circle cx="30" cy="15" r="3" fill="#ef4444" id="vizLed2"/>
          <!-- GPIO labels -->
          <text x="15" y="48" fill="#93c5fd" font-size="5">GPIO4→DHT</text>
          <text x="15" y="58" fill="#93c5fd" font-size="5">GPIO34→SOIL</text>
          <text x="15" y="68" fill="#93c5fd" font-size="5">GPIO26→FAN</text>
          <!-- Antenna -->
          <line x1="120" y1="10" x2="132" y2="3" stroke="#ffd700" stroke-width="1.5"/>
          <line x1="132" y1="3" x2="135" y2="10" stroke="#ffd700" stroke-width="1.5"/>
        </svg>
        <div class="viz-desc">The brain of the system. Reads sensors, connects to WiFi, sends data via HTTP to our PHP backend, receives commands, and controls the fan relay.</div>
      </div>

      <!-- Fan/Relay -->
      <div class="viz-item">
        <h6>Fan Relay Module (LED simulates in Wokwi)</h6>
        <svg width="120" height="85" viewBox="0 0 120 85">
          <rect x="15" y="5" width="90" height="55" rx="6" fill="#14532d" stroke="#4ade80" stroke-width="2"/>
          <text x="60" y="25" text-anchor="middle" fill="#86efac" font-size="9" font-weight="bold">RELAY</text>
          <!-- LED indicator -->
          <circle cx="60" cy="40" r="10" fill="#333" stroke="#4ade80" stroke-width="1.5" id="vizFanLed"/>
          <!-- Screw terminals -->
          <rect x="25" y="60" width="12" height="12" rx="2" fill="#555" stroke="#888" stroke-width="1"/>
          <rect x="54" y="60" width="12" height="12" rx="2" fill="#555" stroke="#888" stroke-width="1"/>
          <rect x="83" y="60" width="12" height="12" rx="2" fill="#555" stroke="#888" stroke-width="1"/>
          <text x="31" y="82" text-anchor="middle" fill="#ccc" font-size="5">COM</text>
          <text x="60" y="82" text-anchor="middle" fill="#ccc" font-size="5">NO</text>
          <text x="89" y="82" text-anchor="middle" fill="#ccc" font-size="5">NC</text>
        </svg>
        <div class="viz-reading" style="color:#a78bfa" id="vizFanStatus">OFF</div>
        <div class="viz-desc">Green LED on <b>GPIO 26</b> simulates a relay controlling the solar dryer fan. When ON, the fan circulates hot air for even drying.</div>
      </div>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- 3. CROP DRYING GUIDE                                         -->
  <!-- ============================================================ -->
  <div class="section-hdr">🌾 Crop Drying Guide — Which Seed &amp; How to Dry</div>
  <div class="crop-guide" id="cropGuide">
    <!-- Filled by JS from crop_profiles -->
  </div>

  <!-- ============================================================ -->
  <!-- CONTROL PANEL                                                -->
  <!-- ============================================================ -->
  <div class="section-hdr">🎛️ Session Control Panel</div>
  <div class="control-panel">
    <h4>Start a Drying Session</h4>
    <div class="cp-grid">
      <div class="cp-group"><label>CROP</label>
        <select id="cropSelect" onchange="loadCropProfile()"><option value="">-- Select crop --</option></select>
      </div>
      <div class="cp-group"><label>TARGET TEMP (°C)</label>
        <input type="number" id="targetTemp" value="50" min="30" max="75" step="0.5">
      </div>
      <div class="cp-group"><label>DURATION (hours)</label>
        <input type="number" id="durationHrs" value="6" min="0.0167" max="48" step="0.0167">
      </div>
      <div class="cp-group"><label>&nbsp;</label>
        <button class="btn-start" onclick="startSession()">▶ START DRYING</button>
      </div>
      <div class="cp-group"><label>&nbsp;</label>
        <button class="btn-stop" onclick="stopSession()">■ STOP SESSION</button>
        <button class="btn-emergency" onclick="emergencyStop()">⚠ EMERGENCY STOP</button>
      </div>
    </div>
    <div id="cropDesc" style="margin-top:12px;font-size:.8rem;color:#64748b;display:none;"></div>
  </div>

  <!-- SESSION PROGRESS -->
  <div class="session-progress" id="sessionProg">
    <div class="prog-header">
      <span id="progCropLabel">Drying: —</span>
      <span id="progTimeLabel">Time remaining: —</span>
    </div>
    <div class="prog-bar-bg"><div class="prog-bar-fill" id="progFill" style="width:0%"></div></div>
    <div class="prog-details">
      <span>Start: <b id="progStart">—</b></span>
      <span>Target: <b id="progTarget">—°C</b></span>
      <span>Duration: <b id="progDur">—</b></span>
      <span>Elapsed: <b id="progElapsed">—</b></span>
    </div>
  </div>

  <!-- SENSOR CARDS + FAN CARD -->
  <div class="section-hdr">📈 Live Sensor Readings</div>
  <div class="cards-row">
    <div class="sensor-card card-temp" id="cardTemp">
      <div class="label">🌡 Temperature</div>
      <div class="value" id="valTemp">--</div>
      <div class="unit">°C</div>
      <div class="sub" id="subTemp">Waiting for sensor...</div>
    </div>
    <div class="sensor-card card-humid" id="cardHumid">
      <div class="label">💧 Humidity</div>
      <div class="value" id="valHumid">--</div>
      <div class="unit">%</div>
      <div class="sub" id="subHumid">–</div>
    </div>
    <div class="sensor-card card-soil">
      <div class="label">🌱 Soil Moisture</div>
      <div class="value" id="valSoil">--</div>
      <div class="unit">% moisture</div>
      <div class="sub" id="subSoil">–</div>
    </div>
    <div class="sensor-card card-fan" id="cardFan">
      <div class="label">🌀 Fan / Ventilation</div>
      <div class="fan-wrap">
        <svg class="fan-svg" viewBox="0 0 90 90">
          <circle cx="45" cy="45" r="43" fill="#1a1a3e" stroke="#2d2d6e" stroke-width="1.5"/>
          <g class="fan-blades stopped" id="fanBlades">
            <ellipse cx="45" cy="22" rx="9" ry="20" fill="#7c3aed" opacity=".85" transform="rotate(0 45 45)"/>
            <ellipse cx="45" cy="22" rx="9" ry="20" fill="#7c3aed" opacity=".85" transform="rotate(90 45 45)"/>
            <ellipse cx="45" cy="22" rx="9" ry="20" fill="#7c3aed" opacity=".85" transform="rotate(180 45 45)"/>
            <ellipse cx="45" cy="22" rx="9" ry="20" fill="#7c3aed" opacity=".85" transform="rotate(270 45 45)"/>
            <circle cx="45" cy="45" r="7" fill="#a78bfa"/>
            <circle cx="45" cy="45" r="3.5" fill="#1a1a3e"/>
          </g>
        </svg>
      </div>
      <div class="value" id="valFan" style="font-size:1.4rem;">OFF</div>
      <div class="fan-status-lbl" id="fanModeLabel">Auto mode</div>
    </div>
  </div>

  <!-- CHARTS -->
  <div class="charts-row">
    <div class="chart-card"><h5>🌡 Temperature (°C) — Last 20 readings</h5><div class="chart-wrap"><canvas id="chartTemp"></canvas></div></div>
    <div class="chart-card"><h5>💧 Humidity (%) — Last 20 readings</h5><div class="chart-wrap"><canvas id="chartHumid"></canvas></div></div>
    <div class="chart-card"><h5>🌱 Soil Moisture (%) — Last 20 readings</h5><div class="chart-wrap"><canvas id="chartSoil"></canvas></div></div>
  </div>

  <!-- RECENT READINGS TABLE -->
  <div class="table-card">
    <h5>📋 Recent Sensor Readings</h5>
    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead><tr><th>#</th><th>Timestamp</th><th>Temp (°C)</th><th>Humidity (%)</th><th>Soil (%)</th><th>Fan</th></tr></thead>
        <tbody id="readingsBody"><tr><td colspan="6" style="text-align:center;color:#475569;">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>

  <!-- SESSION HISTORY -->
  <div class="history-card">
    <h5>📖 Session History</h5>
    <div style="overflow-x:auto;">
      <table class="tbl">
        <thead><tr><th>#</th><th>Crop</th><th>Target Temp</th><th>Duration</th><th>Start</th><th>End</th><th>Status</th></tr></thead>
        <tbody id="historyBody"><tr><td colspan="7" style="text-align:center;color:#475569;">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
  <!-- ============================================================ -->
  <!-- SYSTEM ARCHITECTURE DIAGRAM (with animated data flow dots)   -->
  <!-- ============================================================ -->
  <div class="section-hdr">🏗️ System Architecture Diagram</div>
  <div class="arch-card">
    <h5>IoT Solar Dryer — End-to-End Architecture with Live Data Flow</h5>
    <svg class="arch-svg" viewBox="0 0 960 340" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <marker id="arr" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto"><path d="M0,0 L0,6 L8,3 z" fill="#60a5fa"/></marker>
        <marker id="arrG" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto"><path d="M0,0 L0,6 L8,3 z" fill="#4ade80"/></marker>
        <!-- Flow paths for animated dots -->
        <path id="pathSensorToDb" d="M155,282 Q240,180 330,75" fill="none"/>
        <path id="pathCmdToEsp"   d="M490,90 Q360,130 225,175" fill="none"/>
        <path id="pathDbToFront"  d="M460,230 Q560,150 670,70" fill="none"/>
        <path id="pathFrontToApi" d="M670,210 L620,210" fill="none"/>
      </defs>
      <!-- Background panels -->
      <rect x="0" y="0" width="310" height="340" rx="14" fill="#0f1e40" stroke="#1e3a5f" stroke-width="1.5"/>
      <rect x="320" y="0" width="320" height="340" rx="14" fill="#0f1e40" stroke="#1e3a5f" stroke-width="1.5"/>
      <rect x="650" y="0" width="310" height="340" rx="14" fill="#0f1e40" stroke="#1e3a5f" stroke-width="1.5"/>
      <!-- Panel labels -->
      <text x="155" y="24" text-anchor="middle" fill="#60a5fa" font-size="11" font-weight="bold">HARDWARE (Wokwi ESP32)</text>
      <text x="480" y="24" text-anchor="middle" fill="#60a5fa" font-size="11" font-weight="bold">BACKEND (PHP + MySQL)</text>
      <text x="805" y="24" text-anchor="middle" fill="#60a5fa" font-size="11" font-weight="bold">FRONTEND (Browser)</text>
      <!-- HARDWARE nodes -->
      <rect x="30" y="50" width="110" height="40" rx="8" fill="#1e3a5f" stroke="#60a5fa" stroke-width="1.5"/>
      <text x="85" y="66" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">DHT22 Sensor</text>
      <text x="85" y="82" text-anchor="middle" fill="#93c5fd" font-size="9">Temp + Humidity</text>
      <rect x="30" y="110" width="110" height="40" rx="8" fill="#1e3a5f" stroke="#60a5fa" stroke-width="1.5"/>
      <text x="85" y="126" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">Soil Sensor</text>
      <text x="85" y="142" text-anchor="middle" fill="#93c5fd" font-size="9">Potentiometer</text>
      <rect x="30" y="175" width="110" height="50" rx="8" fill="#2e1065" stroke="#a78bfa" stroke-width="1.5"/>
      <text x="85" y="195" text-anchor="middle" fill="#fff" font-size="11" font-weight="bold">ESP32</text>
      <text x="85" y="210" text-anchor="middle" fill="#c4b5fd" font-size="9">WiFi + HTTPClient</text>
      <text x="85" y="222" text-anchor="middle" fill="#c4b5fd" font-size="9">ArduinoJson</text>
      <rect x="170" y="175" width="110" height="50" rx="8" fill="#14532d" stroke="#4ade80" stroke-width="1.5"/>
      <text x="225" y="195" text-anchor="middle" fill="#fff" font-size="11" font-weight="bold">Fan Relay</text>
      <text x="225" y="210" text-anchor="middle" fill="#86efac" font-size="9">GPIO 26 (LED)</text>
      <text x="225" y="222" text-anchor="middle" fill="#86efac" font-size="9">ON / OFF</text>
      <rect x="115" y="265" width="80" height="35" rx="8" fill="#1e3a5f" stroke="#60a5fa" stroke-width="1"/>
      <text x="155" y="280" text-anchor="middle" fill="#fff" font-size="10">ngrok</text>
      <text x="155" y="294" text-anchor="middle" fill="#93c5fd" font-size="8">HTTP tunnel</text>
      <!-- BACKEND nodes -->
      <rect x="340" y="50" width="120" height="40" rx="8" fill="#1e3a5f" stroke="#60a5fa" stroke-width="1.5"/>
      <text x="400" y="66" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">insert_sensor.php</text>
      <text x="400" y="82" text-anchor="middle" fill="#93c5fd" font-size="9">POST: save readings</text>
      <rect x="500" y="50" width="120" height="40" rx="8" fill="#14532d" stroke="#4ade80" stroke-width="1.5"/>
      <text x="560" y="66" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">get_commands.php</text>
      <text x="560" y="82" text-anchor="middle" fill="#86efac" font-size="9">GET: control cmds</text>
      <rect x="340" y="120" width="120" height="40" rx="8" fill="#78350f" stroke="#fbbf24" stroke-width="1.5"/>
      <text x="400" y="136" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">MySQL farm_db</text>
      <text x="400" y="152" text-anchor="middle" fill="#fde68a" font-size="9">sensor_logs</text>
      <rect x="500" y="120" width="120" height="40" rx="8" fill="#78350f" stroke="#fbbf24" stroke-width="1.5"/>
      <text x="560" y="136" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">MySQL farm_db</text>
      <text x="560" y="152" text-anchor="middle" fill="#fde68a" font-size="9">control_commands</text>
      <rect x="340" y="195" width="120" height="35" rx="8" fill="#1e3a5f" stroke="#60a5fa" stroke-width="1.5"/>
      <text x="400" y="211" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">sensor_stream.php</text>
      <text x="400" y="224" text-anchor="middle" fill="#93c5fd" font-size="9">SSE push events</text>
      <rect x="500" y="195" width="120" height="35" rx="8" fill="#1e3a5f" stroke="#60a5fa" stroke-width="1.5"/>
      <text x="560" y="211" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">start/stop_session</text>
      <text x="560" y="224" text-anchor="middle" fill="#93c5fd" font-size="9">session management</text>
      <rect x="340" y="265" width="280" height="35" rx="8" fill="#14532d" stroke="#4ade80" stroke-width="1"/>
      <text x="480" y="280" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">crop_profiles · drying_sessions · alerts_log</text>
      <text x="480" y="294" text-anchor="middle" fill="#86efac" font-size="8">MySQL tables</text>
      <!-- FRONTEND nodes -->
      <rect x="670" y="50" width="120" height="40" rx="8" fill="#2e1065" stroke="#a78bfa" stroke-width="1.5"/>
      <text x="730" y="66" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">solarDrying.php</text>
      <text x="730" y="82" text-anchor="middle" fill="#c4b5fd" font-size="9">Real-time dashboard</text>
      <rect x="670" y="120" width="120" height="40" rx="8" fill="#2e1065" stroke="#a78bfa" stroke-width="1.5"/>
      <text x="730" y="136" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">Chart.js Charts</text>
      <text x="730" y="152" text-anchor="middle" fill="#c4b5fd" font-size="9">Temp/Humidity/Soil</text>
      <rect x="670" y="195" width="120" height="35" rx="8" fill="#14532d" stroke="#4ade80" stroke-width="1.5"/>
      <text x="730" y="211" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">Control Panel</text>
      <text x="730" y="224" text-anchor="middle" fill="#86efac" font-size="9">Crop select + start/stop</text>
      <rect x="670" y="265" width="120" height="35" rx="8" fill="#4b1113" stroke="#f87171" stroke-width="1.5"/>
      <text x="730" y="281" text-anchor="middle" fill="#fff" font-size="10" font-weight="bold">Alert Banners</text>
      <text x="730" y="294" text-anchor="middle" fill="#fca5a5" font-size="9">Danger / Overheat</text>
      <!-- Static arrows -->
      <line x1="155" y1="282" x2="320" y2="75" stroke="#60a5fa" stroke-width="1.5" marker-end="url(#arr)" stroke-dasharray="4,3"/>
      <text x="220" y="160" fill="#60a5fa" font-size="9" transform="rotate(-30 220 160)">POST data</text>
      <line x1="225" y1="175" x2="490" y2="90" stroke="#4ade80" stroke-width="1.5" marker-end="url(#arrG)" stroke-dasharray="4,3"/>
      <text x="310" y="100" fill="#4ade80" font-size="9" transform="rotate(-15 310 100)">GET commands</text>
      <line x1="460" y1="140" x2="460" y2="195" stroke="#60a5fa" stroke-width="1.5" marker-end="url(#arr)"/>
      <line x1="460" y1="230" x2="670" y2="70" stroke="#60a5fa" stroke-width="1.5" marker-end="url(#arr)" stroke-dasharray="4,3"/>
      <text x="545" y="155" fill="#60a5fa" font-size="9">SSE push</text>
      <line x1="560" y1="160" x2="560" y2="195" stroke="#4ade80" stroke-width="1.2" marker-end="url(#arrG)"/>
      <line x1="670" y1="210" x2="620" y2="210" stroke="#a78bfa" stroke-width="1.5" marker-end="url(#arr)"/>
      <line x1="140" y1="200" x2="170" y2="200" stroke="#4ade80" stroke-width="2" marker-end="url(#arrG)"/>
      <!-- ANIMATED FLOW DOTS -->
      <circle r="4" class="flow-dot"><animateMotion dur="2s" repeatCount="indefinite"><mpath href="#pathSensorToDb"/></animateMotion></circle>
      <circle r="4" class="flow-dot"><animateMotion dur="2s" repeatCount="indefinite" begin="0.7s"><mpath href="#pathSensorToDb"/></animateMotion></circle>
      <circle r="3.5" class="flow-dot-green"><animateMotion dur="2.5s" repeatCount="indefinite"><mpath href="#pathCmdToEsp"/></animateMotion></circle>
      <circle r="4" class="flow-dot"><animateMotion dur="2s" repeatCount="indefinite"><mpath href="#pathDbToFront"/></animateMotion></circle>
      <circle r="4" class="flow-dot"><animateMotion dur="2s" repeatCount="indefinite" begin="1s"><mpath href="#pathDbToFront"/></animateMotion></circle>
      <circle r="3" style="fill:#a78bfa"><animateMotion dur="1.5s" repeatCount="indefinite"><mpath href="#pathFrontToApi"/></animateMotion></circle>
    </svg>
  </div>

  <!-- ============================================================ -->
  <!-- 6. VIVA DEMO GUIDE                                           -->
  <!-- ============================================================ -->
  <!-- <div class="section-hdr">🎤 Viva Presentation Guide — How to Demo</div>
  <div class="demo-card">
    <h5 onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='none'?'block':'block'">
      📋 Step-by-Step Demo Instructions for Your Lecturer
    </h5>
    <div class="demo-steps">
      <div class="demo-step">
        <b>Open Wokwi Simulator:</b> Click the purple <b>"▶ Simulate on Wokwi"</b> button at the top of this page. This opens the virtual ESP32 circuit with DHT22 sensor, potentiometer (soil moisture), and fan LED.
      </div>
      <div class="demo-step">
        <b>Start ngrok tunnel:</b> In your terminal run <code>ngrok http --scheme=http 80</code>. Copy the ngrok URL and paste it into <code>sketch.ino</code> line 26 (<code>BASE_URL</code>). This creates a public tunnel so the Wokwi ESP32 can reach your localhost PHP server.
      </div>
      <div class="demo-step">
        <b>Run the Wokwi simulation:</b> Press the green play button in Wokwi. Watch the Serial Monitor — you'll see <b>"[WiFi] Connected!"</b> then <b>"[POST] Sensor data sent OK"</b> every 5 seconds. The ESP32 is now reading the DHT22 and potentiometer and sending data to your PHP backend.
      </div>
      <div class="demo-step">
        <b>Watch live data appear:</b> Come back to this dashboard. You'll see the <b>green "🟢 Live (SSE)"</b> indicator, and all sensor cards, charts, and table updating in real-time every 5 seconds. Explain: <b>"The data is flowing from the virtual sensor → ESP32 → WiFi → ngrok → PHP → MySQL → SSE → this browser."</b>
      </div>
      <div class="demo-step">
        <b>Select a crop &amp; start drying:</b> Choose <b>"Paddy (50°C)"</b> from the crop dropdown. The target temperature and duration auto-fill. Click <b>"▶ START DRYING"</b>. Show the progress bar appears with a countdown timer. In Wokwi Serial Monitor you'll see <b>"[CMD] Session STARTED"</b> and <b>"[FAN] Turned ON"</b>.
      </div>
      <div class="demo-step">
        <b>Show the fan animation:</b> Point to the fan card — the blades are now <b>spinning</b>. The status badge shows <b>"Fan: ON 🌀"</b>. In Wokwi, the green LED on GPIO 26 is lit. Explain: <b>"The web dashboard sent a START command → PHP queued it in MySQL → ESP32 polled and received it → turned on the fan relay."</b>
      </div>
      <div class="demo-step">
        <b>Trigger a warning:</b> In Wokwi, drag the DHT22 temperature slider to <b>65°C or higher</b>. Wait 5 seconds. Watch this dashboard — a <b>red DANGER alert banner</b> appears and the full-screen <b>"🚨 SYSTEM HALTED"</b> overlay activates. The fan animation <b>slows down and stops</b>. In Wokwi, the LED turns off. Explain: <b>"The sensor detected dangerous temperature → Arduino auto-stopped the fan → sent alert to the server → dashboard shows the warning."</b>
      </div>
      <div class="demo-step">
        <b>Dismiss &amp; recover:</b> Click "Acknowledge & Dismiss" on the overlay. Lower the Wokwi temperature back to ~45°C. Click <b>"▶ START DRYING"</b> again. The system resumes normally.
      </div>
      <div class="demo-step">
        <b>Show architecture &amp; explain:</b> Scroll down to the <b>"System Architecture Diagram"</b> — show the animated dots flowing between components. Point to each box and explain the role: sensors → ESP32 → ngrok → PHP APIs → MySQL → SSE → Browser → Control commands loop back.
      </div>
      <div class="demo-step">
        <b>Emergency stop demo:</b> Click <b>"⚠ EMERGENCY STOP"</b> to show that the system can be halted immediately from the web interface. All operations stop, fan turns off, and session ends.
      </div>
    </div>
  </div> -->

</div><!-- /page-wrap -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  function showCompletedMessage(data) {
  if (!data.just_completed) return;

  const crop = data.completed_crop || 'selected crop';
  const msg  = data.completed_message || ('Drying completed successfully for ' + crop);

  $('#alertsZone').prepend(`
    <div style="
      background:#14532d;
      border-left:4px solid #22c55e;
      color:#bbf7d0;
      padding:12px 18px;
      border-radius:10px;
      margin-bottom:8px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      font-size:.88rem;
      font-weight:600;
    ">
      <div>
        ✅ <strong>DRYING COMPLETED</strong>
        <span style="margin-left:10px;">${msg}</span>
      </div>
      <button onclick="$(this).parent().remove()" style="
        background:none;
        border:none;
        color:#bbf7d0;
        cursor:pointer;
        font-size:1.1rem;
      ">✕</button>
    </div>
  `);

  alert(msg);
}

/* ====================== STATE ====================== */
let cropProfiles = [], sessionActive = false, remainingSec = 0, countdownTimer = null;
let sseSource = null, pollTimer = null, lastId = 0, chartsLoaded = false;
const API = 'api/';
let dangerOverlay, tempChart, humChart, soilChart;

/* ====================== CHARTS ====================== */
const mkChart = (id, label, color, max) => new Chart(document.getElementById(id), {
  type:'line', data:{labels:[], datasets:[{label, data:[], borderColor:color, backgroundColor:color+'33',
  fill:true, tension:.3, pointRadius:2}]}, options:{responsive:true, maintainAspectRatio:false,
  animation:{duration:400}, scales:{y:{min:0, max, ticks:{color:'#94a3b8'}}, x:{display:false}},
  plugins:{legend:{labels:{color:'#e2e8f0'}}}}
});

function pushChart(chart, label, val) {
  chart.data.labels.push(label);
  chart.data.datasets[0].data.push(val);
  if (chart.data.labels.length > 20) { chart.data.labels.shift(); chart.data.datasets[0].data.shift(); }
  chart.update('none');
}

/* ====================== UPDATE CARDS ====================== */
function fanNorm(v) { return (v==='ON'||v==='1'||v===1||v===true) ? 'ON' : 'OFF'; }
function updateCards(r) {
  const t = parseFloat(r.temperature), h = parseFloat(r.humidity), s = parseFloat(r.soil_moisture);
  // Trust fan_status from server — APIs now always reflect true session state
  const fan = fanNorm(r.fan_status);
  $('#valTemp').text(t.toFixed(1) + '°C');
  $('#valHumid').text(h.toFixed(1) + '%');
  $('#valSoil').text(s.toFixed(1) + '%');
  $('#valFan').text(fan === 'ON' ? 'ON 🌀' : 'OFF');
  // Color classes
  const tCard = document.getElementById('cardTemp');
  if (tCard) tCard.classList.toggle('alert-glow', t >= 55);
  // Fan animation
  const blades = document.querySelector('.fan-blades');
  if (blades) {
    blades.classList.remove('spinning','slowing','stopped');
    if (fan === 'ON') blades.classList.add('spinning');
    else blades.classList.add('stopped');
  }
  // Danger overlay
  if (t >= 60) {
    dangerOverlay.classList.add('visible');
    $('#dngTemp').text(t.toFixed(1) + '°C');
  }
  // Update sensor viz
  updateSensorViz(r);
}

/* ====================== DANGER OVERLAY ====================== */
function closeDanger() {
  dangerOverlay.classList.remove('visible');
}

/* ====================== UPDATE TABLE ====================== */
let _rowNum = 0;
function updateTable(rows) {
  const $tb = $('#readingsBody');
  $tb.empty();
  _rowNum = 0;
  rows.forEach(r => {
    _rowNum++;
    const fan = fanNorm(r.fan_status);
    const fanBadge = fan === 'ON'
      ? '<span style="color:#4ade80;font-weight:bold">🌀 ON</span>'
      : '<span style="color:#94a3b8">OFF</span>';
    $tb.append(`<tr>
      <td>${_rowNum}</td>
      <td>${r.recorded_at || r.timestamp || '-'}</td>
      <td>${parseFloat(r.temperature).toFixed(1)}</td>
      <td>${parseFloat(r.humidity).toFixed(1)}</td>
      <td>${parseFloat(r.soil_moisture).toFixed(1)}</td>
      <td>${fanBadge}</td>
    </tr>`);
  });
}

/* ====================== ALERTS ====================== */
function showAlerts(alerts) {
  const $z = $('#alertsZone');
  $z.empty();
  if (!alerts || alerts.length === 0) return;
  alerts.forEach(a => {
    const lvl = a.alert_type || 'WARNING';
    const cls = lvl === 'DANGER' ? 'background:#7f1d1d;border-left:4px solid #ef4444;' : 'background:#78350f;border-left:4px solid #f59e0b;';
    $z.append(`<div style="${cls} padding:10px 16px; border-radius:8px; margin-bottom:6px; display:flex; justify-content:space-between; align-items:center;">
      <div><strong style="color:#fca5a5">${lvl === 'DANGER' ? '🚨' : '⚠️'} ${lvl}</strong>
      <span style="color:#fde68a; margin-left:10px">${a.message}</span>
      <small style="color:#94a3b8; margin-left:10px">${a.triggered_at || ''}</small></div>
      <button onclick="resolveAlert(${a.id})" style="background:#16a34a;color:#fff;border:none;padding:4px 10px;border-radius:6px;cursor:pointer;font-size:12px">Resolve</button>
    </div>`);
  });
}
function resolveAlert(id) {
  $.get(API + 'resolve_alert.php?id=' + id, () => fetchSession());
}
/* ====================== HOW IT WORKS ANIMATION ====================== */
const hiwNarr = [
  "DHT22 sensor detects temperature and humidity in the drying chamber using a capacitive element.",
  "The sensor converts analog readings into digital binary data using its built-in ADC.",
  "ESP32 microcontroller reads the digital signal via GPIO pin 4 using the DHT library.",
  "ESP32 connects to WiFi and sends an HTTP POST request containing temperature, humidity, soil moisture, and fan status.",
  "The ngrok tunnel forwards the HTTP request from the internet to your localhost XAMPP server.",
  "PHP script (insert_sensor.php) receives the data and saves it into MySQL sensor_logs table.",
  "Server-Sent Events (sensor_stream.php) detects the new row and pushes it to the browser instantly.",
  "The dashboard updates cards, charts, and tables in real-time without page refresh.",
  "When the farmer clicks START/STOP, the command is saved to control_commands table.",
  "Arduino polls get_commands.php every 5 seconds, receives the command, and turns the fan ON/OFF."
];
let hiwStep = 0, hiwTimer = null;
function animateHIW() {
  const steps = document.querySelectorAll('.hiw-step');
  const arrows = document.querySelectorAll('.hiw-arrow');
  const narr = document.getElementById('hiwNarrative');
  if (!steps.length) return;
  steps.forEach((s, i) => { s.classList.toggle('active', i === hiwStep); });
  arrows.forEach((a, i) => { a.classList.toggle('flow', i === hiwStep); });
  if (narr) narr.textContent = hiwNarr[hiwStep] || '';
  hiwStep = (hiwStep + 1) % steps.length;
}
hiwTimer = setInterval(animateHIW, 3000);
animateHIW();

/* ====================== SENSOR VISUALIZATION ====================== */
function updateSensorViz(r) {
  const vizT = document.getElementById('vizTemp');
  const vizH = document.getElementById('vizHumid');
  const vizS = document.getElementById('vizSoil');
  const vizF = document.getElementById('vizFanStatus');
  if (vizT) vizT.textContent = parseFloat(r.temperature).toFixed(1) + '°C';
  if (vizH) vizH.textContent = parseFloat(r.humidity).toFixed(1) + '%';
  if (vizS) vizS.textContent = parseFloat(r.soil_moisture).toFixed(1) + '%';
  if (vizF) {
    
    const on = fanNorm(r.fan_status) === 'ON';
    vizF.textContent = on ? 'ON' : 'OFF';
    vizF.style.color = on ? '#4ade80' : '#f87171';
  }
  // Pulse class on data update
  document.querySelectorAll('.data-pulse').forEach(el => {
    el.classList.remove('data-pulse');
    void el.offsetWidth;
    el.classList.add('data-pulse');
  });
}

/* ====================== CROP GUIDE BUILDER ====================== */
function buildCropGuide(profiles) {
  const $c = $('#cropGuide');
  $c.empty();
  if (!profiles || profiles.length === 0) { $c.html('<p style="color:#94a3b8">No crop profiles loaded.</p>'); return; }
  const icons = { Paddy:'🌾', Corn:'🌽', Chili:'🌶️', Cocoa:'🍫', Cassava:'🥔' };
  const tips = {
    Paddy:'Maintain constant airflow. Paddy needs slow even drying to prevent cracking.',
    Corn:'Higher temperatures are acceptable for corn. Watch for kernel discoloration.',
    Chili:'Chili benefits from moderate heat. Over-drying makes them brittle.',
    Cocoa:'Cocoa beans need careful temperature control for proper fermentation flavor.',
    Cassava:'Slice cassava thinly for uniform drying. Monitor moisture closely.'
  };
  profiles.forEach(p => {
    const icon = icons[p.crop_name] || '🌱';
    const tip = tips[p.crop_name] || 'Follow recommended temperature and moisture settings.';
    const tPct = Math.min(100, (p.target_temp / 80) * 100);
    const mPct = p.target_humidity;
    $c.append(`
      <div class="crop-card">
        <div class="crop-card-hdr">${icon} ${p.crop_name}</div>
        <div class="crop-card-body">
          <div style="margin-bottom:8px">
            <span style="color:#94a3b8;font-size:12px">Target Temperature</span>
            <div style="display:flex;align-items:center;gap:8px">
              <div style="flex:1;height:8px;background:#1e293b;border-radius:4px">
                <div style="width:${tPct}%;height:100%;background:linear-gradient(90deg,#f97316,#ef4444);border-radius:4px"></div>
              </div>
              <strong style="color:#f97316">${p.target_temp}°C</strong>
            </div>
          </div>
          <div style="margin-bottom:8px">
            <span style="color:#94a3b8;font-size:12px">Target Moisture</span>
            <div style="display:flex;align-items:center;gap:8px">
              <div style="flex:1;height:8px;background:#1e293b;border-radius:4px">
                <div style="width:${mPct}%;height:100%;background:linear-gradient(90deg,#3b82f6,#06b6d4);border-radius:4px"></div>
              </div>
              <strong style="color:#3b82f6">${p.target_humidity}%</strong>
            </div>
          </div>
          <div style="margin-bottom:8px">
            <span style="color:#94a3b8;font-size:12px">Duration</span>
            <strong style="color:#e2e8f0;margin-left:6px">${p.duration_hours} hours</strong>
          </div>
          <div style="margin-bottom:8px">
            <span style="color:#94a3b8;font-size:12px">Danger Temp</span>
            <strong style="color:#ef4444;margin-left:6px">${p.danger_temp}°C ⚠️</strong>
          </div>
          <div style="background:#1e293b;padding:8px 10px;border-radius:6px;margin-top:6px">
            <span style="color:#fbbf24;font-size:12px">💡 Tip:</span>
            <span style="color:#cbd5e1;font-size:12px"> ${tip}</span>
          </div>
        </div>
      </div>
    `);
  });
}

/* ====================== SESSION UI ====================== */
function updateSessionUI(data) {
  const s = data.session;
  const $bar = $('#progFill');
  const $cropLbl = $('#progCropLabel');
  const $timeLbl = $('#progTimeLabel');
  const $btnStart = $('.btn-start');
  const $btnStop = $('.btn-stop');
  const $btnEmg = $('.btn-emergency');

  if (s && s.status === 'running') {
    sessionActive = true;

    // show fan ON
    $('#valFan').text('ON 🌀');
    const blades = document.querySelector('.fan-blades');
    if (blades) {
      blades.classList.remove('spinning', 'slowing', 'stopped');
      blades.classList.add('spinning');
    }

    const start = new Date(s.start_time).getTime();
    const durMs = parseInt(s.duration_minutes) * 60000;
    const end = start + durMs;
    const now = Date.now();

    // ONLY set remainingSec when session first loads
    if (!countdownTimer) {
      remainingSec = data.remaining_sec != null
        ? parseInt(data.remaining_sec)
        : Math.max(0, Math.floor((end - now) / 1000));
    }

    const pct = durMs > 0 ? Math.min(100, ((durMs - (remainingSec * 1000)) / durMs) * 100) : 100;
    $bar.css('width', pct.toFixed(1) + '%');

    const hh = String(Math.floor(remainingSec / 3600)).padStart(2, '0');
    const mm = String(Math.floor((remainingSec % 3600) / 60)).padStart(2, '0');
    const ss = String(remainingSec % 60).padStart(2, '0');

    $cropLbl.html(`Drying: <b>${s.crop_name}</b> @ ${s.target_temp}°C`);
    $timeLbl.html(`<span style="color:#4ade80">● ACTIVE</span> — Remaining: ${hh}:${mm}:${ss}`);

    $btnStart.prop('disabled', true).css('opacity', .5);
    $btnStop.prop('disabled', false).css('opacity', 1);
    $btnEmg.prop('disabled', false).css('opacity', 1);

    if (!countdownTimer) {
      countdownTimer = setInterval(() => {
        remainingSec--;

        if (remainingSec <= 0) {
          clearInterval(countdownTimer);
          countdownTimer = null;

          // ask backend to auto-complete expired session
          fetchSession();
          return;
        }

        // update display locally without resetting remainingSec
        const pctNow = durMs > 0 ? Math.min(100, ((durMs - (remainingSec * 1000)) / durMs) * 100) : 100;
        $bar.css('width', pctNow.toFixed(1) + '%');

        const hhNow = String(Math.floor(remainingSec / 3600)).padStart(2, '0');
        const mmNow = String(Math.floor((remainingSec % 3600) / 60)).padStart(2, '0');
        const ssNow = String(remainingSec % 60).padStart(2, '0');

        $timeLbl.html(`<span style="color:#4ade80">● ACTIVE</span> — Remaining: ${hhNow}:${mmNow}:${ssNow}`);
      }, 1000);
    }

  } else {
    sessionActive = false;

    $('#valFan').text('OFF');
    const blades = document.querySelector('.fan-blades');
    if (blades) {
      blades.classList.remove('spinning', 'slowing');
      blades.classList.add('stopped');
    }

    if (countdownTimer) {
      clearInterval(countdownTimer);
      countdownTimer = null;
    }

    remainingSec = 0;
    $bar.css('width', '0%');
    $cropLbl.text('Drying: —');
    $timeLbl.html('<span style="color:#94a3b8">No active session</span>');

    $btnStart.prop('disabled', false).css('opacity', 1);
    $btnStop.prop('disabled', true).css('opacity', .5);
    $btnEmg.prop('disabled', true).css('opacity', .5);
  }
}

/* ====================== CROP PROFILES DROPDOWN ====================== */
function loadCropProfiles(profiles) {
  cropProfiles = profiles || [];
  const $sel = $('#cropSelect');
  $sel.empty().append('<option value="">— Select Crop —</option>');
  cropProfiles.forEach(p => {
    $sel.append(`<option value="${p.id}">${p.crop_name} (${p.target_temp}°C)</option>`);
  });
  buildCropGuide(cropProfiles);
}
function loadCropProfile() {
  const id = parseInt($('#cropSelect').val());
  const p = cropProfiles.find(c => c.id === id);
  if (p) {
    $('#targetTemp').val(p.target_temp);
    $('#durationHrs').val(p.duration_hours);
  }
}

/* ====================== SESSION HISTORY ====================== */
function updateHistory(history) {
  const $tb = $('#historyBody');
  $tb.empty();

  if (!history || history.length === 0) {
    $tb.html('<tr><td colspan="7" style="text-align:center;color:#475569;">No history found</td></tr>');
    return;
  }

  history.forEach((h, index) => {
    const status = h.status || '-';
    const statusClr =
      status === 'completed' ? '#4ade80' :
      status === 'running'   ? '#fbbf24' :
      '#f87171';

    const durLabel = h.duration_minutes
      ? (h.duration_minutes < 60
          ? h.duration_minutes + ' min'
          : (Math.round((h.duration_minutes / 60) * 10) / 10) + ' h')
      : '-';

    $tb.append(`
      <tr>
        <td>${index + 1}</td>
        <td>${h.crop_name || '-'}</td>
        <td>${h.target_temp || '-'}°C</td>
        <td>${durLabel}</td>
        <td>${h.start_time || '-'}</td>
        <td>${h.end_time || '-'}</td>
        <td><span style="color:${statusClr};font-weight:bold">${status}</span></td>
      </tr>
    `);
  });
}
/* ====================== SESSION CONTROLS ====================== */
function startSession() {
  const cropId = $('#cropSelect').val();
  const temp = $('#targetTemp').val();
  const dur = $('#durationHrs').val();

  console.log("Selected crop:", cropId);
  console.log("Target temp:", temp);
  console.log("Duration hours:", dur);
  console.log("Duration minutes sent to API:", dur * 60);

  if (!cropId) {
    alert('Please select a crop first.');
    return;
  }

  $.post(API + 'start_session.php', {
    crop_id: cropId,
    target_temp: temp,
    duration: dur * 60
  }, function(res) {
    console.log("start_session.php response:", res);

    if (res.status === 'started') {
      fetchSession();
    } else {
      alert('Error: ' + (res.error || 'Unknown'));
    }
  }, 'json').fail(function(xhr) {
    console.log("start_session.php failed:", xhr.responseText);
    alert('Failed to start session. Check API.');
  });
}
function stopSession() {
  $.post(API + 'stop_session.php', { action: 'stop' }, function(res) {
    fetchSession();
  }, 'json').fail(function() { alert('Failed to stop session.'); });
}
function emergencyStop() {
  if (!confirm('⚠️ EMERGENCY STOP — Are you sure? This will immediately halt the fan and end the session.')) return;
  $.post(API + 'stop_session.php', { action: 'emergency' }, function(res) {
    fetchSession();
  }, 'json').fail(function() { alert('Emergency stop failed.'); });
}

/* ====================== FETCH SESSION ====================== */
function fetchSession() {
  $.getJSON(API + 'get_session.php', function(data) {

    if (data.profiles) loadCropProfiles(data.profiles);
    if (data.alerts) showAlerts(data.alerts);
    if (data.session_history) updateHistory(data.session_history);
    if (data.latest_reading) handleReading(data.latest_reading);

    // NEW: show completed message
    if (data.just_completed) {
      showCompletedMessage(data);
    }

    updateSessionUI(data);
  }).fail(function(xhr) {
    console.log('get_session.php failed', xhr.responseText);
  });
}

/* ====================== SSE STREAM ====================== */
function startSSE() {
  if (sseSource) sseSource.close();
  sseSource = new EventSource('api/sensor_stream.php?init=1');
  sseSource.addEventListener('reading', function(e) {
    try {
      const d = JSON.parse(e.data);
      const r = d.latest || d;
      handleReading(r);
      if (d.table) updateTable(d.table);
      setOnline('SSE');
    } catch(ex) { console.error('SSE parse error', ex); }
  });
  sseSource.addEventListener('init', function(e) {
    try {
      const d = JSON.parse(e.data);
      if (d.latest) handleReading(d.latest);
      if (d.chart_temp) setChartHistory(d.chart_temp, d.chart_humid, d.chart_soil);
      if (d.table) updateTable(d.table);
      setOnline('SSE');
    } catch(ex) { console.error('SSE init error', ex); }
  });
  sseSource.onerror = function() {
    setOffline();
    sseSource.close();
    setTimeout(startSSE, 10000);
  };
}
function handleReading(r) {
  updateCards(r);
  const _ra = r.recorded_at || r.timestamp || '';
  const ts = _ra.length >= 19 ? _ra.substr(11, 8) : (_ra || new Date().toLocaleTimeString());
  pushChart(tempChart, ts, parseFloat(r.temperature));
  pushChart(humChart, ts, parseFloat(r.humidity));
  pushChart(soilChart, ts, parseFloat(r.soil_moisture));
  // Add to table (prepend)
  const fan = fanNorm(r.fan_status);
  const fanBadge = fan === 'ON'
    ? '<span style="color:#4ade80;font-weight:bold">🌀 ON</span>'
    : '<span style="color:#94a3b8">OFF</span>';
  const $row = $(`<tr>
    <td>${$('#readingsBody tr').length + 1}</td>
    <td>${ts}</td>
    <td>${parseFloat(r.temperature).toFixed(1)}</td>
    <td>${parseFloat(r.humidity).toFixed(1)}</td>
    <td>${parseFloat(r.soil_moisture).toFixed(1)}</td>
    <td>${fanBadge}</td>
  </tr>`);
  $('#readingsBody').prepend($row);
  if ($('#readingsBody tr').length > 20) $('#readingsBody tr:last').remove();
}
/* Replace chart data wholesale — guards against duplicate seeding */
function setChartHistory(tArr, hArr, sArr) {
  // Format: [{value, recorded_at}] — from sensor_stream.php init event
  if (chartsLoaded) return;
  chartsLoaded = true;
  const lbs = (tArr||[]).map(r => r.recorded_at ? (r.recorded_at.length >= 19 ? r.recorded_at.substr(11,8) : r.recorded_at.substr(11,5)) : '');
  tempChart.data.labels = lbs.slice();
  tempChart.data.datasets[0].data = (tArr||[]).map(r => parseFloat(r.value));
  tempChart.update('none');
  humChart.data.labels = lbs.slice();
  humChart.data.datasets[0].data = (hArr||[]).map(r => parseFloat(r.value));
  humChart.update('none');
  soilChart.data.labels = lbs.slice();
  soilChart.data.datasets[0].data = (sArr||[]).map(r => parseFloat(r.value));
  soilChart.update('none');
}
function setChartHistoryFlat(chartObj) {
  // Format: {labels[], temperature[], humidity[], soil_moisture[]} — from get_solar_data.php
  if (chartsLoaded) return;
  chartsLoaded = true;
  // labels from PHP are already "H:i:s" (HH:MM:SS) — use as-is for unique X positions
  const lbs = (chartObj.labels||[]).map(l => String(l));
  tempChart.data.labels = lbs.slice();
  tempChart.data.datasets[0].data = (chartObj.temperature||[]).slice();
  tempChart.update('none');
  humChart.data.labels = lbs.slice();
  humChart.data.datasets[0].data = (chartObj.humidity||[]).slice();
  humChart.update('none');
  soilChart.data.labels = lbs.slice();
  soilChart.data.datasets[0].data = (chartObj.soil_moisture||[]).slice();
  soilChart.update('none');
}

/* ====================== POLLING FALLBACK ====================== */
function startPolling() {
  function doPoll() {
    $.getJSON(API + 'get_solar_data.php', function(data) {
      if (data.latest) {
        handleReading(data.latest);
        lastId = Math.max(lastId, parseInt(data.latest.id) || 0);
        setOnline('Poll');
      }
      if (data.chart)      setChartHistoryFlat(data.chart);   // seed charts on first load
      if (data.table_rows) updateTable(data.table_rows);
    });
  }
  doPoll();                               // immediate – populate cards+charts on page load
  pollTimer = setInterval(doPoll, 6000);  // then refresh every 6 s
}

/* ====================== ONLINE / OFFLINE ====================== */
function setOnline(via) {
  $('#connLabel').html('🟢 Live (' + via + ')').css('color','#4ade80');
  $('#lastUpdate').text('Updated: ' + new Date().toLocaleTimeString());
}
function setOffline() {
  $('#connLabel').html('🔴 Disconnected').css('color','#f87171');
}

/* ====================== INIT ====================== */
document.addEventListener('DOMContentLoaded', function() {
  dangerOverlay = document.getElementById('dangerOverlay');
  tempChart = mkChart('chartTemp', 'Temperature °C', '#f97316', 80);
  humChart  = mkChart('chartHumid', 'Humidity %', '#3b82f6', 100);
  soilChart = mkChart('chartSoil', 'Soil Moisture %', '#22c55e', 100);
  startSSE();
  startPolling();
  fetchSession();
  setInterval(fetchSession, 15000);
});
</script>
</body>
</html>