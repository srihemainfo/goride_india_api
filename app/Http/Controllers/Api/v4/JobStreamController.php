<?php

namespace App\Http\Controllers\Api\v4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobStreamController extends Controller
{
    public function stream(Request $request, int $id): StreamedResponse
    {
        return response()->stream(function () use ($request, $id) {

            set_time_limit(0);
            ini_set('output_buffering', 'off');
            ini_set('zlib.output_compression', '0');
            ini_set('implicit_flush', '1');

            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');

            $lastId = $request->header('Last-Event-ID', '0-0');

            // Initial connect
            echo "event: connected\n";
            echo "data: connected\n\n";
            echo str_repeat(' ', 4096);
            flush();

            while (true) {

                if (connection_aborted()) {
                    break;
                }

                $response = Redis::xread(
                    ["job:{$id}:stream" => $lastId],
                    30,   // BLOCK seconds
                    1     // count
                );

                if (!$response) {
                    echo ": ping\n\n";
                    flush();
                    continue;
                }

                foreach ($response as $messages) {
                    foreach ($messages as $msgId => $msg) {

                        echo "id: {$msgId}\n";
                        echo "event: bid\n";
                        echo "data: {$msg['data']}\n\n";
                        flush();

                        $lastId = $msgId;
                    }
                }
            }

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}