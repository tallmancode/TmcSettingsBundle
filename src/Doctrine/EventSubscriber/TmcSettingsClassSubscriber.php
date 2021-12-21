<?php

namespace TallmanCode\SettingsBundle\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use TallmanCode\SettingsBundle\Entity\TmcSettingsInterface;

class TmcSettingsClassSubscriber implements EventSubscriberInterface
{
    const SETTINGS_INTERFACE = TmcSettingsInterface::class;

    public static  function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata
        ];
    }

    public function loadClassMetaData(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();

        if (!in_array(self::SETTINGS_INTERFACE, class_implements($metadata->getName()), true)) {
            return;
        }

    }
}