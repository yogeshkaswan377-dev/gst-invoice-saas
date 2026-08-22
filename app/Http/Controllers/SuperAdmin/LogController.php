<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');

        $logs = collect();
        if (File::exists($logFile)) {
            $lines = File::lines($logFile)->reverse()->take(200)->toArray();

            foreach ($lines as $line) {
                $parsed = $this->parseLogLine($line);
                if ($parsed) {
                    $logs->push($parsed);
                }
            }
        }

        // Optional level filter
        if ($request->filled('level') && $request->level !== '') {
            $logs = $logs->where('level', strtoupper($request->level));
        }

        return view('super-admin.logs.index', ['logs' => $logs->values()]);
    }

    /**
     * Parse a Laravel log line into structured data.
     */
    private function parseLogLine(string $line): ?array
    {
        // Format: [2026-08-22 12:34:56] local.ERROR: Message {"context":...} in file:line
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*)$/';

        if (preg_match($pattern, $line, $matches)) {
            $timestamp = $matches[1];
            $level = strtoupper($matches[3]);
            $message = $matches[4];

            // Extract context if JSON present after message (optional)
            $context = '';
            if (preg_match('/^(.+?)\s*(\{.*\}|\[.*\])$/s', $message, $msgParts)) {
                $message = $msgParts[1];
                $context = $msgParts[2];
            }

            return [
                'timestamp' => $timestamp,
                'level'     => $level,
                'message'   => $message,
                'context'   => $context,
            ];
        }

        // Fallback: just return the raw line
        return [
            'timestamp' => '',
            'level'     => 'INFO',
            'message'   => $line,
            'context'   => '',
        ];
    }
}
