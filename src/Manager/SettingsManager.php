<?php


namespace TallmanCode\SettingsBundle\Manager;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
use ReflectionClass;
use ReflectionException;
use TallmanCode\SettingsBundle\Annotation\TmcSettingsGroup;
use TallmanCode\SettingsBundle\Entity\DynamicSetting;
use TallmanCode\SettingsBundle\Entity\SettingsOwnerInterface;
use TallmanCode\SettingsBundle\Exception\InvalidSettingsAccessorException;
use TallmanCode\SettingsBundle\Exception\InvalidSettingsClassException;
use TallmanCode\SettingsBundle\Validator\SettingsValidator;
use TallmanCode\SettingsBundle\Validator\SettingsValidatorInterface;

/**
 * @author Steve "Tallman" Stewart <steve@tallmancode.co.za>
 */
class SettingsManager implements SettingsManagerInterface
{
    private array $defaults_config;

    private EntityManagerInterface $manager;

    private SettingsValidator $settingsValidator;

    private $repo;

    private Reader $reader;

    public function __construct(
        EntityManagerInterface     $manager,
        SettingsValidatorInterface $settingsValidator,
        Reader $reader,
        array                      $defaults_config = []
    )
    {
        $this->manager = $manager;
        $this->defaults_config = $defaults_config;
        $this->settingsValidator = $settingsValidator;
        $this->repo = $this->manager->getRepository(DynamicSetting::class);
        $this->reader = $reader;
    }

    /**
     * @throws ReflectionException
     * @throws JsonException
     */
    public function getCollection(SettingsOwnerInterface $owner, $settingsProperties): SettingsOwnerInterface
    {
        $reflectionClass = new ReflectionClass($owner);

        foreach($settingsProperties as $settingProperty){
            $reflectionProperty = $reflectionClass->getProperty($settingProperty);
            $propertyAnnotation = $this->reader->getPropertyAnnotation($reflectionProperty, TmcSettingsGroup::class);
            $group = $propertyAnnotation->getGroup();
            $targetClass = $propertyAnnotation->getTargetClass();
            $settings = $this->getSettings($targetClass, $owner, $group);
            $setterName = 'set'.ucfirst($reflectionProperty->getName());
            $owner->$setterName($settings);
        }

        return $owner;
    }


    /**
     * @throws ReflectionException
     * @throws JsonException
     */
    public function get(SettingsOwnerInterface $owner, $settingProperty): SettingsOwnerInterface
    {
        $reflectionClass = new ReflectionClass($owner);
        $reflectionProperty = $reflectionClass->getProperty($settingProperty);
        $propertyAnnotation = $this->reader->getPropertyAnnotation($reflectionProperty, TmcSettingsGroup::class);
        $group = $propertyAnnotation->getGroup();
        $targetClass = $propertyAnnotation->getTargetClass();
        $settings = $this->getSettings($targetClass, $owner, $group);
        $setterName = 'set'.ucfirst($reflectionProperty->getName());
        $owner->$setterName($settings);
        return $owner;
    }

    /**
     * @throws JsonException
     */
    public function getSettings($targetClass, SettingsOwnerInterface $owner = null , $group = null)
    {
        $validator = new $this->settingsValidator([], $this->defaults_config);
        $validator->validateGroup($group);

        $settingsClass = $this->initTargetClass($targetClass);
        $persistedSettings = $this->getSettingsFromRepo($owner, $group);

        if($persistedSettings){
            $settingsClass = $this->mapProperties($settingsClass, json_decode($persistedSettings->getPayload(), true, 512, JSON_THROW_ON_ERROR), $group);

            if(is_string($persistedSettings->getUuid())){
                $settingsClass->setUuid($persistedSettings->getUuid());
            }

        }

        return $this->mapDefaults($settingsClass, $group);
    }


    public function mapProperties($settingsClass, $persistedSettings, $group)
    {
        foreach ($persistedSettings as $name => $setting) {
            $setterName = 'set' . ucfirst($name);
            if (method_exists($settingsClass, $setterName)) {
                $settingsClass->$setterName($setting);
            }
        }
        return $settingsClass;
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function save($data, $relationClass, $group)
    {
        $this->validateTargetClass($data);

        $persistedSettings = $this->getPersistedSettings($relationClass, $data->getRelationId(), $group, $data->getUuid());

        if (!$persistedSettings) {
            $persistedSettings = $this->setupDynamicSettings($relationClass, $data, $group);
        }

        $data->setUuid($persistedSettings->getUuid());

        $existingPayload = [];
        if($persistedSettings->getPayload()){
            $existingPayload = json_decode($persistedSettings->getPayload(), true, 512, JSON_THROW_ON_ERROR);
        }


        $mergeResponse = $this->mergeProperties(
            $data,
            $group,
            $existingPayload
        );

        $persistedSettings->setPayload($mergeResponse['payload']);
        $persistedSettings->setUpdatedAt(new \DateTime());
        $this->manager->persist($persistedSettings);
        $this->manager->flush();

        return $mergeResponse['data'];
    }

    private function validateTargetClass($targetClass)
    {
        if(!is_object($targetClass)){
            throw new InvalidSettingsClassException($targetClass);
        }
    }

    private function getPersistedSettings($relationClass, $relationId, $group, $uuid = null)
    {
        $findValues = [
            'relationClass' => $relationClass,
            'relationId' => $relationId,
            'groupName' => $group
        ];

        if($uuid){
            $findValues['uuid'] = $uuid;
        }

        return $this->repo->findOneBy($findValues);
    }

    /**
     * @throws Exception
     */
    private function setupDynamicSettings($relationClass, $data, $group): DynamicSetting
    {
        $persistedSettings = new DynamicSetting();
        $persistedSettings->setUuid($this->generateUuid());
        $persistedSettings->setRelationClass($relationClass);
        $persistedSettings->setRelationId($data->getRelationId());
        $persistedSettings->setGroupName($group);
        $persistedSettings->setName($group);
        $persistedSettings->setCreatedAt(new \DateTime());
        return $persistedSettings;
    }

    /**
     * @throws Exception
     */
    private function generateUuid(): string
    {
       $prefix =  bin2hex(random_bytes(4));
        return uniqid($prefix, false);
    }

    /**
     * @throws Exception
     */
    private function initTargetClass($targetClass)
    {
        if (is_string($targetClass)) {
            $targetClass = new $targetClass;
        }

//        if(!is_object($targetClass)){
//            throw new InvalidSettingsClassException($targetClass);
//        }

        if(!$targetClass->getUuid()){
            $UUID = $this->generateUuid();
            $targetClass->setUuid($UUID);
        }

        return $targetClass;
    }

    private function mapDefaults($settingsClass, $group)
    {
        $reflectionClass = new ReflectionClass($settingsClass);
        $defaults = $this->getDefaults($group);
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            if (!in_array($reflectionProperty->getName(), ['uuid', 'relationId'])) {
                $getterName = 'get' . ucfirst($reflectionProperty->getName());
                if (method_exists($settingsClass, $getterName)) {
                    $propertyValue = $settingsClass->$getterName();
                    if ($propertyValue === null) {
                        $setterName = 'set' . ucfirst($reflectionProperty->getName());
                        $settingsClass->$setterName($defaults[$reflectionProperty->getName()]['default']);
                    }
                }
            }
        }

        return $settingsClass;
    }

    /**
     * @throws JsonException
     */
    private function mergeProperties($data, $group, $existingPayload = null): array
    {
        $reflectionClass = new ReflectionClass($data);
        $reflectionProperties = $reflectionClass->getProperties();
        $payloadArray = [];
        $defaults = $this->getDefaults($group);

        foreach ($reflectionProperties as $reflectionProperty) {
            $propertyKey = $reflectionProperty->getName();
            $getterName = $this->accessorName($propertyKey, 'getter');
            $setterName = $this->accessorName($propertyKey, 'setter');
            $propertyValue = $data->$getterName();

            if($propertyValue === null){
                if(array_key_exists($propertyKey, $existingPayload)){
                    $data->$setterName($existingPayload[$propertyKey]);
                    $payloadArray[$propertyKey] = $propertyValue;
                } elseif(array_key_exists($propertyKey, $defaults) ) {
                    //validation could happen here
                    if(isset($defaults[$propertyKey]['default'])){
                        $data->$setterName($defaults[$propertyKey]['default']);
                    }
                }
                continue;
            }

            if(array_key_exists($propertyKey, $defaults) && $propertyValue !== $defaults[$propertyKey]['default']){
                $payloadArray[$propertyKey] = $propertyValue;
            }
        }

        return [
            'data' => $data,
            'payload' => json_encode($payloadArray, JSON_THROW_ON_ERROR)
        ];
    }

    private function getDefaults($group)
    {
        return $this->defaults_config[$group];
    }

    private function accessorName($propertyName, $accessorType): string
    {
        if($accessorType !== 'getter' && $accessorType !== 'setter'){
            throw new InvalidSettingsAccessorException();
        }
        $prefix = '';
        switch($accessorType){
            case 'getter':
                $prefix = 'get';
                break;
            case 'setter':
                $prefix = 'set';
                break;
        }

        return $prefix.''.ucfirst($propertyName);
    }

    /**
     * @throws JsonException
     */
    private function getSettingsFromRepo(SettingsOwnerInterface $owner = null, $group = null)
    {
        $relationId = null;
        $relationClass = null;

        if($owner){
            $relationClass = get_class($owner);
            $relationId = $owner->getIdentifier();
        }

        if ($group !== null) {
            $dynamicSetting = $this->repo->findOneBy(['relationClass' => $relationClass, 'relationId' => $relationId]);
        } else {
            $dynamicSetting = $this->repo->findOneBy(['relationClass' => $relationClass, 'relationId' => $relationId, 'groupName' => $group]);
        }

        return $dynamicSetting;
    }

}