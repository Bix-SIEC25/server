<?php
include_once("../insa_db.php");
$db = dbConnect();

$results = [];
if (isset($_GET['id']) && $_GET['id'] !== '') {
    $id = $_GET['id'];
    if (!ctype_digit(strval($id))) {
        http_response_code(400);
        echo "<h1>Invalid id</h1>";
        exit;
    }
    $stmt = $db->prepare('SELECT * FROM residents WHERE device_id = :id');
    $stmt->execute([':id' => (int)$id]);
    $row = $stmt->fetch();
    if ($row) $results[] = $row;
} elseif (isset($_GET['name']) && trim($_GET['name']) !== '') {
    $name = trim($_GET['name']);
    // case-insensitive exact match using COLLATE
    $stmt = $db->prepare('SELECT * FROM residents WHERE name COLLATE utf8mb4_unicode_ci = :name');
    $stmt->execute([':name' => $name]);
    $rows = $stmt->fetchAll();
    if ($rows) $results = $rows;
} else {
    $stmt = $db->query('SELECT * FROM residents ORDER BY name, last_name LIMIT 200');
    $results = $stmt->fetchAll();
}

function e($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="card.css">
    <title>Resident Card</title>
</head>

<body>
    <div class="wrap">
        <header>
            <div>
                <h1>Resident card</h1>
            </div>
        </header>

        <?php if (empty($results)): ?>
            <div class="empty">No residents found for the current <?php
                if (isset($_REQUEST["name"]))
                    echo "name \"" . htmlspecialchars($_REQUEST["name"]) . "\".";
                else if (isset($_REQUEST["id"]))
                    echo "ID (" . htmlspecialchars($_REQUEST["id"]) . ").";
                else echo "search."
                ?></div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($results as $r): ?>
                    <article class="card" aria-labelledby="r-<?php echo e($r['device_id']); ?>">
                        <div class="row">
                            <div class="avatar" aria-hidden="true">
                                <?php
                                $initials = '';
                                $initials .= isset($r['name']) && $r['name'] !== '' ? mb_substr($r['name'], 0, 1, 'UTF-8') : '';
                                $initials .= isset($r['last_name']) && $r['last_name'] !== '' ? mb_substr($r['last_name'], 0, 1, 'UTF-8') : '';
                                echo e(mb_strtoupper($initials, 'UTF-8'));
                                ?>
                            </div>
                            <div style="flex:1;">
                                <div class="name" id="r-<?php echo e($r['device_id']); ?>"><?php echo e($r['name'] . ' ' . $r['last_name']); ?></div>
                                <div class="muted">Device ID: <strong><?php echo e($r['device_id']); ?></strong></div>
                                <div class="meta">
                                    <div class="pill">Age: <?php echo e($r['age']); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="notes">
                            <strong>Notes about resident:</strong><br>
                            <?php echo nl2br(e($r['notes'])); ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>