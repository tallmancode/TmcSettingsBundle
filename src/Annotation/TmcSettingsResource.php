<?php
namespace TallmanCode\SettingsBundle\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 */
class TmcSettingsResource
{

    private string $relationClass;
    private string $settingsGroup;

    public function __construct(string $relationClass, string $settingsGroup)
    {
        $this->relationClass = $relationClass;
        $this->settingsGroup = $settingsGroup;
    }

    /**
     * @return string
     */
    public function getRelationClass(): string
    {
        return $this->relationClass;
    }

    /**
     * @return string
     */
    public function getSettingsGroup(): string
    {
        return $this->settingsGroup;
    }
}