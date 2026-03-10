<?php
// ============================================================
//  setup_db.php  —  Creates all solar-dryer tables
//  Call once: http://localhost/FARM_MANAGEMENT_SYSTEM/api/setup_db.php
// ============================================================
header('Content-Type: application/json');

$conn = new mysqli("localhost","root","","");
if ($conn->connect_error) { die(json_encode(["error"=>$conn->connect_error])); }

$conn->query("CREATE DATABASE IF NOT EXISTS farm_db");
$conn->select_db("farm_db");

$tables = [];

// sensor_logs (already exists but ensure it)
$tables['sensor_logs'] = "CREATE TABLE IF NOT EXISTS sensor_logs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    farm_id       INT           NOT NULL DEFAULT 1,
    temperature   DECIMAL(5,2)  NOT NULL,
    humidity      DECIMAL(5,2)  NOT NULL,
    soil_moisture TINYINT       NOT NULL,
    fan_status    TINYINT       NOT NULL DEFAULT 0,
    recorded_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_recorded_at (recorded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// Add fan_status column if missing
$conn->query("ALTER TABLE sensor_logs ADD COLUMN IF NOT EXISTS fan_status TINYINT NOT NULL DEFAULT 0");

// crop profiles
$tables['crop_profiles'] = "CREATE TABLE IF NOT EXISTS crop_profiles (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    crop_name       VARCHAR(50)   NOT NULL,
    min_temp        DECIMAL(5,2)  NOT NULL,
    max_temp        DECIMAL(5,2)  NOT NULL,
    target_temp     DECIMAL(5,2)  NOT NULL,
    target_humidity DECIMAL(5,2)  NOT NULL,
    duration_hours  INT           NOT NULL,
    danger_temp     DECIMAL(5,2)  NOT NULL DEFAULT 65.00,
    description     TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// drying sessions
$tables['drying_sessions'] = "CREATE TABLE IF NOT EXISTS drying_sessions (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    farmer_id        INT           NOT NULL DEFAULT 1,
    crop_name        VARCHAR(50)   NOT NULL,
    target_temp      DECIMAL(5,2)  NOT NULL,
    duration_minutes INT           NOT NULL,
    start_time       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    end_time         TIMESTAMP     NULL,
    status           ENUM('running','completed','stopped') DEFAULT 'running',
    notes            TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// control commands
$tables['control_commands'] = "CREATE TABLE IF NOT EXISTS control_commands (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    session_id    INT           NULL,
    command_type  VARCHAR(50)   NOT NULL,
    command_value JSON          NOT NULL,
    created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    received      TINYINT       NOT NULL DEFAULT 0,
    received_at   TIMESTAMP     NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

// alerts log
$tables['alerts_log'] = "CREATE TABLE IF NOT EXISTS alerts_log (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    session_id   INT           NULL,
    alert_type   VARCHAR(50)   NOT NULL,
    message      TEXT          NOT NULL,
    temperature  DECIMAL(5,2)  NULL,
    humidity     DECIMAL(5,2)  NULL,
    triggered_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resolved     TINYINT       NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$results = [];
foreach ($tables as $name => $sql) {
    $ok = $conn->query($sql);
    $results[$name] = $ok ? "OK" : $conn->error;
}

// Seed crop profiles if empty
$cnt = $conn->query("SELECT COUNT(*) AS c FROM crop_profiles")->fetch_assoc()['c'];
if ((int)$cnt === 0) {
    $crops = [
        ["Paddy",  40, 55, 50, 14, 6,  65, "Rice paddy: remove moisture to safe storage level of 14%"],
        ["Corn",   45, 58, 53, 13, 8,  68, "Maize: dry to 13% moisture for safe storage"],
        ["Chili",  50, 62, 58, 10, 10, 70, "Red/green chili: dry to 10% to prevent mold"],
        ["Cocoa",  40, 48, 45,  7, 14, 60, "Cocoa beans: slow gentle drying to preserve flavour"],
        ["Cassava",50, 60, 55, 12,  7, 68, "Cassava chips: rapid drying to 12% moisture"],
    ];
    $s = $conn->prepare("INSERT INTO crop_profiles (crop_name,min_temp,max_temp,target_temp,target_humidity,duration_hours,danger_temp,description) VALUES (?,?,?,?,?,?,?,?)");
    foreach ($crops as $c) {
        $s->bind_param("sddddids", $c[0],$c[1],$c[2],$c[3],$c[4],$c[5],$c[6],$c[7]);
        $s->execute();
    }
    $results['crop_profiles_seeded'] = "5 crops inserted";
}

$conn->close();
echo json_encode(["status"=>"done","tables"=>$results]);
