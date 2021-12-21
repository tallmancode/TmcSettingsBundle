<?php
namespace TallmanCode\SettingsBundle\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 */
class TmcSettingsOwner
{
    private $targetResources;

    public function __construct($targetResources)
    {
        $this->targetResources = $targetResources;
    }

    public function getTargetResources()
    {
        return $this->targetResources;
    }

}