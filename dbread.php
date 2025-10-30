<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Bin Sensor - Eco Monitor</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* 1. Global Styles: Eco / Green Theme */
        :root {
            --color-bg: #f4f7f6;     /* Light Gray/Off-white Background */
            --color-main-text: #333333; /* Dark Gray Text */
            --color-light-text: #666666; /* Medium Gray for labels */
            --color-primary: #4CAF50; /* Primary Green */
            --color-secondary: #8BC34A; /* Lighter Green */
            --color-warning: #FFC107; /* Yellow (Standard) */
            --color-critical: #F44336; /* Red (Standard) */
            --color-border: #e0e0e0; /* Light Gray Border */
            --font-primary: 'Roboto', sans-serif;
        }

        body {
            background-color: var(--color-bg);
            color: var(--color-main-text);
            font-family: var(--font-primary);
            padding: 30px;
            text-align: center;
        }

        h1 {
            color: var(--color-primary); /* Use primary green for title */
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 40px;
            border-bottom: 2px solid var(--color-primary); /* Green underline */
            padding-bottom: 10px;
            display: inline-block; /* Make underline fit text */
        }

        /* 2. Main Container */
        .data-console {
            max-width: 1100px;
            margin: 0 auto;
            border: 1px solid var(--color-border);
            padding: 25px;
            background-color: #ffffff; /* White inner area */
            border-radius: 8px; /* Slightly more rounded */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* 3. Sensor Display - Key Indicators */
        .sensor-data {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .data-card {
            border: 1px solid var(--color-border);
            padding: 20px 15px;
            margin: 10px;
            width: 280px;
            background-color: #f9f9f9; /* Very light gray */
            border-radius: 6px;
            text-align: left;
        }

        .data-label {
            color: var(--color-light-text);
            font-size: 0.9em;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .data-value {
            font-size: 2.5em;
            font-weight: 700;
            color: var(--color-primary); /* Green data values */
        }

        /* 4. Garbage Level - Progress Bar */
        .level-container {
            margin-top: 30px;
            text-align: left;
        }

        .level-bar-outer {
            height: 25px;
            width: 100%;
            background-color: #e0e0e0; /* Light gray background */
            border-radius: 12px; /* Rounded bar */
            overflow: hidden;
            margin-top: 5px;
            border: 1px solid var(--color-border);
        }

        .level-bar-inner {
            height: 100%;
            background-color: var(--color-secondary); /* Lighter Green: OK */
            width: 0%;
            transition: width 0.5s ease-in-out, background-color 0.5s;
            border-radius: 12px;
        }

        /* Bar Colors based on Status */
        .level-bar-inner.warning { background-color: var(--color-warning); }
        .level-bar-inner.critical { background-color: var(--color-critical); }

        .status-text {
            color: var(--color-main-text);
            font-size: 1.1em;
            margin-bottom: 5px;
        }

        /* 5. Notification - Alert Panel */
        .alert-box {
            margin-top: 40px;
            padding: 15px;
            border: 1px solid var(--color-critical);
            background-color: rgba(244, 67, 54, 0.1); /* Light red background */
            color: var(--color-critical);
            font-weight: 700;
            display: none;
            border-radius: 4px;
        }

        .alert-box.active {
            display: block;
        }

        /* 6. History Log - Clean Table */
        .data-log {
            margin-top: 40px;
            text-align: left;
        }

        .data-log h2 {
            color: var(--color-primary);
            font-size: 1.5em;
            margin-bottom: 15px;
            border-bottom: 1px solid var(--color-border);
            padding-bottom: 5px;
        }

        .data-log table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
        }

        .data-log th, .data-log td {
            border-bottom: 1px solid var(--color-border);
            padding: 12px 15px; /* Slightly more padding */
            text-align: left;
        }

        .data-log th {
            color: var(--color-light-text);
            background-color: #f1f8e9; /* Very light green header */
            font-weight: bold;
        }
        
        /* Highlight status */
        .status-CRITICAL { color: var(--color-critical); font-weight: bold; }
        .status-WARNING { color: var(--color-warning); font-weight: bold; }
        .status-OK { color: var(--color-secondary); }

         /* Alert Feed types */
        .type-SYSTEM { color: #666; font-style: italic; }
        .type-CAPACITY { color: var(--color-main-text); }
        .type-DANGER { color: var(--color-critical); font-weight: bold; }

    </style>
</head>
<body>
    <div class="data-console">
        <h1>Smart Bin Sensor - Eco Monitor 🌿</h1>

        <div class="level-container">
            <div class="status-text">🗑️ **ระดับขยะ: <span id="capacity-percent">--</span>%** [สถานะ: <span id="capacity-status">กำลังเชื่อมต่อ...</span>]</div>
            <div class="level-bar-outer">
                <div class="level-bar-inner" id="level-bar"></div>
            </div>
        </div>

        <div class="sensor-data">
            <div class="data-card">
                <div class="data-label">🗑️ ระดับขยะ (V1)</div>
                <div class="data-value" id="capacity-card-value">-- %</div>
            </div>
            <div class="data-card">
                <div class="data-label">🌡️ อุณหภูมิ (V2)</div>
                <div class="data-value" id="temp-value">-- °C</div>
            </div>
            <div class="data-card">
                <div class="data-label">💨 ระดับก๊าซ (V3)</div>
                <div class="data-value" id="gas-value">-- RAW</div>
            </div>
            <div class="data-card">
                <div class="data-label">💧 ความชื้น (ไม่ได้บันทึก)</div>
                <div class="data-value" id="humidity-value">-- %</div>
            </div>
        </div>

        <div class="alert-box" id="alert-box">
            🚨 **แจ้งเตือนเร่งด่วน: ถังขยะใกล้เต็ม! กรุณาเก็บขยะ.**
        </div>

        <div class="data-log">
            <h2>ประวัติการแจ้งเตือน</h2>
            <table>
                <thead>
                    <tr>
                        <th>เวลา</th>
                        <th>ประเภท</th>
                        <th>ข้อความ</th>
                    </tr>
                </thead>
                <tbody id="alert-feed-body">
                    </tbody>
            </table>
        </div>

        <div class="data-log">
            <h2>สถิติรายชั่วโมง</h2>
            <table>
                <thead>
                    <tr>
                        <th>ชั่วโมง</th>
                        <th>ระดับขยะเฉลี่ย</th>
                        <th>อุณหภูมิสูงสุด</th>
                        <th>ก๊าซสูงสุด</th>
                    </tr>
                </thead>
                <tbody id="hourly-stats-body">
                    </tbody>
            </table>
        </div>

        <div class="data-log">
            <h2>ประวัติข้อมูลล่าสุด (10 รายการ)</h2>
            <table>
                <thead>
                    <tr>
                        <th>เวลา</th>
                        <th>ระดับขยะ (V1)</th>
                        <th>อุณหภูมิ (V2)</th>
                        <th>ก๊าซ (V3)</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody id="log-body">
                    </tbody>
            </table>
        </div>
    </div>

    <script>
        // *******************************************************************
        // เปลี่ยน URL นี้เป็น URL ของไฟล์ "dbread_updated.php" ของคุณ (HTTPS)
        const DATA_API_URL = "https://yourserver.com/dbread_updated.php"; 
        // *******************************************************************

        function updateDashboard(latest, status) {
            const bar = document.getElementById('level-bar');
            const alertBox = document.getElementById('alert-box');
            
            const capacity = parseInt(latest.value1);
            const temp = parseFloat(latest.value2);
            const gasRaw = parseFloat(latest.value3);

            // 1. Update capacity bar and text
            bar.style.width = capacity + '%';
            document.getElementById('capacity-percent').textContent = capacity;
            document.getElementById('capacity-status').textContent = status.toUpperCase();
            document.getElementById('capacity-card-value').textContent = capacity + ' %';

            // 2. Manage bar color and alert box visibility
            bar.classList.remove('warning', 'critical');
            alertBox.classList.remove('active');
            
            if (status === 'CRITICAL') {
                bar.classList.add('critical');
                alertBox.classList.add('active');
            } else if (status === 'WARNING') {
                bar.classList.add('warning');
            } 
            
            // 3. Update main sensor values
            document.getElementById('temp-value').textContent = temp.toFixed(1) + ' °C';
            document.getElementById('gas-value').textContent = gasRaw.toFixed(0) + ' RAW'; 
            document.getElementById('humidity-value').textContent = '-- %'; // Humidity not directly logged
        }

        function updateLogTable(history) {
            const logBody = document.getElementById('log-body');
            logBody.innerHTML = ''; 

            if (history && history.length > 0) {
                history.forEach(record => {
                    const newRow = logBody.insertRow();
                    const timeStr = new Date(record.timestamp).toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    
                    newRow.insertCell(0).textContent = timeStr;
                    newRow.insertCell(1).textContent = record.value1 + '%';
                    newRow.insertCell(2).textContent = parseFloat(record.value2).toFixed(1);
                    newRow.insertCell(3).textContent = record.value3;
                    
                    const statusCell = newRow.insertCell(4);
                    statusCell.textContent = record.alert_status.toUpperCase();
                    statusCell.className = 'status-' + record.alert_status.toUpperCase();
                });
            } else {
                 logBody.innerHTML = '<tr><td colspan="5" style="text-align:center;">ไม่พบข้อมูลประวัติ</td></tr>';
            }
        }

        function updateAlertFeed(alerts) {
            const alertBody = document.getElementById('alert-feed-body');
            alertBody.innerHTML = ''; 

            if (alerts && alerts.length > 0) {
                alerts.forEach(record => {
                    const newRow = alertBody.insertRow();
                    const timeStr = new Date(record.timestamp).toLocaleString('th-TH', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
                    
                    newRow.insertCell(0).textContent = timeStr;
                    
                    const typeCell = newRow.insertCell(1);
                    typeCell.textContent = record.alert_type;
                    typeCell.className = 'type-' + record.alert_type.toUpperCase();

                    newRow.insertCell(2).textContent = record.message;
                });
            } else {
                 alertBody.innerHTML = '<tr><td colspan="3" style="text-align:center;">ไม่พบการแจ้งเตือน</td></tr>';
            }
        }

        function updateHourlyStats(stats) {
            const statsBody = document.getElementById('hourly-stats-body');
            statsBody.innerHTML = ''; 

            if (stats && stats.length > 0) {
                stats.forEach(record => {
                    const newRow = statsBody.insertRow();
                    // แสดงผลเฉพาะชั่วโมง
                    const timeStr = new Date(record.hour_timestamp).toLocaleTimeString('th-TH', { hour: '2-digit', minute:'2-digit' }); 
                    
                    newRow.insertCell(0).textContent = timeStr;
                    newRow.insertCell(1).textContent = parseFloat(record.avg_capacity).toFixed(1) + ' %';
                    newRow.insertCell(2).textContent = parseFloat(record.max_temp).toFixed(1) + ' °C';
                    newRow.insertCell(3).textContent = parseFloat(record.max_gas).toFixed(0) + ' RAW';
                });
            } else {
                 statsBody.innerHTML = '<tr><td colspan="4" style="text-align:center;">ไม่พบข้อมูลสถิติรายชั่วโมง</td></tr>';
            }
        }


        function fetchDataAndUpdate() {
            fetch(DATA_API_URL)
                .then(response => {
                    if (!response.ok) {
                        // More specific error for network issues
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                 })
                .then(data => {
                    if (data.status === 'success') {
                        // Check if latest data exists before processing
                        if (data.latest) {
                             const status = data.log_history && data.log_history.length > 0 ? data.log_history[0].alert_status : 'OK';
                             updateDashboard(data.latest, status);
                        } else {
                             document.getElementById('capacity-status').textContent = 'NO LATEST DATA';
                        }
                        
                        updateLogTable(data.log_history);
                        updateAlertFeed(data.alert_feed);
                        updateHourlyStats(data.hourly_stats);

                    } else {
                         document.getElementById('capacity-status').textContent = 'API RETURNED ERROR!';
                         console.error("API Error Message:", data.message); // Log API specific error
                    }
                })
                .catch(error => {
                    console.error('Fetch operation error:', error);
                    document.getElementById('capacity-status').textContent = 'FETCH FAILED!';
                    document.getElementById('alert-box').textContent = `ERROR: Cannot fetch data from API. ${error.message}`;
                    document.getElementById('alert-box').classList.add('active');
                    // Clear tables on fetch error to avoid showing stale data
                    document.getElementById('log-body').innerHTML = '<tr><td colspan="5" style="text-align:center;">Error loading data...</td></tr>';
                    document.getElementById('alert-feed-body').innerHTML = '<tr><td colspan="3" style="text-align:center;">Error loading alerts...</td></tr>';
                    document.getElementById('hourly-stats-body').innerHTML = '<tr><td colspan="4" style="text-align:center;">Error loading stats...</td></tr>';
                });
        }

        // Initial fetch and set interval
        fetchDataAndUpdate();
        setInterval(fetchDataAndUpdate, 5000); // Update every 5 seconds

    </script>
</body>
</html>