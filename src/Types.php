<?php

namespace App;

use App\Utils\PagedData;
use Yabx\TypeScriptBundle\Service\TypeScript;
use Yabx\TypeScriptBundle\Contracts\TypesInterface;

class Types implements TypesInterface {

    public static function registerTypes(TypeScript $ts): void {
        $ts->registerInterfacesFromDir(__DIR__ . '/Entity');
        $ts->registerInterfacesFromDir(__DIR__ . '/Model');
        $ts->registerInterfacesFromDir(__DIR__ . '/Enum');
        $ts->registerInterface(PagedData::class);
    }

    public static function codePostProcessor(string $code): string {
        // nothing to do
        return $code;
    }

}
