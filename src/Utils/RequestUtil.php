<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;

class RequestUtil {

    public static function getClientIp(Request $request): string {
        return $request->headers->get('cf-connecting-ip') ?: $request->get('__ip') ?: $request->getClientIp();
    }

    public static function getClientCountry(Request $request): string {
        $ip = self::getClientIp($request);
        if(str_starts_with($ip, '192.168.') || $ip == '::1') return 'BY';
        return $request->headers->get('cf-ipcountry') ?: $request->get('__country') ?: '';
    }

}
