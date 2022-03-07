<?php

namespace TallmanCode\SettingsBundle\Util;

use Symfony\Component\String\Slugger\AsciiSlugger;

class SluggerGroup
{
    public function generateGroup(String $targetClass) :String
    {
        $pos = strrpos($targetClass, '/');
        $className = $pos === false ? $targetClass : substr($targetClass, $pos + 1);
        $slugger = new AsciiSlugger();
        return $slugger->slug($className);
    }
}