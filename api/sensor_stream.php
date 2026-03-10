<?php
// ============================================================
//  GET /api/sensor_stream.php
//  Server-Sent Events endpoint — pushes new sensor rows to the
//  browser the moment they are written by insert_sensor.php.
//
//  EventSource auto-reconnects if the connection drops.
//  Each connection lives for up to 50 s then sends a
//  "reconnect" event so the client re-opens cleanly.
// ============================================================
set_time_limit(0);
ignore_user_abort(false);

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no');        // Nginx: disable proxy buffering
header('Access-Control-Allow-Origin: *');

// Kill any existing output buffer so flush() actually works
while (ob_get_level()) ob_end_clean();

// ── DB connection ─────────────────────────────────────────────
$conn = new mysqli("localhost", "root", "", "");
if ($conn->connect_error) {
    echo "event: error\ndata: " . json_encode(["msg" => "DB connect failed"]) . "\n\n";
    flush();
    exit;
}
$conn->query("CREATE DATABASE IF NOT EXISTS farm_db");
$conn->select_db("farm_db");

// ── Bootstrap table (safety) ──────────────────────────────────
$conn->query("
    CREATE TABLE IF NOT EXISTS sensor_logs (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        farm_id       INT            NOT NULL DEFAULT 1,
        temperature   DECIMAL(5,2)   NOT NULL,
        humidity      DECIMAL(5,2)   NOT NULL,
        soil_moisture TINYINT        NOT NULL,
        fan_status    TINYINT        NOT NULL DEFAULT 0,
        recorded_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_recorded_at (recorded_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
$conn->query("ALTER TABLE sensor_logs ADD COLUMN IF NOT EXISTS fan_status TINYINT NOT NULL DEFAULT 0");

// ── Start from the highest known id sent by the client ────────
// Client passes ?last_id=NNN in the EventSource URL
$lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

// If client passes 0 (fresh start), seed with latest row id so we
// don't flood with old history — only push NEW rows from now on.
if ($lastId === 0) {
    $seed = $conn->query("SELECT COALESCE(MAX(id),0) AS mid FROM sensor_logs");
    if ($seed) $lastId = (int)$seed->fetch_assoc()['mid'];
}

// ── Also send the last 20 rows as initial state on first connect ──
$initData = [];
if (isset($_GET['init']) && $_GET['init'] === '1') {
    $initRes = $conn->query("
        SELECT id, temperature, humidity, soil_moisture, fan_status, recorded_at
        FROM sensor_logs
        ORDER BY recorded_at DESC
        LIMIT 20
    ");
    while ($row = $initRes->fetch_assoc()) $initData[] = $row;
    $initData = array_reverse($initData);   // oldest first

    $cntRes = $conn->query("SELECT COUNT(*) AS total FROM sensor_logs");
    $total  = (int)$cntRes->fetch_assoc()['total'];

    // Build chart arrays
    $chartTemp = []; $chartHumid = []; $chartSoil = [];
    foreach ($initData as $r) {
        $lbl = substr($r['recorded_at'], 11, 5);
        $chartTemp[]  = ['value' => (float)$r['temperature'],  'recorded_at' => $r['recorded_at']];
        $chartHumid[] = ['value' => (float)$r['humidity'],     'recorded_at' => $r['recorded_at']];
        $chartSoil[]  = ['value' => (int)$r['soil_moisture'],  'recorded_at' => $r['recorded_at']];
    }

    // Check session state for fan override
    $initSessionOn = false;
    $isr = $conn->query("SELECT id FROM drying_sessions WHERE status='running' LIMIT 1");
    if ($isr && $isr->num_rows > 0) $initSessionOn = true;
    $fanVal = $initSessionOn ? 1 : 0;

    // Latest row for cards
    $latest = !empty($initData) ? $initData[count($initData)-1] : null;
    if ($latest) $latest['fan_status'] = $fanVal;

    // Override fan_status on all table rows
    $tableInit = array_reverse($initData);
    foreach ($tableInit as &$trow) $trow['fan_status'] = $fanVal;
    unset($trow);

    echo "event: init\n";
    echo "data: " . json_encode([
        "latest"      => $latest,
        "table"       => $tableInit,
        "chart_temp"  => $chartTemp,
        "chart_humid" => $chartHumid,
        "chart_soil"  => $chartSoil,
        "total"       => $total
    ]) . "\n\n";
    flush();
}

// ── Send a comment heartbeat every ~15 s to keep proxies alive ──
$startTime  = time();
$maxRuntime = 50;        // seconds before we tell the client to reconnect
$pollMs     = 2;         // seconds between DB polls
$heartbeat  = 15;        // seconds between no-op heartbeats
$lastHB     = time();

while (!connection_aborted() && (time() - $startTime) < $maxRuntime) {

    // Re-use connection (reconnect if dropped by MySQL)
    if (!$conn->ping()) {
        $conn = new mysqli("localhost", "root", "", "farm_db");
        if ($conn->connect_error) { sleep(2); continue; }
    }

    // ── Look for rows newer than lastId ───────────────────────
    $stmt = $conn->prepare("
        SELECT id, temperature, humidity, soil_moisture, fan_status, recorded_at
        FROM sensor_logs
        WHERE id > ?
        ORDER BY id ASC
        LIMIT 10
    ");
    $stmt->bind_param("i", $lastId);
    $stmt->execute();
    $res = $stmt->get_result();

    $count = $conn->query("SELECT COUNT(*) AS total FROM sensor_logs")->fetch_assoc()['total'];

    // Check once per poll cycle whether a session is running
    $sessionOn = false;
    $sr3 = $conn->query("SELECT id FROM drying_sessions WHERE status='running' LIMIT 1");
    if ($sr3 && $sr3->num_rows > 0) $sessionOn = true;

    while ($row = $res->fetch_assoc()) {
        $lastId = (int)$row['id'];
        // Get the latest 10 rows for the table
        $tblRes = $conn->query("SELECT id,temperature,humidity,soil_moisture,fan_status,recorded_at FROM sensor_logs ORDER BY recorded_at DESC LIMIT 10");
        $tableRows = [];
        while ($tr = $tblRes->fetch_assoc()) $tableRows[] = $tr;
        echo "id: {$lastId}\n";
        echo "event: reading\n";
        echo "data: " . json_encode([
            "latest" => [
                "id"           => (int)$row['id'],
                "temperature"  => (float)$row['temperature'],
                "humidity"     => (float)$row['humidity'],
                "soil_moisture"=> (int)$row['soil_moisture'],
                "fan_status"   => $sessionOn ? 1 : 0,  // always reflect actual session state
                "recorded_at"  => $row['recorded_at'],
            ],
            "table" => $tableRows,
            "total" => (int)$count,
        ]) . "\n\n";
        flush();
    }
    $stmt->close();

    // ── Heartbeat ─────────────────────────────────────────────
    if (time() - $lastHB >= $heartbeat) {
        echo ": heartbeat " . date('H:i:s') . "\n\n";
        flush();
        $lastHB = time();
    }

    sleep($pollMs);
}

// Tell the client to reconnect immediately
echo "event: reconnect\n";
echo "data: {}\n\n";
flush();

$conn->close();
