<?php
// ============================================================
//  GET /api/get_sensor_data.php
//  Returns JSON: latest zone readings + history for charts
// ============================================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost", "root", "", "");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "DB connect failed: " . $conn->connect_error]);
    exit;
}

// Ensure database + table exist
$conn->query("CREATE DATABASE IF NOT EXISTS farm_db");
$conn->select_db("farm_db");
$conn->query("
    CREATE TABLE IF NOT EXISTS solar_dryer_logs (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        device_id     VARCHAR(100)  NOT NULL DEFAULT 'solar_dryer_001',
        location      VARCHAR(100)  NOT NULL,
        temperature   DECIMAL(5,2)  NOT NULL,
        humidity      DECIMAL(5,2)  NOT NULL,
        recorded_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_location    (location),
        INDEX idx_recorded_at (recorded_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// ── 1. Latest reading per location ───────────────────────────
$locations = ['collector', 'top_tray', 'middle_tray', 'bottom_tray'];
$latest    = [];

foreach ($locations as $loc) {
    $stmt = $conn->prepare(
        "SELECT temperature, humidity, recorded_at
         FROM solar_dryer_logs
         WHERE location = ?
         ORDER BY recorded_at DESC
         LIMIT 1"
    );
    $stmt->bind_param("s", $loc);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $latest[$loc] = $row;
    } else {
        $latest[$loc] = ["temperature" => null, "humidity" => null, "recorded_at" => null];
    }
    $stmt->close();
}

// ── 2. History for charts – last 20 distinct timestamps ──────
$histResult = $conn->query("
    SELECT recorded_at, location, temperature, humidity
    FROM solar_dryer_logs
    ORDER BY recorded_at DESC
    LIMIT 100
");

$rawHistory = [];
while ($row = $histResult->fetch_assoc()) {
    $rawHistory[] = $row;
}
$rawHistory = array_reverse($rawHistory);

// Group by timestamp label
$grouped = [];
foreach ($rawHistory as $row) {
    $label = date("H:i", strtotime($row['recorded_at']));
    $grouped[$label][$row['location']] = (float)$row['temperature'];
    $grouped[$label]['humidity']        = (float)$row['humidity'];
}

// Keep last 20 unique timestamps
$labels      = [];
$collector   = [];
$top_tray    = [];
$middle_tray = [];
$bottom_tray = [];
$humidSeries = [];

foreach (array_slice($grouped, -20) as $label => $vals) {
    $labels[]      = $label;
    $collector[]   = $vals['collector']    ?? null;
    $top_tray[]    = $vals['top_tray']     ?? null;
    $middle_tray[] = $vals['middle_tray']  ?? null;
    $bottom_tray[] = $vals['bottom_tray']  ?? null;
    $humidSeries[] = $vals['humidity']     ?? null;
}

// ── 3. Total log count ───────────────────────────────────────
$countResult = $conn->query("SELECT COUNT(*) AS total FROM solar_dryer_logs");
$totalLogs   = (int)$countResult->fetch_assoc()['total'];

// ── 4. Recent 10 rows for the table ─────────────────────────
$tableResult = $conn->query("
    SELECT device_id, location, temperature, humidity, recorded_at
    FROM solar_dryer_logs
    ORDER BY recorded_at DESC
    LIMIT 10
");
$tableRows = [];
while ($row = $tableResult->fetch_assoc()) {
    $tableRows[] = $row;
}

$conn->close();

echo json_encode([
    "latest"     => $latest,
    "chart"      => [
        "labels"      => $labels,
        "collector"   => $collector,
        "top_tray"    => $top_tray,
        "middle_tray" => $middle_tray,
        "bottom_tray" => $bottom_tray,
        "humidity"    => $humidSeries,
    ],
    "total_logs" => $totalLogs,
    "table_rows" => $tableRows,
]);
