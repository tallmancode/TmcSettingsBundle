<?php


namespace TallmanCode\SettingsBundle\Exception;


use Throwable;

class SettingsOwnerException extends \RuntimeException
{
    public function __construct($className)
    {
        parent::__construct(sprintf('Unknown target resources set for "%s" valid options are string or array', $className));
    }
}