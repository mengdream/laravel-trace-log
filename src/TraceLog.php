<?php

namespace Forecho\LaravelTraceLog;


use Illuminate\Support\Str;

/**
 * Class Log
 * @method static void debug(string $message, array $context = [], array $request = [], array $response = [])
 * @method static void info(string $message, array $context = [], array $request = [], array $response = [])
 * @method static void warning(string $message, array $context = [], array $request = [], array $response = [])
 * @method static void error(string $message, array $context = [], array $request = [], array $response = [])
 */
class TraceLog
{
    public function __call(string $name, array $arguments)
    {
        static::writeLog($name, $arguments);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        static::writeLog($name, $arguments);
    }

    protected static function writeLog(string $name, array $arguments)
    {
        $argOffset = 0;
        $message = config('app.name');
        $logData['trace_id'] = static::getTraceId();
        $logData['message'] = data_get($arguments, (string)(0 + $argOffset), '');
        $logData['context'] = data_get($arguments, (string)(1 + $argOffset), []);

        $requestData = data_get($arguments, (string)(2 + $argOffset), []);
        if (!empty($requestData)) {
            $logData['request'] = $requestData;
        }

        $responseData = data_get($arguments, (string)(3 + $argOffset), []);
        if (!empty($responseData)) {
            $logData['response'] = $responseData;
        }

        \Illuminate\Support\Facades\Log::$name($message, $logData);
    }

    protected static function getTraceId(): string
    {
        if (!$traceId = config('tracelog.id')) {
            $traceId = Str::uuid();
            config(['tracelog.id' => $traceId]);
        }

        return $traceId;
    }
}