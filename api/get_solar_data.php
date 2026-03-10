<?php
// ============================================================
//  GET /api/get_solar_data.php
//  Returns latest + history sensor readings from sensor_logs
//  for the Solar Drying Area dashboard.
// ============================================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost", "root", "", "");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "DB connect failed: " . $conn->connect_error]);
    exit;
}

$conn->query("CREATE DATABASE IF NOT EXISTS farm_db");
$conn->select_db("farm_db");

// Ensure table exists (safety fallback)
$conn->query("
    CREATE TABLE IF NOT EXISTS sensor_logs (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        farm_id       INT            NOT NULL DEFAULT 1,
        temperature   DECIMAL(5,2)   NOT NULL,
        humidity      DECIMAL(5,2)   NOT NULL,
        soil_moisture TINYINT        NOT NULL,
        fan_status    TINYINT        NOT NULL DEFAULT 0,
        recorded_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_farm_id    (farm_id),
        INDEX idx_recorded_at (recorded_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
$conn->query("ALTER TABLE sensor_logs ADD COLUMN IF NOT EXISTS fan_status TINYINT NOT NULL DEFAULT 0");

// ── 1. Latest single reading ──────────────────────────────────
$latestRow = null;
$res = $conn->query("
    SELECT id, temperature, humidity, soil_moisture, fan_status, recorded_at
    FROM sensor_logs
    ORDER BY recorded_at DESC
    LIMIT 1
");
if ($res && $row = $res->fetch_assoc()) {
    $latestRow = $row;
}

// ── 2. History – last 20 readings for charts ─────────────────
$histRes = $conn->query("
    SELECT temperature, humidity, soil_moisture, fan_status, recorded_at
    FROM sensor_logs
    ORDER BY recorded_at DESC
    LIMIT 20
");

$labels   = [];
$temps    = [];
$humids   = [];
$soils    = [];
$rawRows  = [];
while ($row = $histRes->fetch_assoc()) {
    $rawRows[] = $row;
}
$rawRows = array_reverse($rawRows);   // oldest first for charts

foreach ($rawRows as $row) {
    $labels[]  = date("H:i:s", strtotime($row['recorded_at']));
    $temps[]   = (float)$row['temperature'];
    $humids[]  = (float)$row['humidity'];
    $soils[]   = (int)$row['soil_moisture'];
}

// ── 3. Total log count ────────────────────────────────────────
$cntRes   = $conn->query("SELECT COUNT(*) AS total FROM sensor_logs");
$total    = (int)$cntRes->fetch_assoc()['total'];

// ── 4. Last 10 rows for table ─────────────────────────────────
$tableRes = $conn->query("
    SELECT temperature, humidity, soil_moisture, fan_status, recorded_at
    FROM sensor_logs
    ORDER BY recorded_at DESC
    LIMIT 10
");
$tableRows = [];
while ($row = $tableRes->fetch_assoc()) {
    $tableRows[] = $row;
}

// ── Override fan_status if a session is running ─────────────
$sessionRunning = false;
$sr2 = $conn->query("SELECT id FROM drying_sessions WHERE status='running' LIMIT 1");
if ($sr2 && $sr2->num_rows > 0) $sessionRunning = true;
// Always force fan_status to match actual session state so stale DB values don't flip the UI
if ($latestRow) $latestRow['fan_status'] = $sessionRunning ? 1 : 0;
// Apply same override to every table row
foreach ($tableRows as &$tr) $tr['fan_status'] = $sessionRunning ? 1 : 0;
unset($tr);

$conn->close();

echo json_encode([
    "latest"     => $latestRow,
    "chart"      => [
        "labels"       => $labels,
        "temperature"  => $temps,
        "humidity"     => $humids,
        "soil_moisture"=> $soils,
    ],
    "total_logs" => $total,
    "table_rows" => $tableRows,
]);
