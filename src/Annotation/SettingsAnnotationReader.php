<?php

namespace TallmanCode\SettingsBundle\Annotation;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\String\Slugger\AsciiSlugger;
use TallmanCode\SettingsBundle\Util\SluggerGroup;

class SettingsAnnotationReader implements SettingsAnnotationReaderInterface
{
    private Reader $annotationReader;

    /**
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }


    public function getAnnotationsForClass($settingClass)
    {
        $reflectionClass = new \ReflectionClass($settingClass);
        $tmcSettingsResourceAnnotation =  $this->annotationReader->getClassAnnotation($reflectionClass, TmcSettingsResource::class);
        if($tmcSettingsResourceAnnotation) {
            return $tmcSettingsResourceAnnotation;
        }
        $tmcSettingsOwnerAnnotation = $this->annotationReader->getClassAnnotation($reflectionClass, TmcSettingsOwner::class);
        if($tmcSettingsOwnerAnnotation){
            return $tmcSettingsOwnerAnnotation;
        }

        return null;
    }


    /*
     * returns the group set in the annotation of a settings class or null
     */
    public function getAnnotationGroup($settingClass= null,TmcSettingsResource $annotation = null): ?string
    {
        if(!$annotation){
            $annotation = $this->getAnnotationsForClass($settingClass);
            if(!$annotation){
                return null;
            }
        }

        if(!$group = $annotation->getSettingsGroup()){
            $sluggerGroup = new SluggerGroup();
            return $sluggerGroup->generateGroup($settingClass);
        }

        return $group;
    }

    public function getPropertyAnnotation($propertyName, $settingsOwner)
    {
        $reflectionClass = new ReflectionClass($settingsOwner);
        $reflectionProperty = $reflectionClass->getProperty($propertyName);
        return $this->annotationReader->getPropertyAnnotation($reflectionProperty, TmcSettingsGroup::class);
    }
}