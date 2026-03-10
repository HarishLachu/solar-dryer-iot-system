<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost", "root", "", "farm_db");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$crop_id     = isset($_POST['crop_id']) ? (int)$_POST['crop_id'] : 0;
$target_temp = isset($_POST['target_temp']) ? (float)$_POST['target_temp'] : 0;
$duration    = isset($_POST['duration']) ? (float)$_POST['duration'] : 0; // minutes

if ($crop_id <= 0) {
    echo json_encode(["error" => "Invalid crop selected"]);
    exit;
}

if ($duration <= 0) {
    echo json_encode(["error" => "Invalid duration"]);
    exit;
}

/* ------------------------------------------------------------
   1. Check if a session is already running
------------------------------------------------------------ */
$running = $conn->query("SELECT * FROM drying_sessions WHERE status='running' ORDER BY start_time DESC LIMIT 1");
if ($running && $row = $running->fetch_assoc()) {
    $startTime = strtotime($row['start_time']);
    $endTarget = $startTime + ((int)$row['duration_minutes'] * 60);

    if (time() >= $endTarget) {
        // auto-complete expired old session
        $sid = (int)$row['id'];
        $conn->query("UPDATE drying_sessions SET status='completed', end_time=NOW() WHERE id=$sid");

        $cmdValue = $conn->real_escape_string(json_encode([
            "fan" => false,
            "reason" => "completed"
        ]));
        $conn->query("INSERT INTO control_commands (session_id, command_type, command_value, received)
                      VALUES ($sid, 'STOP_SESSION', '$cmdValue', 0)");
    } else {
        echo json_encode(["error" => "A drying session is already running"]);
        exit;
    }
}

/* ------------------------------------------------------------
   2. Load selected crop profile
------------------------------------------------------------ */
$stmt = $conn->prepare("SELECT id, crop_name, target_temp FROM crop_profiles WHERE id=? LIMIT 1");
$stmt->bind_param("i", $crop_id);
$stmt->execute();
$res = $stmt->get_result();
$crop = $res->fetch_assoc();
$stmt->close();

if (!$crop) {
    echo json_encode(["error" => "Crop profile not found"]);
    exit;
}

$crop_name = $crop['crop_name'];

if ($target_temp <= 0) {
    $target_temp = (float)$crop['target_temp'];
}

$duration_minutes = (int) round($duration);
$farmer_id = 1; // change if needed
$notes = "Started from dashboard";

/* ------------------------------------------------------------
   3. Insert new session
------------------------------------------------------------ */
$stmt = $conn->prepare("INSERT INTO drying_sessions
    (farmer_id, crop_name, target_temp, duration_minutes, start_time, status, notes)
    VALUES (?, ?, ?, ?, NOW(), 'running', ?)");

$stmt->bind_param("isdis", $farmer_id, $crop_name, $target_temp, $duration_minutes, $notes);

if (!$stmt->execute()) {
    echo json_encode(["error" => "Failed to start session"]);
    exit;
}

$session_id = $stmt->insert_id;
$stmt->close();

/* ------------------------------------------------------------
   4. Insert START_SESSION command
------------------------------------------------------------ */
$cmd = json_encode([
    "session_id"   => $session_id,
    "crop"         => $crop_name,
    "target_temp"  => $target_temp,
    "duration_min" => $duration_minutes,
    "fan"          => true
]);

$cmdStmt = $conn->prepare("INSERT INTO control_commands (session_id, command_type, command_value, received)
                           VALUES (?, 'START_SESSION', ?, 0)");
$cmdStmt->bind_param("is", $session_id, $cmd);
$cmdStmt->execute();
$cmdStmt->close();

echo json_encode([
    "status" => "started",
    "session_id" => $session_id,
    "crop_name" => $crop_name,
    "target_temp" => $target_temp,
    "duration_minutes" => $duration_minutes
]);

$conn->close();
?>