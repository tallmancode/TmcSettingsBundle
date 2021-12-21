<?php


namespace TallmanCode\SettingsBundle\Exception;


use Throwable;

class UnknownSettingsGroupException extends \RuntimeException
{
    public function __construct($group)
    {
        parent::__construct(sprintf('Unknown setting "%s"', $group));
    }
}