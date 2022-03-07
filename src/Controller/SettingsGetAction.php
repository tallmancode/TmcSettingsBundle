<?php

namespace TallmanCode\SettingsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use TallmanCode\SettingsBundle\Manager\SettingsManagerInterface;

final class SettingsGetAction
{
    private SettingsManagerInterface $settingsManager;

    public function __construct(SettingsManagerInterface $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    public function __invoke(Request $request, $data)
    {
        $resourceClass = $this->getResourceClass($request);
        $setting = $this->settingsManager->find($resourceClass);
        dump($setting);
        return $setting;
    }

    private function getResourceClass(Request $request)
    {
        return $request->attributes->get('_api_resource_class');
    }
}