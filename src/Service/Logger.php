<?php

namespace App\Service;

use DateTime;
use Stringable;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface {

    protected static array $loggers = [];
    protected string $name;
    protected string $dir;
    protected string $path;
    protected string $prefix;

    public function __construct(string $path, string $prefix = '') {
        $this->path = $path;
        $this->prefix = $prefix;
    }

    public static function get(string $name, string $prefix = ''): LoggerInterface {
        if(key_exists($name, self::$loggers)) {
            return self::$loggers[ $name . $prefix ];
        }
        $dir = __DIR__ . '/../../var/log/' . $name;
        if(!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $path = $dir . '/' . date('Y-m-d') . '.log';
        return self::$loggers[ $name . $prefix ] = new self($path, $prefix);
    }

    public static function clean(int $days = 10): int {
        $dir = realpath(__DIR__ . '/../../var/log');
        if(!$dir) {
            return 0;
        }
        exec("find {$dir} -type f -mtime +{$days}", $files);
        $space = 0;
        foreach($files as $file) {
            $space += filesize($file);
        }
        exec("find {$dir} -type f -mtime +{$days} -delete");
        // exec("find {$dir} -type d -empty -delete");
        return $space;
    }

    public function emergency(string|Stringable $message, array $context = []): void {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function log(mixed $level, string|Stringable $message, array $context = []): void {
        $f = fopen($this->path, 'a');
        fwrite($f, (new DateTime())->format('[H:i:s.u]') . ' ' . strtoupper($level) . ': ' . $this->prefix . $message . (!empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_LINE_TERMINATORS | JSON_UNESCAPED_SLASHES) : '') . PHP_EOL);
        fclose($f);
    }

    public function alert(string|Stringable $message, array $context = []): void {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical(string|Stringable $message, array $context = []): void {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error(string|Stringable $message, array $context = []): void {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning(string|Stringable $message, array $context = []): void {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice(string|Stringable $message, array $context = []): void {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info(string|Stringable $message, array $context = []): void {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug(string|Stringable $message, array $context = []): void {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

}
