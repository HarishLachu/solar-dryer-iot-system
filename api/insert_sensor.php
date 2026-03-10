<?php
// ============================================================
//  POST /api/insert_sensor.php
//  Receives sensor data from ESP32 / Wokwi
//  Saves to farm_db.sensor_logs
//  Auto-completes expired drying session
// ============================================================
date_default_timezone_set('Asia/Colombo');
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Only POST requests are allowed."
    ]);
    exit;
}

/* ============================================================
   1. Collect inputs
============================================================ */
$temperature   = isset($_POST['temperature'])   ? (float)$_POST['temperature']   : null;
$humidity      = isset($_POST['humidity'])      ? (float)$_POST['humidity']      : null;
$soil_moisture = isset($_POST['soil_moisture']) ? (int)$_POST['soil_moisture']   : null;
$farm_id       = isset($_POST['farm_id'])       ? (int)$_POST['farm_id']         : 1;
$fan_status_in = isset($_POST['fan_status'])    ? $_POST['fan_status']            : 0;

if ($temperature === null || $humidity === null || $soil_moisture === null) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields: temperature, humidity, soil_moisture."
    ]);
    exit;
}

/* ============================================================
   2. DB connection
============================================================ */
$conn = new mysqli("localhost", "root", "", "farm_db");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "DB connect failed: " . $conn->connect_error
    ]);
    exit;
}

/* ============================================================
   3. Ensure required tables exist
============================================================ */
$conn->query("
    CREATE TABLE IF NOT EXISTS sensor_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        farm_id INT NOT NULL DEFAULT 1,
        temperature DECIMAL(5,2) NOT NULL,
        humidity DECIMAL(5,2) NOT NULL,
        soil_moisture TINYINT NOT NULL COMMENT '0-100 percent',
        fan_status TINYINT NOT NULL DEFAULT 0,
        recorded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_farm_id (farm_id),
        INDEX idx_recorded_at (recorded_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$conn->query("
    CREATE TABLE IF NOT EXISTS alerts_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_id INT NULL,
        alert_type VARCHAR(50) NOT NULL,
        message TEXT NOT NULL,
        temperature DECIMAL(5,2) NULL,
        humidity DECIMAL(5,2) NULL,
        triggered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        resolved TINYINT NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

/* ============================================================
   4. AUTO-COMPLETE expired running session
   This runs every time Wokwi sends sensor data
============================================================ */
$run = $conn->query("
    SELECT * FROM drying_sessions
    WHERE status='running'
    ORDER BY start_time DESC
    LIMIT 1
");

if ($run && $sess = $run->fetch_assoc()) {
    $sid = (int)$sess['id'];
    $startTime = strtotime($sess['start_time']);
    $endTarget = $startTime + ((int)$sess['duration_minutes'] * 60);

    if (time() >= $endTarget) {
        // Mark completed
        $conn->query("
            UPDATE drying_sessions
            SET status='completed', end_time=NOW()
            WHERE id=$sid
        ");

        // Insert STOP_SESSION only once for completed reason
        $chk = $conn->query("
            SELECT id FROM control_commands
            WHERE session_id=$sid
              AND command_type='STOP_SESSION'
              AND command_value LIKE '%completed%'
            LIMIT 1
        ");

        if (!$chk || $chk->num_rows === 0) {
            $cmdValue = $conn->real_escape_string(json_encode([
                "fan" => false,
                "reason" => "completed"
            ]));

            $conn->query("
                INSERT INTO control_commands (session_id, command_type, command_value, received)
                VALUES ($sid, 'STOP_SESSION', '$cmdValue', 0)
            ");
        }
    }
}

/* ============================================================
   5. Normalize fan status
============================================================ */
$fan_status = (
    $fan_status_in === 'ON' ||
    $fan_status_in === '1'  ||
    $fan_status_in === 1    ||
    $fan_status_in === true
) ? 1 : 0;

/* If there is NO running session, force fan OFF */
$hasSession = $conn->query("SELECT id FROM drying_sessions WHERE status='running' LIMIT 1");
if (!$hasSession || $hasSession->num_rows === 0) {
    $fan_status = 0;
}

/* ============================================================
   6. Insert sensor log
============================================================ */
$stmt = $conn->prepare("
    INSERT INTO sensor_logs (farm_id, temperature, humidity, soil_moisture, fan_status)
    VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("iddii", $farm_id, $temperature, $humidity, $soil_moisture, $fan_status);

if (!$stmt->execute()) {
    $err = $stmt->error;
    $stmt->close();
    $conn->close();
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Insert failed: " . $err
    ]);
    exit;
}

$insertedId = $stmt->insert_id;
$stmt->close();

/* ============================================================
   7. Alert logic
============================================================ */
$dangerTemp = 65.0;
$tooHotTemp = 60.0;
$tooColdTemp = 35.0;
$sessionId = null;

$sr = $conn->query("
    SELECT ds.id, cp.danger_temp
    FROM drying_sessions ds
    LEFT JOIN crop_profiles cp ON ds.crop_name = cp.crop_name
    WHERE ds.status='running'
    ORDER BY ds.start_time DESC
    LIMIT 1
");

if ($sr && $srow = $sr->fetch_assoc()) {
    $sessionId = (int)$srow['id'];
    if (!empty($srow['danger_temp'])) {
        $dangerTemp = (float)$srow['danger_temp'];
    }
}

$al = [];

if ($temperature >= $dangerTemp) {
    $al[] = ['DANGER', "DANGER: Temperature {$temperature}°C exceeds danger limit {$dangerTemp}°C!"];
} elseif ($temperature >= $tooHotTemp) {
    $al[] = ['OVERHEAT', "WARNING: Temperature {$temperature}°C too high (>60°C). Risk of crop damage."];
} elseif ($temperature < $tooColdTemp) {
    $al[] = ['TOO_COLD', "WARNING: Temperature {$temperature}°C too low (<35°C). Drying ineffective."];
}

if ($humidity > 80) {
    $al[] = ['HIGH_HUMIDITY', "WARNING: Humidity {$humidity}% too high (>80%). Poor drying conditions."];
}

foreach ($al as $entry) {
    $type = $entry[0];
    $msg  = $entry[1];

    $typeEsc = $conn->real_escape_string($type);
    $recent = $conn->query("
        SELECT id FROM alerts_log
        WHERE alert_type='$typeEsc'
          AND resolved=0
          AND triggered_at > DATE_SUB(NOW(), INTERVAL 10 MINUTE)
        LIMIT 1
    ");

    if (!$recent || $recent->num_rows === 0) {
        $as = $conn->prepare("
            INSERT INTO alerts_log (session_id, alert_type, message, temperature, humidity)
            VALUES (?, ?, ?, ?, ?)
        ");
        $as->bind_param("issdd", $sessionId, $type, $msg, $temperature, $humidity);
        $as->execute();
        $as->close();
    }
}

/* ============================================================
   8. Success response
============================================================ */
$conn->close();

http_response_code(200);
echo json_encode([
    "status"        => "success",
    "inserted_id"   => $insertedId,
    "farm_id"       => $farm_id,
    "temperature"   => $temperature,
    "humidity"      => $humidity,
    "soil_moisture" => $soil_moisture,
    "fan_status"    => $fan_status,
    "alerts"        => count($al),

    "debug_running_session_found" => isset($sess) ? true : false,
    "debug_session_id"            => isset($sid) ? $sid : null,
    "debug_start_time"            => isset($sess['start_time']) ? $sess['start_time'] : null,
    "debug_duration_minutes"      => isset($sess['duration_minutes']) ? $sess['duration_minutes'] : null,
    "debug_now"                   => date('Y-m-d H:i:s'),
    "debug_end_target"            => isset($endTarget) ? date('Y-m-d H:i:s', $endTarget) : null,
    "debug_should_complete"       => isset($endTarget) ? (time() >= $endTarget) : false
]);
?>