<table>
    <thead>
        <tr>
            <th>Resident</th>
            <th>Jerk Magnitude</th>
            <th>Timestamp</th>
        </tr>
    </thead>
    <tbody>

        <?php

        include_once("../insa_db.php");
        $db = dbConnect();

        $falls = [];
        if (isset($_REQUEST["devid"])) {
            $stmt = $db->prepare('SELECT
    f.id,
    f.timestamp,
    f.device_id,
    f.jerkmagnitude,
    r.name
FROM
    falls f
JOIN
    residents r ON f.device_id = r.device_id
WHERE device_id = :dev;');
            $stmt->execute(['dev' => htmlspecialchars($_REQUEST["devid"])]);
            $falls = $stmt->fetchAll();
        } else {
            $stmt = $db->prepare('SELECT
    f.id,
    f.timestamp,
    f.device_id,
    f.jerkmagnitude,
    r.name
FROM
    fall_alerts f
JOIN
    residents r ON f.device_id = r.device_id;');
            $stmt->execute();
            $falls = $stmt->fetchAll();
        }

        foreach ($falls as $_fall_index => $fall) {
            $currentTime = time();
            $tenMinutesAgo = $currentTime - (10 * 60);
            $fallTime = strtotime($fall['timestamp']);
            $isRecent = ($fallTime > $tenMinutesAgo);

            $severity = ($fall['jerkmagnitude'] > 5) ? 'badge-danger' : (($fall['jerkmagnitude'] > 3) ? 'badge-warning' : 'badge-success');

        ?>

            <tr <?php if ($isRecent) echo ' class="recent-fall"' ?>>
                <td><strong>${record.name || 'Unknown'}</strong><span class="resident-badge">${record.device_id}</span></td>
                <td><span class="badge ${severity}">${record.jerkmagnitude.toFixed(2)}</span></td>
                <td>${new Date(record.timestamp).toLocaleString()}<?php if ($isRecent) echo ' 🚨' ?></td>
            </tr>
        

        <?php
        }

        ?>

    </tbody>
</table>