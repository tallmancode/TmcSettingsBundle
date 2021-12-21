<?php


namespace TallmanCode\SettingsBundle\Exception;

use Throwable;

class InvalidSettingsClassException extends \RuntimeException
{
    public function __construct($targetClass)
    {
        $message = 'Invalid tmc settings class argument';

        if(is_string($targetClass) || is_int($targetClass)){
            $message = sprintf('Invalid tmc settings class argument. "%s" ', $targetClass);
        }
        parent::__construct($message);
    }
}