<?php

namespace TallmanCode\SettingsBundle\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TallmanCode\SettingsBundle\Annotation\SettingsAnnotationReaderInterface;
use TallmanCode\SettingsBundle\Entity\TmcSettingsInterface;
use TallmanCode\SettingsBundle\Manager\SettingsManagerInterface;

class TmcSettingsClassSubscriber
{
    private SettingsAnnotationReaderInterface $annotationReader;
    private SettingsManagerInterface $manager;

    public function __construct(SettingsAnnotationReaderInterface $annotationReader, SettingsManagerInterface $manager)
    {
        $this->annotationReader = $annotationReader;
        $this->manager = $manager;
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        if (!in_array('TallmanCode\SettingsBundle\Entity\SettingsInterface', class_implements($eventArgs->getObject()), true)) {
            return;
        }
        $this->manager->populateRelations($eventArgs->getObject());
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        if (!in_array('TallmanCode\SettingsBundle\Entity\SettingsInterface', class_implements($args->getEntity()), true)) {
            return;
        }
        $this->manager->persistRelation($args->getEntity());
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        if (!in_array('TallmanCode\SettingsBundle\Entity\SettingsInterface', class_implements($args->getEntity()), true)) {
            return;
        }
        $this->manager->persistRelation($args->getEntity());
    }
}