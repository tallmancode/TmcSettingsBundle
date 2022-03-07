<?php

namespace TallmanCode\SettingsBundle\Util;

use Exception;

class Uuid
{
    /**
     * @throws Exception
     */
    public function generate(): string
    {
        $prefix = bin2hex(random_bytes(4));
        return uniqid($prefix, false);
    }
}