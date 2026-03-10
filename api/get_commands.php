<?php
// ============================================================
//  GET /api/get_commands.php
//  Called by Arduino every loop to receive control commands.
//  Returns the latest unread command + active session params.
//  Marks commands as received.
// ============================================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost","root","","farm_db");
if ($conn->connect_error) {
    echo json_encode(["error"=>"DB connect failed"]);
    exit;
}

// ── Get active drying session ─────────────────────────────────
$session = null;
$sr = $conn->query("SELECT * FROM drying_sessions WHERE status='running' ORDER BY start_time DESC LIMIT 1");
if ($sr && $row = $sr->fetch_assoc()) {
    $session = $row;
}

// ── Get oldest unread command ─────────────────────────────────
$cmd = null;
$cr = $conn->query("SELECT * FROM control_commands WHERE received=0 ORDER BY created_at ASC LIMIT 1");
if ($cr && $row = $cr->fetch_assoc()) {
    $cmd = $row;
    // Mark as received
    $conn->query("UPDATE control_commands SET received=1, received_at=NOW() WHERE id=".(int)$row['id']);
}

// ── Get danger temp from active crop ─────────────────────────
$dangerTemp = 65.0;
$targetTemp = 50.0;
if ($session) {
    $cp = $conn->prepare("SELECT danger_temp,target_temp FROM crop_profiles WHERE crop_name=? LIMIT 1");
    $cp->bind_param("s", $session['crop_name']);
    $cp->execute();
    $res = $cp->get_result()->fetch_assoc();
    if ($res) {
        $dangerTemp = (float)$res['danger_temp'];
        $targetTemp = (float)$res['target_temp'];
    }
    $cp->close();
}

// ── Build response ────────────────────────────────────────────
$response = [
    "session_active" => $session ? true : false,
    "session_id"     => $session ? (int)$session['id'] : null,
    "crop"           => $session ? $session['crop_name'] : null,
    "target_temp"    => $session ? (float)$session['target_temp'] : $targetTemp,
    "danger_temp"    => $dangerTemp,
    "command"        => $cmd ? $cmd['command_type'] : null,
    "command_value"  => $cmd ? json_decode($cmd['command_value'], true) : null,
    "fan_on"         => $session ? true : false,  // default ON when session running
    "timestamp"      => date('Y-m-d H:i:s'),
];

// Override fan_on if specific command
if ($cmd) {
    if ($cmd['command_type'] === 'STOP_FAN')  $response['fan_on'] = false;
    if ($cmd['command_type'] === 'START_FAN') $response['fan_on'] = true;
    if ($cmd['command_type'] === 'STOP_SESSION') {
        $response['session_active'] = false;
        $response['fan_on'] = false;
    }
}

$conn->close();
echo json_encode($response);
