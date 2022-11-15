<?php

namespace App\Utils;

class Password {

    public const CHARS_DIGITS = '0123456789';
    public const CHARS_LETTERS = self::CHARS_LETTERS_LC . self::CHARS_LETTERS_UC;
    public const CHARS_LETTERS_LC = 'abcdefghijklmnopqrstuvwxyz';
    public const CHARS_LETTERS_UC = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public const CHARS_SPECIAL = '`-=~!@#$%^&*()_+,./<>?;:[]{}\|';

    public static function generate(int $length = 16, int $difficulty = 2): string {
        $chars = self::CHARS_DIGITS;
        if($difficulty >= 2) {
            $chars .= self::CHARS_LETTERS;
        }
        if($difficulty >= 3) {
            $chars .= self::CHARS_SPECIAL;
        }
        $password = '';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++) {
            $password .= $chars[ random_int(0, $max) ];
        }
        return $password;
    }

    public static function fromChars(int $length = 16, string $chars = self::CHARS_LETTERS): string {
        $password = '';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++) {
            $password .= $chars[ random_int(0, $max) ];
        }
        return $password;
    }

}
