<?php
namespace TallmanCode\SettingsBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 */
class TmcSettingsGroup
{
    private string $group;
    private string $targetClass;

    public function __construct(string $group, string $targetClass)
    {
        $this->group = $group;
        $this->targetClass = $targetClass;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @return string
     */
    public function getTargetClass(): string
    {
        return $this->targetClass;
    }


}