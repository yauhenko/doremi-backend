<?php

namespace App\Utils;

use RuntimeException;

class Env {

    protected static bool $isLoaded = false;
    protected static array $values = [];

    public static function getBool(string $key): bool {
        return (bool)self::get($key);
    }

    public static function get(string $key): ?string {
        if(!self::$isLoaded) {
            self::load();
        }
        return self::$values[ $key ] ?? null;
    }

    protected static function load(): void {
        if($path = realpath(__DIR__ . '/../../.env.local.php')) {
            $env = include $path;
            foreach($env as $key => $value) {
                self::$values[ $key ] = $value;
            }
        } elseif($path = realpath(__DIR__ . '/../../.env')) {
            $env = trim(file_get_contents($path));
            foreach(explode("\n", $env) as $line) {
                $line = trim($line);
                if($line === '' || str_starts_with($line, '#')) {
                    continue;
                }
                [$key, $value] = explode('=', trim($line ?? ''), 2);
                if(str_starts_with($value, '\'') && str_ends_with($value, '\'')) {
                    $value = substr($value, 1, -1);
                }
                self::$values[ $key ] = $value;
            }
        } else {
            throw new RuntimeException('Failed to load env vars');
        }
        self::$isLoaded = true;
    }

    public static function getInt(string $key): int {
        return (int)self::get($key);
    }

    public static function getArray(string $key, string $separator = ','): array {
        $values = explode($separator, self::get($key));
        return array_map('trim', $values);
    }

}
