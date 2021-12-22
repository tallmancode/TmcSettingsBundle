<?php

namespace TallmanCode\SettingsBundle;

use Doctrine\Common\Annotations\Reader;
use TallmanCode\SettingsBundle\Annotation\TmcSettingsOwner;
use TallmanCode\SettingsBundle\Annotation\TmcSettingsResource;
use TallmanCode\SettingsBundle\Exception\InvalidSettingsClassException;
use TallmanCode\SettingsBundle\Exception\SettingsOwnerException;
use TallmanCode\SettingsBundle\Manager\SettingsManagerInterface;

class SettingsHelper
{
    private SettingsManagerInterface $settingsManager;
    private Reader $reader;

    public function __construct(SettingsManagerInterface $settingsManager, Reader $reader)
    {
        $this->settingsManager = $settingsManager;
        $this->reader = $reader;
    }

    /*
     * retrieve settings from a AbstractTmcSettings class
     */
    public function getClassSettings($targetClass, $relationClass = null, $relationId = null)
    {
        $annotation = $this->reader->getClassAnnotation(new \ReflectionClass($targetClass), TmcSettingsResource::class);

        if(null !== $annotation){
            $settingsGroup = $annotation->getSettingsGroup();
            if(is_string($settingsGroup)){
                $data = $this->settingsManager->getSettings($targetClass, null, $settingsGroup);
            }else{
                throw new SettingsOwnerException(get_class($targetClass));
            }
            return $data;
        }

        throw new InvalidSettingsClassException($targetClass);
    }

    /**
     * @throws \ReflectionException
     * @throws \JsonException
     */
    public function saveSettings($targetClass)
    {
        $annotation = $this->reader->getClassAnnotation(new \ReflectionClass($targetClass), TmcSettingsResource::class);

        if(null !== $annotation){
            return $this->settingsManager->save(
                $targetClass,
                $annotation->getRelationClass(),
                $annotation->getSettingsGroup()
            );
        }

        throw new InvalidSettingsClassException($targetClass);

    }
}