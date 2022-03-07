<?php


namespace TallmanCode\SettingsBundle\Manager;

use TallmanCode\SettingsBundle\Entity\SettingsInterface;
use TallmanCode\SettingsBundle\Entity\TmcSettingsInterface;

interface SettingsManagerInterface
{
    public function find($settingClass, $relationClass = null, $annotation = null, $group = null);

    public function populateRelations(SettingsInterface $settingsOwner);

    public function persist(TmcSettingsInterface $settingEntity, $relationClass = null, $annotation = null);

    public function persistRelation(SettingsInterface $settingsOwner);

    public function mergeProperties($data, $configDefaults, $existingPayload = null): array;

    public function setupSettingsBundleSettings($relationClass, $data, $group, $uuid = null);

    public function accessorName($propertyName, $accessorType): string;

    public function remove();

    public function getConfiguration();
}