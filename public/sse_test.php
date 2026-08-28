<?php

// ---- IMPORTANT HEADERS ----
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');

// ---- PHP RUNTIME ----
set_time_limit(0);
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', 0);
ini_set('implicit_flush', 1);

while (ob_get_level() > 0) {
    ob_end_flush();
}

// ---- INITIAL EVENT ----
echo "event: connected\n";
echo "data: " . json_encode([
    'status' => 'connected',
    'time' => date('H:i:s')
]) . "\n\n";
flush();

$counter = 1;
$lastHeartbeat = time();

// ---- STREAM LOOP ----
while (true) {

    // Stop if browser closes connection
    if (connection_aborted()) {
        break;
    }

    // Send event every 3 seconds
    echo "event: test_event\n";
    echo "data: " . json_encode([
        'message' => 'Hello from SSE',
        'count' => $counter++,
        'time' => date('H:i:s')
    ]) . "\n\n";
    flush();

    // Heartbeat every 15 seconds
    if (time() - $lastHeartbeat >= 15) {
        echo ": heartbeat\n\n";
        flush();
        $lastHeartbeat = time();
    }

    sleep(3);
}