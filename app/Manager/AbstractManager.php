<?php

namespace App\Manager;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class AbstractManager {

    protected $cacheTime = 3600;
    protected $prefix;

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    protected function setCache(string $key, $value): void
    {
        try {
            $serialized = serialize($value);
            Redis::setex($this->prefix . $key, $this->cacheTime, $serialized);
        } catch (\Exception $e) {
            Log::error('Redis set error: ' . $e->getMessage());
        }
    }

    protected function getCache(string $key)
    {
        try {
            $value = Redis::get($this->prefix . $key);
            return $value ? unserialize($value) : null;
        } catch (\Exception $e) {
            Log::error('Redis get error: ' . $e->getMessage());
            return null;
        }
    }

    protected function deleteCache(string $key): void
    {
        try {
            Redis::del($this->prefix . $key);
        } catch (\Exception $e) {
            Log::error('Redis delete error: ' . $e->getMessage());
        }
    }

    protected function clearCache(): void
    {
        try {
            $pattern = $this->prefix . '*';
            $iterator = null;
            $keys = [];

            do {
                $keys = Redis::scan($iterator, [
                    'match' => $pattern,
                    'count' => 100
                ]);

                if ($keys) {
                    foreach ($keys as $key) {
                        Redis::del($key);
                    }
                }
            } while ($iterator > 0);

        } catch (\Exception $e) {
            Log::error('Redis clear error: ' . $e->getMessage());
        }
    }

}
