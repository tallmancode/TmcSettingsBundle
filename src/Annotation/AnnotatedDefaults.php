<?php

namespace TallmanCode\SettingsBundle\Annotation;

class AnnotatedDefaults
{

    public function get($annotation, $groupName)
    {
        $defaults = $annotation->getDefaults();

        if($defaults && $defaults[$groupName]){
            return $defaults[$groupName];
        }

        return null;
    }
}