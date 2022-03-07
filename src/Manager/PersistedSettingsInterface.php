<?php

namespace TallmanCode\SettingsBundle\Manager;

interface PersistedSettingsInterface
{
    public function get($relationClass = null, $group = null, $relationId = null);

    public function getPayload($relationClass);
}