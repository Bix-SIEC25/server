-- MySQL / MariaDB Schema
-- Converted from PostgreSQL dump

CREATE DATABASE IF NOT EXISTS gateway;
USE gateway;

-- Table: fall_alerts
CREATE TABLE fall_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    device_id INT NOT NULL,
    jerkmagnitude FLOAT
);

-- Table: residents
CREATE TABLE residents (
    device_id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Table: residents_vitals
CREATE TABLE residents_vitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    device_id INT NOT NULL,
    spo2 FLOAT,
    heart_rate FLOAT,
    temperature FLOAT
);

-- Foreign key relationships (optional, can be uncommented if needed)
-- ALTER TABLE fall_alerts
--     ADD CONSTRAINT fk_fall_alerts_device
--     FOREIGN KEY (device_id) REFERENCES residents(device_id)
--     ON DELETE CASCADE;

-- ALTER TABLE residents_vitals
--     ADD CONSTRAINT fk_residents_vitals_device
--     FOREIGN KEY (device_id) REFERENCES residents(device_id)
--     ON DELETE CASCADE;

