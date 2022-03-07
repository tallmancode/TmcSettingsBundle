<?php

namespace TallmanCode\SettingsBundle\Factory;

use Exception;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use ReflectionClass;
use ReflectionException;
use TallmanCode\SettingsBundle\Entity\SettingsBundleSetting;
use TallmanCode\SettingsBundle\Manager\Configuration;
use TallmanCode\SettingsBundle\Util\Uuid;

class SettingsClassFactory
{
    /**
     * @throws Exception
     * @throws Exception
     */
    public function build($settingClass)
    {
        if (is_string($settingClass)) {
            $settingClass = new $settingClass();
        }

        if (!$settingClass->getUuid()) {
            $UUID = new Uuid();
            $settingClass->setUuid($UUID->generate());
        }
        return $settingClass;
    }

    /**
     * @throws ReflectionException|JsonException|\JsonException
     */
    public function setProperties($settingsClass, Configuration $config, ?SettingsBundleSetting $persistedSettings, $group, $annotation)
    {
        if($persistedSettings){
            $settingsPayload = json_decode($persistedSettings->getPayload(), true, 512, JSON_THROW_ON_ERROR);
            if(method_exists($settingsClass, "setUuid") && $uuid = $persistedSettings->getUuid()){
                $settingsClass->setUuid($uuid);
            }

            if(method_exists($settingsClass, "setRelationId") && $relationId = $persistedSettings->getRelationId()){
                $settingsClass->setRelationId($relationId);
            }
        }

        $annotatedDefaults = $annotation->getDefaults();
        $configDefaults = $config->getGroupDefaults($group, $annotatedDefaults);
        $reflectionClass = new ReflectionClass($settingsClass);
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if (!in_array($reflectionProperty->getName(), ['uuid', 'relationId'])) {
                $getterName = 'get' . ucfirst($reflectionProperty->getName());
                if (method_exists($settingsClass, $getterName)) {
                    $persistedValue = $settingsPayload[$reflectionProperty->getName()] ?? null;
                    $setterName = 'set' . ucfirst($reflectionProperty->getName());
                    if($persistedValue){
                        $settingsClass->$setterName($persistedValue);
                    }else{
                        $defaultValue = null;
                        if($configDefaults && isset($configDefaults[$reflectionProperty->getName()]['value'])){
                            $defaultValue = $configDefaults[$reflectionProperty->getName()]['value'];
                        }
                        $settingsClass->$setterName($defaultValue);
                    }
                }
            }
        }

        return $settingsClass;
    }
}