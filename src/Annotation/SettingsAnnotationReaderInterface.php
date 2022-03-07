<?php

namespace TallmanCode\SettingsBundle\Annotation;

interface SettingsAnnotationReaderInterface
{
    public function getAnnotationsForClass($settingClass);

    public function getAnnotationGroup($settingClass= null, ?TmcSettingsResource $annotation = null): ?string;
}