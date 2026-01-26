<table>
    <thead>
        <tr>
            <th>Resident</th>
            <th>SpO2</th>
            <th>Heart Rate</th>
            <!-- <th>Board Temperature</th> -->
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
    residents_vitals v
JOIN
    residents r ON v.device_id = r.device_id
WHERE v.device_id = :dev
ORDER BY v.timestamp DESC
;');
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
    residents r ON v.device_id = r.device_id
ORDER BY v.timestamp DESC
;');
            $stmt->execute();
            $vitalss = $stmt->fetchAll();
        }

        foreach ($vitalss as $_vitals_index => $vitals) {
            $spo2Status = ($vitals['spo2'] < 90) ? 'badge-danger' : (($vitals['spo2'] < 95) ? 'badge-warning' : 'badge-success');

            $hrStatus = (($vitals['heart_rate'] < 60) || ($vitals['heart_rate'] > 100)) ?
                'badge-warning' : 'badge-success';

        ?>

            <tr>
                <td><strong><?php echo $vitals['name'] ?></strong><span class="resident-badge"><? echo $vitals['device_id']?></span></td>
                <td><span class="badge <?php echo $spo2Status ?>"><?php echo $vitals['spo2'] ?>%</span></td>
                <td><span class="badge <?php echo $hrStatus ?>"><?php echo $vitals['heart_rate'] ?> BPM</span></td>
                <!-- <td><?php echo ($vitals['temperature'] ? $vitals['temperature'] . '°C' : 'N/A') ?></td> -->
                <td><?php echo $vitals['timestamp'] ?></td>
            </tr>

        <?php
        }
        ?>

    </tbody>
</table>
