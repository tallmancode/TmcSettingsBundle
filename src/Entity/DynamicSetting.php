<?php

namespace TallmanCode\SettingsBundle\Entity;

use App\Repository\Settings\DynamicSettingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class DynamicSettings
 * @ORM\Table (name="tmc_settings")
 * @ORM\Entity(repositoryClass="TallmanCode\SettingsBundle\Repository\DynamicSettingRepository")
 */
class DynamicSetting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uuid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $relationClass;

    /**
     * @ORM\Column(type="integer")
     */
    private $relationId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $groupName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $payload;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRelationClass(): ?string
    {
        return $this->relationClass;
    }

    public function setRelationClass(string $relationClass): self
    {
        $this->relationClass = $relationClass;

        return $this;
    }

    public function getRelationId(): ?int
    {
        return $this->relationId;
    }

    public function setRelationId(int $relationId): self
    {
        $this->relationId = $relationId;

        return $this;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function setGroupName(?string $groupName): self
    {
        $this->groupName = $groupName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function setPayload(string $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param mixed $uuid
     */
    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}
