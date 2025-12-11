<table>
    <thead>
        <tr>
            <th>Sender</th>
            <th>Level</th>
            <th>Message</th>
            <th>Timestamp</th>
        </tr>
    </thead>
    <tbody>

        <?php

        include_once("../insa_db.php");
        $db = dbConnect();


        $logs = [];
        if (isset($_REQUEST["sender"]) && isset($_REQUEST["type"])) {
            $stmt = $db->prepare('SELECT * FROM bix_logs WHERE sender = :sender AND type = :type ORDER BY timestamp DESC LIMIT 200;');
            $stmt->execute(['sender' => htmlspecialchars($_REQUEST["sender"]), 'type' => htmlspecialchars($_REQUEST["type"])]);
            $logs = $stmt->fetchAll();
        } else if (isset($_REQUEST["type"])) {
            $stmt = $db->prepare('SELECT * FROM bix_logs WHERE type = :type ORDER BY timestamp DESC LIMIT 200;');
            $stmt->execute(['type' => htmlspecialchars($_REQUEST["type"])]);
            $logs = $stmt->fetchAll();
        } else if (isset($_REQUEST["sender"])) {
            $stmt = $db->prepare('SELECT * FROM bix_logs WHERE sender = :sender ORDER BY timestamp DESC LIMIT 200;');
            $stmt->execute(['sender' => htmlspecialchars($_REQUEST["sender"])]);
            $logs = $stmt->fetchAll();
        } else {
            $stmt = $db->prepare('SELECT * FROM bix_logs ORDER BY timestamp DESC LIMIT 200;');
            $stmt->execute();
            $logs = $stmt->fetchAll();
        }

        foreach ($logs as $_log_index => $log) {
            if ($log['type'] > 3) continue;

	    if (!isset($_REQUEST["watchdog"]) && str_ends_with(htmlspecialchars($log["sender"]),"watchdog")) continue;

            $logType = ($log['type'] == 0) ? 'error' : (($log['type'] == 1) ? 'warning' : (($log['type'] == 2) ? 'trace' : 'debug'));
            // $logType = ($log['type'] == 0) ? 'error' : (($log['type'] == 1) ? 'warning' : (($log['type'] == 2) ? 'trace' : 'debug'));
        ?>

            <tr>
                <td><strong><?php echo $log['sender'] ?></strong></td>
                <td><span class="badge b<?php echo $logType ?>"><?php echo $logType ?></span></td>
                <td><span class="msg"><?php echo $log['message'] ?></span></td>
                <td><?php echo $log['timestamp'] ?></td>
            </tr>

        <?php
        }
        ?>

    </tbody>
</table>
