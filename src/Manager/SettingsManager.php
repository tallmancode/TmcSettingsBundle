<?php


namespace TallmanCode\SettingsBundle\Manager;

use DateTime;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
use ReflectionClass;
use ReflectionException;
use TallmanCode\SettingsBundle\Annotation\SettingsAnnotationReaderInterface;
use TallmanCode\SettingsBundle\Annotation\TmcSettingsGroup;
use TallmanCode\SettingsBundle\Entity\SettingsBundleSetting;
use TallmanCode\SettingsBundle\Entity\SettingsInterface;
use TallmanCode\SettingsBundle\Entity\TmcSettingsInterface;
use TallmanCode\SettingsBundle\Exception\InvalidSettingsAccessorException;
use TallmanCode\SettingsBundle\Exception\InvalidSettingsClassException;
use TallmanCode\SettingsBundle\Factory\SettingsClassFactory;
use TallmanCode\SettingsBundle\Util\Uuid;
use TallmanCode\SettingsBundle\Validator\SettingsValidator;
use TallmanCode\SettingsBundle\Validator\SettingsValidatorInterface;

/**
 * @author Steve "Tallman" Stewart <steve@tallmancode.co.za>
 */
class SettingsManager implements SettingsManagerInterface
{
    private Configuration $config;

    private EntityManagerInterface $manager;

    private SettingsValidator $settingsValidator;

    private $repo;

    private Reader $reader;
    private SettingsAnnotationReaderInterface $annotationReader;
    private PersistedSettingsInterface $persistedSettings;

    public function __construct(
        Configuration                     $configuration,
        PersistedSettingsInterface        $persistedSettings,
        EntityManagerInterface            $manager,
        Reader                            $reader,
        SettingsAnnotationReaderInterface $annotationReader
    )
    {

        $this->config = $configuration;
        $this->manager = $manager;
        $this->repo = $this->manager->getRepository(SettingsBundleSetting::class);
        $this->reader = $reader;
        $this->annotationReader = $annotationReader;
        $this->persistedSettings = $persistedSettings;
    }


    /**
     * @throws JsonException
     * @throws Exception
     */
    public function find($settingClass, $relationClass = null, $annotation = null, $group = null)
    {
        if (null === $annotation) {
            $annotation = $this->annotationReader->getAnnotationsForClass($settingClass);
        }

        if (null === $group) {
            $group = $this->annotationReader->getAnnotationGroup(null, $annotation);
        }

        $factory = new SettingsClassFactory();
        $settingClass = $factory->build($settingClass);
        $persistedSettings = $this->persistedSettings->get($relationClass, $group);
        return $factory->setProperties($settingClass, $this->config, $persistedSettings, $group, $annotation);
    }

    public function populateRelations(SettingsInterface $settingsOwner)
    {
        $annotation = $this->annotationReader->getAnnotationsForClass($settingsOwner);

        if ($annotation && $targetResources = $annotation->getTargetResources()) {
            foreach ($targetResources as $targetResource) {
                $setter = $this->accessorName($targetResource, 'setter');
                $anno = $this->annotationReader->getPropertyAnnotation($targetResource, $settingsOwner);
                $test = $this->find($anno->getTargetClass(), $settingsOwner, null, $anno->getGroup());
                $settingsOwner->$setter($test);
            }
        }
    }


    /**
     * @throws JsonException
     * @throws Exception
     */
    public function persist(TmcSettingsInterface $settingEntity, $relationClass = null, $annotation = null)
    {
        $group = null;
        if (null === $annotation) {
            $annotation = $this->annotationReader->getAnnotationsForClass($settingEntity);
            $group = $this->annotationReader->getAnnotationGroup(null, $annotation);
        } else {
            $group = $annotation->getSettingsGroup();
        }

        $persistedSettings = $this->persistedSettings->get($relationClass, $group);
        $payload = $persistedSettings ? $this->persistedSettings->getPayload($persistedSettings) : null;
        $annotatedDefaults = $annotation->getDefaults();
        $configDefaults = $this->config->getGroupDefaults($group, $annotatedDefaults);

        $mergeResponse = $this->mergeProperties(
            $settingEntity,
            $configDefaults,
            $payload,
        );
        if (!$persistedSettings) {
            $persistedSettings = $this->setupSettingsBundleSettings($relationClass, $settingEntity, $group, $settingEntity->getUuid());
            $persistedSettings->setGroupName($group);
        }

        $persistedSettings->setPayload($mergeResponse['payload']);
        $persistedSettings->setUpdatedAt(new DateTime());
        $this->manager->persist($persistedSettings);
        $this->manager->flush();
        $responseEntity = $mergeResponse['data'];
        if (!$responseEntity->getUuid()) {
            $responseEntity->setUuid($persistedSettings->getUuid());
        }
        if (!$responseEntity->getRelationId()) {
            $responseEntity->setRelationId($persistedSettings->getRelationId());
        }

        return $responseEntity;
    }

    public function persistRelation(SettingsInterface $settingsOwner)
    {
        $annotation = $this->annotationReader->getAnnotationsForClass($settingsOwner);
        if ($annotation && $targetResources = $annotation->getTargetResources()) {
            foreach ($targetResources as $targetResource) {
                $getter = $this->accessorName($targetResource, 'getter');
                if (method_exists($settingsOwner, $getter)) {
                    $settingEntity = $settingsOwner->$getter();

                    if (!$settingEntity) {
                        $resourceAnnotation = $this->annotationReader->getPropertyAnnotation($targetResource, $settingsOwner);
                        $targetClassName = $resourceAnnotation->getTargetClass();
                        $settingEntity = new $targetClassName;
                    }

                    if (!is_object($settingEntity) && is_array($settingEntity)) {
                        $resourceAnnotation = $this->annotationReader->getPropertyAnnotation($targetResource, $settingsOwner);
                        $targetClassName = $resourceAnnotation->getTargetClass();
                        $targetClass = new $targetClassName;
                        foreach ($settingEntity as $key => $value) {
                            $setter = $this->accessorName($key, 'getter');
                            if (method_exists($targetClass, $setter)) {
                                $targetClass->$setter($value);
                            }
                        }
                        $settingEntity = $targetClass;
                    }
                }

                $settings = $this->persist($settingEntity, $settingsOwner);
            }
        }
    }

    public function mergeProperties($data, $configDefaults, $existingPayload = null): array
    {
        $reflectionClass = new ReflectionClass($data);
        $payloadArray = [];

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $propertyKey = $reflectionProperty->getName();
            $getterName = $this->accessorName($propertyKey, 'getter');
            $setterName = $this->accessorName($propertyKey, 'setter');
            $propertyValue = $data->$getterName();
            if ($propertyValue === null) {
                if (null !== $existingPayload && array_key_exists($propertyKey, $existingPayload)) {
                    $data->$setterName($existingPayload[$propertyKey]);
                    $payloadArray[$propertyKey] = $propertyValue;
                } elseif (array_key_exists($propertyKey, $configDefaults)) {
                    //validation could happen here
                    if (isset($defaults[$propertyKey]['value'])) {
                        $data->$setterName($configDefaults[$propertyKey]['value']);
                    }
                }
                continue;
            }

            if (array_key_exists($propertyKey, $configDefaults) && $propertyValue !== $configDefaults[$propertyKey]['value']) {
                $payloadArray[$propertyKey] = $propertyValue;
            }
        }

        return [
            'data' => $data,
            'payload' => json_encode($payloadArray, JSON_THROW_ON_ERROR)
        ];
    }

    /**
     * @throws Exception
     */
    public function setupSettingsBundleSettings($relationClass, $data, $group, $uuid = null): SettingsBundleSetting
    {
        $relationClassName = null;

        if (!$uuid) {
            $uuidGenerator = new Uuid();
            $uuid = $uuidGenerator->generate();
        }

        if ($relationClass) {
            if (is_string($relationClass)) {
                $relationClassName = $relationClass;
                $relationClass = new $relationClass;
            } else {
                $relationClassName = $relationClass::class;
            }
        }

        $persistedSettings = new SettingsBundleSetting();
        $persistedSettings->setUuid($uuid);
        $persistedSettings->setRelationClass($relationClassName);
        $persistedSettings->setRelationId($relationClass ? $relationClass->getIdentifier() : null);
        $persistedSettings->setGroupName($group);
        $persistedSettings->setName($group);
        $persistedSettings->setCreatedAt(new DateTime());
        return $persistedSettings;
    }

    public function accessorName($propertyName, $accessorType): string
    {
        if ($accessorType !== 'getter' && $accessorType !== 'setter') {
            throw new InvalidSettingsAccessorException();
        }
        $prefix = '';
        switch ($accessorType) {
            case 'getter':
                $prefix = 'get';
                break;
            case 'setter':
                $prefix = 'set';
                break;
        }

        return $prefix . '' . ucfirst($propertyName);
    }

    public function remove()
    {
        // TODO: Implement remove() method.
    }

    public function getConfiguration()
    {
        // TODO: Implement getConfiguration() method.
    }
}