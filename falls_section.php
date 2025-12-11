<table>
    <thead>
        <tr>
            <th>Resident</th>
            <th>Jerk Magnitude</th>
            <th>Zone</th>
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
    f.aonz,
    r.name
FROM
    fall_alerts f
JOIN
    residents r ON f.device_id = r.device_id
WHERE f.device_id = :dev
ORDER BY f.timestamp DESC
;');
            $stmt->execute(['dev' => htmlspecialchars($_REQUEST["devid"])]);
            $falls = $stmt->fetchAll();
        } else {
            $stmt = $db->prepare('SELECT
    f.id,
    f.timestamp,
    f.device_id,
    f.jerkmagnitude,
    f.zone,
    r.name
FROM
    fall_alerts f
JOIN
    residents r ON f.device_id = r.device_id
ORDER BY f.timestamp DESC
;');
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
                <td><strong><?php echo $fall['name'] ?></strong><span class="resident-badge"><? echo $fall['device_id']?></span></td>
                <td><span class="badge <?php echo $severity?>"><?php echo $fall['jerkmagnitude']?></span></td>
                <td><span class="badge"><?php echo $fall['zone']?></span></td>
                <td><?php echo $fall['timestamp']; if ($isRecent) echo ' 🚨' ?></td>
            </tr>
        

        <?php
        }

        ?>

    </tbody>
</table>
