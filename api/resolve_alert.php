<?php
// POST /api/resolve_alert.php  — Mark all alerts as resolved
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost","root","","farm_db");
if ($conn->connect_error) { echo json_encode(["error"=>"DB failed"]); exit; }
$conn->query("UPDATE alerts_log SET resolved=1 WHERE resolved=0");
echo json_encode(["status"=>"ok","affected"=>$conn->affected_rows]);
$conn->close();
