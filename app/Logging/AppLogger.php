<?php

declare(strict_types = 1);

namespace App\Logging;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AppLogger implements LoggerInterface
{
    //private const EMERGENCY = 'emergency';
    //private const ALERT     = 'alert';
    private const CRITICAL  = 'critical';
    //private const ERROR     = 'error';
    //private const WARNING   = 'warning';
    //private const NOTICE    = 'notice';
    //private const INFO      = 'info';
    //private const DEBUG     = 'debug';

    private Request $request;

    public function __construct()
    {
        $this->request = request();
    }


    public function emergency(string $message, array $context = [])
    {
        $this->put('emergency', $message, $context);
    }

    public function alert(string $message, array $context = [])
    {
        $this->put('alert', $message, $context);
    }

    public function critical(string $message, array $context = [])
    {
        $this->put(static::CRITICAL, $message, $context);
    }

    public function error(string $message, array $context = [])
    {
        $this->put('error', $message, $context);
    }
    public function warning(string $message, array $context = [])
    {
        $this->put('warning', $message, $context);
    }

    public function notice(string $message, array $context = [])
    {
        $this->put('notice', $message, $context);
    }

    public function info(string $message, array $context = [])
    {
        $this->put('info', $message, $context);
    }

    public function debug(string $message, array $context = [])
    {
        $this->put('debug', $message, $context);
    }

    private function put(string $level, string $message, array $context)
    {
        $context = array_merge([
            '_url' => $this->request->fullUrl(),
            '_method' => $this->request->method(),
            '_ip' => $this->request->ip(),
        ], $context);
        Log::channel($level)->error($message, $context);
    }
}
