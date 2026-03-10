<?php
// ============================================================
//  POST /api/log_alert.php
//  Called by Arduino when a threshold is breached.
//  Also called by frontend for manual alert logging.
// ============================================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$type    = isset($_POST['type'])        ? trim($_POST['type'])         : 'UNKNOWN';
$message = isset($_POST['message'])     ? trim($_POST['message'])      : '';
$temp    = isset($_POST['temperature']) ? (float)$_POST['temperature'] : null;
$humid   = isset($_POST['humidity'])    ? (float)$_POST['humidity']    : null;

$conn = new mysqli("localhost","root","","farm_db");
if ($conn->connect_error) { echo json_encode(["error"=>"DB failed"]); exit; }

// Get active session
$sessionId = null;
$r = $conn->query("SELECT id FROM drying_sessions WHERE status='running' ORDER BY start_time DESC LIMIT 1");
if ($r && $row = $r->fetch_assoc()) $sessionId = (int)$row['id'];

$stmt = $conn->prepare(
    "INSERT INTO alerts_log (session_id, alert_type, message, temperature, humidity)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("issdd", $sessionId, $type, $message, $temp, $humid);
$stmt->execute();
$alertId = $stmt->insert_id;
$stmt->close();

// If OVERHEAT → auto queue STOP_FAN command
if (in_array($type, ['OVERHEAT','DANGER'])) {
    $cmdVal = json_encode(["fan"=>false,"reason"=>"overheat","temp"=>$temp]);
    $cs = $conn->prepare(
        "INSERT INTO control_commands (session_id, command_type, command_value) VALUES (?, 'STOP_FAN', ?)"
    );
    $cs->bind_param("is", $sessionId, $cmdVal);
    $cs->execute();
    $cs->close();
}

$conn->close();
echo json_encode(["status"=>"logged","alert_id"=>$alertId,"type"=>$type]);
