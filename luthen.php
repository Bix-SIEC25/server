<?php
session_start();

if (!isset($_SESSION["connected"]) || $_SESSION["connected"] != true) {
    http_response_code(404);
    die();
}

$groups = ["main", "logs", "admin"];
$known_messages = [
    "ping",
    "pong",
    "test"
];


if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group   = $_POST['group'] ?? '';
    $message = $_POST['message'] ?? '';

    if ($group !== '' && $message !== '') {
        // $payload = 'bix/' . htmlspecialchars($group) . ':' . htmlspecialchars($message);
        $payload = 'bix/' . $group . ':' . $message;

        @file_get_contents(
            'http://127.0.0.1:6442/push',
            false,
            stream_context_create([
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-Type: text/plain\r\n",
                    'content' => $payload
                ]
            ])
        );

        $_SESSION['history'][] = [
            'time'    => date('H:i:s'),
            'group'   => $group,
            'message' => $message
        ];
    }

    // Prevent form resubmission on refresh
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Push Message</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        select,
        input {
            font-size: 16px;
            padding: 4px;
        }

        .history {
            font-size: 14px;
        }

        .history div {
            margin-bottom: 2px;
        }

        .box {
            background-color: lightgray;
            font-size: x-small;
            border-radius: 3px;
            padding: 5px;
            margin: 3px;
        }

        .messagebox {
            color: gray;
            font-size: x-small;
            padding: 5px;
            border-radius: 3px;
            margin: 3px;
            border: solid 1px gray;
        }
    </style>
</head>

<body>

    <form method="post" id="pushForm" autocomplete="off">
        <label>
            Group:
            <select name="group" id="groupSelect" autofocus>
                <option value="-">—</option>
                <?php
                foreach ($groups as $_key => $option) {
                    echo "<option value=\"$option\">$option</option>";
                }
                ?>
            </select>
        </label>

        <label>
            Message:
            <input
                type="text"
                name="message"
                id="messageInput"
                size="40">
        </label>
    </form>

    <p class="suggestions">

        <?php
        foreach ($groups as $_key => $group) {
            echo "<span class='groupbox box'>$group</span>";
        }
        ?>
        <br>
        <br>
        <?php
        foreach ($known_messages as $_key => $msg) {
            echo "<span class='messagebox'>$msg</span>";
        }
        ?>
    </p>

    <?php if (!empty($_SESSION['history'])): ?>
        <div class="history">
            <strong>History</strong>
            <?php foreach (array_reverse($_SESSION['history']) as $entry): ?>
                <div>
                    [<?= htmlspecialchars($entry['time']) ?>]
                    <strong><?= htmlspecialchars($entry['group']) ?></strong>:
                    <?= htmlspecialchars($entry['message']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <script>
        const knownMessages = <?= json_encode(array_values($known_messages)) ?>;
    </script>

    <script>
        const groupSelect = document.getElementById('groupSelect');
        const messageInput = document.getElementById('messageInput');
        const form = document.getElementById('pushForm');

        let suggestion = '';
        let suggestionActive = false;

        /* ---------- keyboard flow ---------- */

        groupSelect.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                messageInput.focus();
            }
        });

        /* ---------- suggestion logic ---------- */

        messageInput.addEventListener('input', () => {
            const value = messageInput.value;
            suggestion = '';
            suggestionActive = false;

            if (!value) {
                updateGhost('');
                return;
            }

            const match = knownMessages.find(m =>
                m.startsWith(value) && m !== value
            );

            if (match) {
                suggestion = match;
                suggestionActive = true;
                updateGhost(match.slice(value.length));
            } else {
                updateGhost('');
            }
        });

        messageInput.addEventListener('keydown', e => {
            if (e.key === 'Tab' && suggestionActive) {
                e.preventDefault();
                messageInput.value = suggestion;
                suggestion = '';
                suggestionActive = false;
                updateGhost('');
                return;
            }

            if (e.key === 'Enter') {
                e.preventDefault();
                form.submit();
            }
        });

        /* ---------- ghost text rendering ---------- */

        const ghost = document.createElement('span');
        ghost.style.position = 'absolute';
        ghost.style.pointerEvents = 'none';
        ghost.style.color = '#aaa';
        ghost.style.fontFamily = 'inherit';
        ghost.style.fontSize = 'inherit';
        ghost.style.whiteSpace = 'pre';

        document.body.appendChild(ghost);

        function updateGhost(text) {
            if (!text) {
                ghost.textContent = '';
                return;
            }

            const rect = messageInput.getBoundingClientRect();
            const style = window.getComputedStyle(messageInput);

            ghost.textContent = text;
            ghost.style.left =
                rect.left +
                parseInt(style.paddingLeft) +
                measureTextWidth(messageInput.value, style.font) +
                'px';

            ghost.style.top =
                rect.top +
                parseInt(style.paddingTop) +
                'px';
        }

        function measureTextWidth(text, font) {
            const canvas = measureTextWidth.canvas || (measureTextWidth.canvas = document.createElement("canvas"));
            const ctx = canvas.getContext("2d");
            ctx.font = font;
            return ctx.measureText(text).width;
        }
    </script>


</body>

</html>