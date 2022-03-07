<?php

namespace TallmanCode\SettingsBundle\Manager;

class Configuration
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function get()
    {
        return $this->config;
    }

    public function getGroupDefaults(String $group, $overRides = null)
    {
        if(isset($this->config[$group]['defaults'])) {
            $defaults = $this->config[$group]['defaults'];

            if($overRides){
                //TODO validate defaults structure
                $defaults = array_merge($defaults, $overRides);
            }

            return $defaults;
        }

        if(!isset($this->config[$group]['defaults']) && $overRides){
            return $overRides;
        }

        return null;
    }

}