<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost", "root", "", "farm_db");
if ($conn->connect_error) {
    echo json_encode(["error" => "DB failed"]);
    exit;
}

$session   = null;
$remaining = null;

$justCompleted    = false;
$completedMessage = null;
$completedCrop    = null;

// 1. Get current running session
$r = $conn->query("SELECT * FROM drying_sessions WHERE status='running' ORDER BY start_time DESC LIMIT 1");

if ($r && $row = $r->fetch_assoc()) {
    $session = $row;

    $startTime = strtotime($session['start_time']);
    $durationMinutes = (int)$session['duration_minutes'];
    $endTarget = $startTime + ($durationMinutes * 60);
    $remaining = max(0, $endTarget - time());

    // 2. Auto-complete session if timer finished
    if (time() >= $endTarget) {
        $sid  = (int)$session['id'];
        $crop = $session['crop_name'];

        // Mark session completed
        $conn->query("
            UPDATE drying_sessions
            SET status='completed', end_time=NOW()
            WHERE id=$sid
        ");

        // Turn fan OFF in latest log row
        $conn->query("
            UPDATE sensor_logs
            SET fan_status='OFF'
            WHERE id = (
                SELECT id2 FROM (
                    SELECT id AS id2
                    FROM sensor_logs
                    ORDER BY id DESC
                    LIMIT 1
                ) AS temp
            )
        ");

        // Insert stop command
        $cmdValue = $conn->real_escape_string(json_encode([
            "fan"    => false,
            "reason" => "completed"
        ]));

        $conn->query("
            INSERT INTO control_commands (session_id, command_type, command_value, received)
            VALUES ($sid, 'STOP_SESSION', '$cmdValue', 0)
        ");

        // Completion info for frontend
        $justCompleted    = true;
        $completedCrop    = $crop;
        $completedMessage = "Drying completed successfully for " . $crop;

        // No active session now
        $session   = null;
        $remaining = 0;
    }
}

// 3. Crop profiles
$profiles = [];
$pr = $conn->query("SELECT * FROM crop_profiles ORDER BY crop_name");
if ($pr) {
    while ($row = $pr->fetch_assoc()) {
        $profiles[] = $row;
    }
}

// 4. Recent unresolved alerts
$alerts = [];
$ar = $conn->query("SELECT * FROM alerts_log WHERE resolved=0 ORDER BY triggered_at DESC LIMIT 5");
if ($ar) {
    while ($row = $ar->fetch_assoc()) {
        $alerts[] = $row;
    }
}

// 5. Latest sensor reading
$latest = null;
$lr = $conn->query("SELECT * FROM sensor_logs ORDER BY recorded_at DESC LIMIT 1");
if ($lr && $row = $lr->fetch_assoc()) {
    $latest = $row;
}

// 6. Session history
$history = [];
$hr = $conn->query("SELECT * FROM drying_sessions ORDER BY start_time DESC LIMIT 5");
if ($hr) {
    while ($row = $hr->fetch_assoc()) {
        $history[] = $row;
    }
}

// 7. Last received command time
$lastCmd = null;
$cr = $conn->query("SELECT received_at FROM control_commands WHERE received=1 ORDER BY received_at DESC LIMIT 1");
if ($cr && $row = $cr->fetch_assoc()) {
    $lastCmd = $row['received_at'];
}

$conn->close();

// 8. Final JSON response
echo json_encode([
    "session"            => $session,
    "remaining_sec"      => $remaining,
    "profiles"           => $profiles,
    "alerts"             => $alerts,
    "latest_reading"     => $latest,
    "session_history"    => $history,
    "last_cmd_received"  => $lastCmd,
    "just_completed"     => $justCompleted,
    "completed_crop"     => $completedCrop,
    "completed_message"  => $completedMessage
]);
?>