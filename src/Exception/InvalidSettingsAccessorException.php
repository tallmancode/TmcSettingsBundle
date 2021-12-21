<?php


namespace TallmanCode\SettingsBundle\Exception;

use Throwable;

class InvalidSettingsAccessorException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invalid tmc settings accessor. Valid option are getter or setter');
    }
}