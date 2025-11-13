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

        $vitalss = [];
        if (isset($_REQUEST["devid"])) {
            $stmt = $db->prepare('SELECT * FROM residents_vitals WHERE device_id = :dev');
            $stmt->execute(['dev' => htmlspecialchars($_REQUEST["devid"])]);
            $vitalss = $stmt->fetchAll();
            echo "asked";
        } else {
            $stmt = $db->prepare('SELECT * FROM residents_vitals');
            $stmt->execute();
            $vitalss = $stmt->fetchAll();
            echo "who?";
        }

        foreach ($vitalss as $_vitals_index => $vitals) {
        ?>

        

        <?php
        }
        ?>

    </tbody>
</table>