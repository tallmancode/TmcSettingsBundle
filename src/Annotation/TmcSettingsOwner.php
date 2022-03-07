<?php
namespace TallmanCode\SettingsBundle\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 */
class TmcSettingsOwner
{
    private ?array $targetResources ;

    public function __construct(array $targetResources)
    {
        $this->targetResources = $targetResources;
    }

    public function getTargetResources() : ?array
    {
        return $this->targetResources;
    }

}