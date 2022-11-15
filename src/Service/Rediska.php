<?php

namespace App\Service;

use Redis;

class Rediska {

    private Redis $redis;

    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect('localhost');
    }

    public function get(string $key): mixed {
        return unserialize($this->redis->get($key));
    }

    public function set(string $key, mixed $value, ?int $ttl = null): bool {
        return $this->redis->set($key, serialize($value), $ttl);
    }

    public function del(string|array $key): int {
        return $this->redis->del($key);
    }

    public function keys(string $pattern): array {
        return $this->redis->keys($pattern);
    }

}
