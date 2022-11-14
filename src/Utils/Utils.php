<?php

namespace App\Utils;

class Utils {

    public static function numpad(int $value, int $pad = 4): string {
        return sprintf('%0' . $pad . 'd', $value);
    }

    public static function formatTimeFull(int $s): string {
        $h = $m = 0;
        if($s >= 3600) {
            $h = floor($s / 3600);
            $s -= $h * 3600;
        }
        if($s >= 60) {
            $m = floor($s / 60);
            $s -= $m * 60;
        }
        if($s < 10) $s = "0{$s}";
        if($m < 10) $m = "0{$m}";
        return "{$h}:{$m}:{$s}.000";
    }

    public static function formatTimeShort(int $s): string {
        $m = 0;
        if($s >= 60) {
            $m = floor($s / 60);
            $s -= $m * 60;
        }
        if($s < 10) $s = "0{$s}";
        if($m < 10) $m = "0{$m}";
        return "{$m}:{$s}";
    }

}
