<?php


namespace TallmanCode\SettingsBundle\Exception;

use Throwable;

class UnknownSettingsException extends \RuntimeException
{
    public function __construct($group, $setting)
    {
        parent::__construct(sprintf('Unknown setting "%s" in settings group "%s"', $setting, $group));
    }
}