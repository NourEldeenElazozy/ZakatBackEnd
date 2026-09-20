<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PerformanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $queryCount = 0;

        // الاستماع لكل استعلام يتم تنفيذه لزيادة العداد
        DB::listen(function ($query) use (&$queryCount) {
            $queryCount++;
        });

        $response = $next($request);

        $durationMs = (microtime(true) - $startTime) * 1000;

        // تسجيل الطلبات التي تستغرق أكثر من 500 ميلي ثانية
        if ($durationMs > 500) {
            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/performance.log'),
            ])->warning('API Bottleneck Detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'duration_ms' => round($durationMs, 2),
                'query_count' => $queryCount,
                'ip' => $request->ip(),
            ]);
        }

        // إضافة ترويسة لزمن الاستجابة في البيئة التطويرية للفرونت اند
        $response->headers->set('X-Response-Time-Ms', round($durationMs, 2));

        return $response;
    }
}
