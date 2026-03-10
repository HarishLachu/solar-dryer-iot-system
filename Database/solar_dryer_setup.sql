-- ============================================================
--  Solar Dryer IoT Database Setup
--  Run this in phpMyAdmin or: mysql -u root < solar_dryer_setup.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS farm_db;
USE farm_db;

CREATE TABLE IF NOT EXISTS solar_dryer_logs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    device_id     VARCHAR(100)   NOT NULL DEFAULT 'solar_dryer_001',
    location      VARCHAR(100)   NOT NULL COMMENT 'collector | top_tray | middle_tray | bottom_tray',
    temperature   DECIMAL(5,2)   NOT NULL,
    humidity      DECIMAL(5,2)   NOT NULL,
    recorded_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_location    (location),
    INDEX idx_recorded_at (recorded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  Sample seed data so charts display immediately
-- ============================================================
INSERT INTO solar_dryer_logs (device_id, location, temperature, humidity, recorded_at) VALUES
('solar_dryer_001','collector',   44.5, 62, NOW() - INTERVAL 120 MINUTE),
('solar_dryer_001','top_tray',    49.0, 62, NOW() - INTERVAL 120 MINUTE),
('solar_dryer_001','middle_tray', 47.2, 62, NOW() - INTERVAL 120 MINUTE),
('solar_dryer_001','bottom_tray', 45.8, 62, NOW() - INTERVAL 120 MINUTE),

('solar_dryer_001','collector',   46.1, 60, NOW() - INTERVAL 90 MINUTE),
('solar_dryer_001','top_tray',    50.3, 60, NOW() - INTERVAL 90 MINUTE),
('solar_dryer_001','middle_tray', 48.5, 60, NOW() - INTERVAL 90 MINUTE),
('solar_dryer_001','bottom_tray', 46.9, 60, NOW() - INTERVAL 90 MINUTE),

('solar_dryer_001','collector',   47.8, 58, NOW() - INTERVAL 60 MINUTE),
('solar_dryer_001','top_tray',    52.1, 58, NOW() - INTERVAL 60 MINUTE),
('solar_dryer_001','middle_tray', 49.9, 58, NOW() - INTERVAL 60 MINUTE),
('solar_dryer_001','bottom_tray', 48.2, 58, NOW() - INTERVAL 60 MINUTE),

('solar_dryer_001','collector',   49.0, 57, NOW() - INTERVAL 30 MINUTE),
('solar_dryer_001','top_tray',    53.5, 57, NOW() - INTERVAL 30 MINUTE),
('solar_dryer_001','middle_tray', 51.0, 57, NOW() - INTERVAL 30 MINUTE),
('solar_dryer_001','bottom_tray', 49.5, 57, NOW() - INTERVAL 30 MINUTE),

('solar_dryer_001','collector',   45.0, 60, NOW()),
('solar_dryer_001','top_tray',    50.0, 60, NOW()),
('solar_dryer_001','middle_tray', 48.0, 60, NOW()),
('solar_dryer_001','bottom_tray', 46.0, 60, NOW());
