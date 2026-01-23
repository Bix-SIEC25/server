<?php
function updateScenario($db, string $scenario_name, bool $condition, string $mark): bool
{
    if (!$condition) return false;

    $stmt = $db->prepare("SELECT scenario, scenario_name,last_step FROM bix_state");
    $stmt->execute([]);
    $res = $stmt->fetchAll();
    
    if (sizeof($res) != 1) return false;
    if ($scenario_name != "" && $scenario_name != $res[0]["scenario_name"]) return false;
    // if second time we receive this step then no change to do
    if ($mark == $res[0]["last_step"]) return true;

    $scenar_txt = $res[0]["scenario"];
    // $scenar_txt = '[{"step":"1"},{"transition":"1->2"},{"step":"2"},{"transition":"2->3"},{"step":"3"},{"transition":"3->4"},{"step":"4"},{"transition":"4->5"},{"step":"5"}]';

    $scenario = json_decode($scenar_txt, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die('JSON decode error: ' . json_last_error_msg());
    }

    markStep($scenario, $mark);

    @file_get_contents(
        "http://127.0.0.1:6442/push",
        false,
        stream_context_create(['http' => [
            'method' => 'POST',
            'header' => "Content-Type: text/plain\r\n",
            'content' => "bix/admin:markStep:" . $mark
        ]])
    );

    $s = json_encode($scenario);

    $updateQuery = "UPDATE `bix_state` 
    SET
        `scenario`=:s,
        `last_step`=:laststep
    WHERE 1";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->execute([
        "s" => $s,
        "laststep" => $mark
    ]);

    return true;
}

function markStep(array &$scenario, string $step): void
{
    // does step exists
    $foundIndex = null;
    foreach ($scenario as $idx => $item) {
        if (isset($item['step']) && $item['step'] === $step) {
            $foundIndex = $idx;
            break;
        }
    }

    if ($foundIndex === null) {
        error_log("{$step} doesn't exist!");
        return;
    }

    $len = count($scenario);

    for ($number = 0; $number < $len; $number++) {
        $state = $scenario[$number];

        if (isset($state['step'])) {
            if ($state['step'] === $step) {
                if (isset($scenario[$number]['state']) && $scenario[$number]['state'] === 'notdone') {
                    $scenario[$number]['state'] = 'done';
                    return;
                }
                $scenario[$number]['state'] = 'done';

                // if there are more steps
                if ($len > $number + 2) {
                    $scenario[$number + 1]['state'] = 'inprogress';
                    $scenario[$number + 2]['state'] = 'next';
                }
                return;
            } else {
                if (
                    // isset($scenario[$number]['state']) &&
                    ($scenario[$number]['state'] === 'done' || $scenario[$number]['state'] === 'notdone')
                ) {
                    continue;
                } elseif (
                    // isset($scenario[$number]['state']) && 
                    $scenario[$number]['state'] === 'next'
                ) {
                    $scenario[$number]['state'] = 'notdone';
                    // $scenario[$number]['state'] = 'done';
                } else {
                    $scenario[$number]['state'] = 'notdone';
                }
            }
        } elseif (isset($state['transition'])) {
            $scenario[$number]['state'] = 'done';
        }
    }
}
