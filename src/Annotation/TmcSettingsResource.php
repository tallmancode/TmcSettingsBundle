<?php
namespace TallmanCode\SettingsBundle\Annotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 */
class TmcSettingsResource
{

    private ?string $relationClass;
    private ?string $settingsGroup;
    private ?array $defaults;

    public function __construct(?string $settingsGroup = null, ?string $relationClass = null, ?array $defaults= null)
    {
        $this->relationClass = $relationClass;
        $this->settingsGroup = $settingsGroup;
        $this->defaults = $defaults;
    }

    /**
     * @return string|null
     */
    public function getRelationClass(): ?string
    {
        return $this->relationClass;
    }

    /**
     * @return string
     */
    public function getSettingsGroup(): ?string
    {
        return $this->settingsGroup;
    }

    /**
     * @return array|null
     */
    public function getDefaults(): ?array
    {
        return $this->defaults;
    }
}