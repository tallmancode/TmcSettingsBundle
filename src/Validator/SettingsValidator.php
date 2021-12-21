<?php


namespace TallmanCode\SettingsBundle\Validator;


use TallmanCode\SettingsBundle\Exception\UnknownSettingsException;
use TallmanCode\SettingsBundle\Exception\UnknownSettingsGroupException;

class SettingsValidator implements SettingsValidatorInterface
{
    private array $values;

    private array $config;

    public function __construct(array $values = [], array $config = [])
    {
        $this->values = $values;
        $this->config = $config;
    }

    public function validateGroup($group)
    {
        if(!is_string($group) || !array_key_exists($group, $this->config)){
            throw new UnknownSettingsGroupException($group);
        }

        $groupConfig = $this->config[$group];

        foreach($this->values as $key=>$value){
            if(!array_key_exists($key, $groupConfig)){
                throw new UnknownSettingsException($key, $group);
            }

            $this->validateSetting($value, $groupConfig[$key]);
        }
    }

    public function validateSetting($value, $groupConfig)
    {

    }
}