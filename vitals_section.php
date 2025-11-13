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

        $falls = [];
        if (isset($_REQUEST["devid"])) {
            $stmt = $db->prepare('SELECT * FROM fall_alerts WHERE device_id = :dev');
            $stmt->execute(['dev' => htmlspecialchars($_REQUEST["devid"])]);
            $falls = $stmt->fetchAll();
        } else {
            $stmt = $db->prepare('SELECT * FROM fall_alerts');
            $stmt->execute();
            $falls = $stmt->fetchAll();
        }

        foreach ($falls as $_fall_index => $fall) {
            ?>



            <?php
        }

        ?>

    </tbody>
</table>