<?php
include 'connect.php'; // (ใช้ไฟล์ connect.php เดิมของคุณ)

$api_key_value = "tPmAT5Ab3j7F9"; 

// ฟังก์ชันทำความสะอาด Input (เหมือนเดิม)
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// ฟังก์ชันสำหรับ INSERT Alert ใหม่ (ฟังก์ชันใหม่)
function createAlert($conn, $type, $message) {
    $sql = "INSERT INTO AlertsLog (alert_type, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $type, $message);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input(isset($_POST["api_key"]) ? $_POST["api_key"] : '');
    
    if($api_key != $api_key_value) {
        http_response_code(401); // Unauthorized
        die("Wrong API Key provided.");
    }
    
    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        http_response_code(500); 
        die("Connection failed: " . $conn->connect_error);
    }

    // --- (อัปเดต) ตรวจสอบว่าเป็นการส่ง Event หรือ Data ---
    
    // 1. ถ้าเป็นการส่ง Event (เช่น Startup)
    if (isset($_POST["event_type"])) {
        $event_type = test_input($_POST["event_type"]);
        $message = test_input($_POST["message"]);
        createAlert($conn, $event_type, $message);
        echo "Event Alert logged successfully";
        $conn->close();
        exit();
    }
    
    // 2. ถ้าเป็นการส่งข้อมูลเซ็นเซอร์ (ปกติ)
    $sensor = test_input(isset($_POST["sensor"]) ? $_POST["sensor"] : 'UNKNOWN');
    $location = test_input(isset($_POST["location"]) ? $_POST["location"] : 'N/A');
    $value1 = test_input(isset($_POST["value1"]) ? $_POST["value1"] : '0'); // Capacity
    $value2 = test_input(isset($_POST["value2"]) ? $_POST["value2"] : '-99.0'); // Temperature
    $value3 = test_input(isset($_POST["value3"]) ? $_POST["value3"] : '0'); // Gas Raw

    // --- (อัปเดต) ตรวจสอบค่าเพื่อสร้าง Alert ---
    
    // ดึงค่าล่าสุด (ก่อนบันทึก) เพื่อเปรียบเทียบ
    $last_sql = "SELECT value1, value2, value3 FROM SensorDataProject ORDER BY id DESC LIMIT 1";
    $result = $conn->query($last_sql);
    $last_row = $result->fetch_assoc();
    
    $last_capacity = (int)$last_row['value1'];
    $last_temp = (float)$last_row['value2'];
    $last_gas = (int)$last_row['value3'];
    
    $new_capacity = (int)$value1;
    $new_temp = (float)$value2;
    $new_gas = (int)$value3;

    // ตรวจสอบ Thresholds (เกณฑ์)
    if ($new_capacity == 0 && $last_capacity > 0) createAlert($conn, 'CAPACITY', 'ถังขยะว่าง (0%)');
    if ($new_capacity >= 20 && $last_capacity < 20) createAlert($conn, 'CAPACITY', 'ขยะถึง 20%');
    if ($new_capacity >= 40 && $last_capacity < 40) createAlert($conn, 'CAPACITY', 'ขยะถึง 40%');
    if ($new_capacity >= 60 && $last_capacity < 60) createAlert($conn, 'CAPACITY', 'ขยะถึง 60%');
    if ($new_capacity >= 80 && $last_capacity < 80) createAlert($conn, 'CAPACITY', 'ขยะถึง 80% (Warning)');
    if ($new_capacity >= 100 && $last_capacity < 100) createAlert($conn, 'CAPACITY', 'ขยะเต็ม 100% (Critical)');
    
    // (ตัวอย่าง) เกณฑ์แจ้งเตือนอุณหภูมิ/ก๊าซ (ปรับค่าเอง)
    if ($new_temp >= 50.0 && $last_temp < 50.0) createAlert($conn, 'DANGER', 'อุณหภูมิสูงเกิน 50°C!');
    if ($new_gas >= 1000 && $last_gas < 1000) createAlert($conn, 'DANGER', 'ระดับก๊าซสูง (ค่า Raw > 1000)');

    
    // --- บันทึกข้อมูลดิบลง SensorData1 (เหมือนเดิม) ---
    $sql = "INSERT INTO SensorData1 (sensor, location, value1, value2, value3)
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $sensor, $location, $value1, $value2, $value3);

    if ($stmt->execute()) {
        http_response_code(200); 
        echo "New record created successfully (and alerts checked)";
    } else {
        http_response_code(500); 
        echo "Error executing statement: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();

} else {
    http_response_code(405);
    echo "Method not allowed.";
}
?>