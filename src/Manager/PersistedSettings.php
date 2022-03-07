<?php

namespace TallmanCode\SettingsBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use TallmanCode\SettingsBundle\Entity\SettingsBundleSetting;
use TallmanCode\SettingsBundle\Entity\SettingsInterface;

class PersistedSettings implements PersistedSettingsInterface
{
    private $repo;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->repo = $manager->getRepository(SettingsBundleSetting::class);
    }

    public function get($relationClass = null, $group = null, $relationId = null)
    {
        $relationClassname = null;

        if(null !== $relationClass){
            if(is_string($relationClass)){
                $relationClass = new $relationClass;
            }
            $relationClassname = get_class($relationClass);
            if(method_exists($relationClass, 'getIdentifier')){
                $relationId = $relationClass->getIdentifier();
            }
        }
        return $this->repo->findOneBy(['relationClass' => $relationClassname, 'relationId' => $relationId, 'groupName' => $group]);

    }

    public function getPayload($relationClass)
    {
        $payload = $relationClass->getPayload();
        return json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
    }
}