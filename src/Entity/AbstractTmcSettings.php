<?php


namespace TallmanCode\SettingsBundle\Entity;


use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @author Steve "Tallman" Stewart <steve@tallmancode.co.za>
 */
abstract class AbstractTmcSettings
{
    /**
     * @ApiProperty(identifier=true)
     */
    protected $uuid;

    protected $relationId;

    /**
     * @return mixed
     */
    public function getRelationId()
    {
        return $this->relationId;
    }

    /**
     * @param mixed $relationId
     */
    public function setRelationId($relationId): void
    {
        $this->relationId = $relationId;
    }

    public function unsetRelationId(): void
    {
        unset($this->relationId);
    }

    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param mixed $uuid
     */
    public function setUuid($uuid): void
    {
        $this->uuid = $uuid;
    }
}