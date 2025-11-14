<table>
    <thead>
        <tr>
            <th>Resident</th>
            <th>SpO2</th>
            <th>Heart Rate</th>
            <th>Board Temperature</th>
            <th>Timestamp</th>
        </tr>
    </thead>
    <tbody>

        <?php

        include_once("../insa_db.php");
        $db = dbConnect();

        $vitalss = [];
        if (isset($_REQUEST["devid"])) {
            $stmt = $db->prepare('SELECT
    v.id,
    v.timestamp,
    v.device_id,
    v.spo2,
    v.heart_rate,
    v.temperature,
    r.name
FROM
    vitals v
JOIN
    residents r ON v.device_id = r.device_id
 WHERE device_id = :dev;');
            $stmt->execute(['dev' => htmlspecialchars($_REQUEST["devid"])]);
            $vitalss = $stmt->fetchAll();
        } else {
            $stmt = $db->prepare('SELECT
    v.id,
    v.timestamp,
    v.device_id,
    v.spo2,
    v.heart_rate,
    v.temperature,
    r.name
FROM
    residents_vitals v
JOIN
    residents r ON v.device_id = r.device_id;
');
            $stmt->execute();
            $vitalss = $stmt->fetchAll();
        }

        foreach ($vitalss as $_vitals_index => $vitals) {
        ?>

            <tr>
                <td><strong>${record.name || 'Unknown'}</strong><span class="resident-badge">${record.device_id}</span></td>
                <td><span class="badge ${spo2Status}">${record.spo2}%</span></td>
                <td><span class="badge ${hrStatus}">${record.heart_rate} BPM</span></td>
                <td>${record.temperature ? record.temperature + '°C' : 'N/A'}</td>
                <td>${new Date(record.timestamp).toLocaleString()}</td>
            </tr>

        <?php
        }
        ?>

    </tbody>
</table>