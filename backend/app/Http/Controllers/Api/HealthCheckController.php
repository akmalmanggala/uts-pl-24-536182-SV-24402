<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HealthCheckController extends Controller
{
    /**
     * Inspect database and cache health status.
     * Returns HTTP 200 when all critical subsystems are operational,
     * or HTTP 503 if any subsystem is unreachable or degraded.
     */
    public function check(Request $request): JsonResponse
    {
        $isHealthy = true;
        $services = [];

        // 1. Database Connectivity Probe
        $dbStart = microtime(true);
        try {
            DB::connection()->getPdo();
            DB::select('SELECT 1 AS ping');
            $dbDuration = round((microtime(true) - $dbStart) * 1000, 2);

            $services['database'] = [
                'status'     => 'healthy',
                'connection' => config('database.default'),
                'latency_ms' => $dbDuration,
                'message'    => 'Database connection and ping query successful',
            ];
        } catch (\Throwable $e) {
            $isHealthy = false;
            $dbDuration = round((microtime(true) - $dbStart) * 1000, 2);

            $services['database'] = [
                'status'     => 'unhealthy',
                'connection' => config('database.default'),
                'latency_ms' => $dbDuration,
                'error'      => $e->getMessage(),
            ];

            // Log Structured Error: Database Failure (Konteks: Siapa, Sedang apa, Sebabnya)
            Log::error('Health check probe encountered database connectivity failure', [
                'actor' => [
                    'ip'         => $request->ip(),
                    'user_agent' => $request->userAgent() ?? 'Docker-Healthcheck/1.0',
                    'auth_id'    => $request->user()?->id ?? 'system_probe',
                ],
                'action' => 'database_health_probe',
                'reason' => 'Unable to establish PDO connection to MySQL server or query timed out',
                'error'  => [
                    'exception' => get_class($e),
                    'message'   => $e->getMessage(),
                ],
            ]);
        }

        // 2. Cache Subsystem Probe
        $cacheStart = microtime(true);
        try {
            $probeKey = '_health_probe_' . uniqid();
            Cache::put($probeKey, 'operational', 10);
            $cacheVal = Cache::get($probeKey);
            Cache::forget($probeKey);

            if ($cacheVal !== 'operational') {
                throw new \RuntimeException('Cache write-read roundtrip value mismatch');
            }

            $cacheDuration = round((microtime(true) - $cacheStart) * 1000, 2);

            $services['cache'] = [
                'status'     => 'healthy',
                'driver'     => config('cache.default'),
                'latency_ms' => $cacheDuration,
                'message'    => 'Cache read/write test passed',
            ];
        } catch (\Throwable $e) {
            $isHealthy = false;
            $cacheDuration = round((microtime(true) - $cacheStart) * 1000, 2);

            $services['cache'] = [
                'status'     => 'unhealthy',
                'driver'     => config('cache.default'),
                'latency_ms' => $cacheDuration,
                'error'      => $e->getMessage(),
            ];

            // Log Structured Error: Cache Failure
            Log::error('Health check probe encountered cache subsystem failure', [
                'actor' => [
                    'ip'         => $request->ip(),
                    'user_agent' => $request->userAgent() ?? 'Docker-Healthcheck/1.0',
                    'auth_id'    => $request->user()?->id ?? 'system_probe',
                ],
                'action' => 'cache_health_probe',
                'reason' => 'Failed writing or retrieving probe token from cache store',
                'error'  => [
                    'exception' => get_class($e),
                    'message'   => $e->getMessage(),
                ],
            ]);
        }

        $httpCode = $isHealthy ? 200 : 503;

        return response()->json([
            'status'    => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'app_env'   => config('app.env'),
            'services'  => $services,
        ], $httpCode);
    }
}
