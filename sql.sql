-- ใช้ฐานข้อมูลของคุณ
USE s67160349;

-- 1. ตารางเก็บข้อมูลดิบ (แก้ไขจากไฟล์ของคุณ)
-- (ใช้ชื่อ SensorData1 ให้ตรงกับ PHP)
CREATE TABLE `SensorDataProject` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `sensor` varchar(30) NOT NULL,
  `location` varchar(30) NOT NULL,
  `value1` varchar(100) DEFAULT NULL, -- ใช้เก็บ Capacity (%)
  `value2` float DEFAULT NULL,        -- ใช้เก็บ Temperature (°C)
  `value3` float DEFAULT NULL,        -- ใช้เก็บ Gas Raw Value
  `reading_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 2. (ใหม่) ตารางเก็บการแจ้งเตือน (Alerts)
CREATE TABLE `AlertsLog` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `alert_type` varchar(50) NOT NULL, -- (เช่น 'CAPACITY', 'TEMP', 'SYSTEM')
  `message` varchar(255) NOT NULL,   -- (เช่น 'ขยะถึง 80%', 'ระบบเริ่มทำงาน')
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. (ใหม่) ตารางเก็บสถิติรายชั่วโมง
CREATE TABLE `HourlyStats` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `hour_timestamp` datetime NOT NULL,    -- (เช่น 2025-10-24 13:00:00)
  `avg_capacity` float DEFAULT NULL,
  `max_temp` float DEFAULT NULL,
  `max_gas` float DEFAULT NULL,
  UNIQUE KEY `hour_timestamp` (`hour_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;