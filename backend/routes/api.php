<?php

require __DIR__ . '/UsersRoutes.php';
require __DIR__ . '/AuthRoutes.php';

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

// Redis test route
Route::get('/redis-test', function () {
    try {
        Redis::set('test_key', 'Redis is working!');
        $value = Redis::get('test_key');
        Redis::del('test_key');

        return response()->json([
            'status' => 'success',
            'message' => 'Redis connection successful',
            'test_value' => $value
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Redis connection failed: ' . $e->getMessage()
        ], 500);
    }
});

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->ip());
});
