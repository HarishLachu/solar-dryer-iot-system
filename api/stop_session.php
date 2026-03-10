<?php
// ============================================================
//  POST /api/stop_session.php
//  Stops the active session, turns off fan.
// ============================================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost","root","","farm_db");
if ($conn->connect_error) { echo json_encode(["error"=>"DB failed"]); exit; }

// Get running session
$r = $conn->query("SELECT id FROM drying_sessions WHERE status='running' ORDER BY start_time DESC LIMIT 1");
$sessionId = null;
if ($r && $row = $r->fetch_assoc()) $sessionId = (int)$row['id'];

// Mark stopped
$conn->query("UPDATE drying_sessions SET status='stopped', end_time=NOW() WHERE status='running'");

// Reset fan_status to 0 on all sensor_logs rows so UI shows OFF immediately
$conn->query("UPDATE sensor_logs SET fan_status=0");

// Queue STOP command
$cmdVal = json_encode(["fan"=>false,"reason"=>"user_stop"]);
if ($sessionId) {
    $cs = $conn->prepare(
        "INSERT INTO control_commands (session_id, command_type, command_value) VALUES (?, 'STOP_SESSION', ?)"
    );
    $cs->bind_param("is",$sessionId,$cmdVal);
    $cs->execute();
    $cs->close();
} else {
    $conn->query("INSERT INTO control_commands (command_type, command_value) VALUES ('STOP_SESSION', '{\"fan\":false}')");
}

$conn->close();
echo json_encode(["status"=>"stopped","session_id"=>$sessionId]);
