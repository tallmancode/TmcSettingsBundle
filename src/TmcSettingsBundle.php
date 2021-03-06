<?php


namespace TallmanCode\SettingsBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use TallmanCode\SettingsBundle\DependencyInjection\TmcSettingsExtension;

class TmcSettingsBundle extends Bundle
{

    /**
     * Overridden to allow for the custom extension alias.
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new TmcSettingsExtension();
        }
        return $this->extension;
    }
}