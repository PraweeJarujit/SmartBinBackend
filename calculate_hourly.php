<?php
// (ไฟล์นี้ต้องตั้งค่าให้รันอัตโนมัติทุก 1 ชั่วโมง)
include 'connect.php';

// 1. กำหนดช่วงเวลา (1 ชั่วโมงที่ผ่านมา)
$hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));
$now = date('Y-m-d H:i:s');
$hour_marker = date('Y-m-d H:00:00'); // เวลาหัวชั่วโมงปัจจุบัน

// 2. คำนวณสถิติ
$sql = "SELECT 
            AVG(CAST(value1 AS DECIMAL(10,2))) as avg_cap, 
            MAX(CAST(value2 AS DECIMAL(10,2))) as max_temp, 
            MAX(CAST(value3 AS DECIMAL(10,2))) as max_gas 
        FROM SensorData1 
        WHERE reading_time >= '$hour_ago' AND reading_time < '$now'";

$result = $conn->query($sql);
$stats = $result->fetch_assoc();

// 3. บันทึกสถิติลงตาราง HourlyStats
$insert_sql = "INSERT INTO HourlyStats (hour_timestamp, avg_capacity, max_temp, max_gas) 
               VALUES (?, ?, ?, ?)";
               
$stmt = $conn->prepare($insert_sql);
$stmt->bind_param("sddd", $hour_marker, $stats['avg_cap'], $stats['max_temp'], $stats['max_gas']);
$stmt->execute();

echo "Hourly stats calculated and inserted for $hour_marker";
$stmt->close();
$conn->close();
?>