<?php


namespace TallmanCode\SettingsBundle\DataPersister;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TallmanCode\SettingsBundle\Annotation\TmcSettingsResource;
use TallmanCode\SettingsBundle\Manager\SettingsManager;

class TmcSettingsPersister implements ContextAwareDataPersisterInterface
{

    private ContextAwareDataPersisterInterface $decorated;

    private Reader $reader;

    private SettingsManager $settingsManager;

    public function __construct(ContextAwareDataPersisterInterface $decorated, Reader $reader, SettingsManager $settingsManager)
    {
        $this->decorated = $decorated;
        $this->reader = $reader;
        $this->settingsManager = $settingsManager;
    }

    /**
     * @throws \JsonException
     */
    public function supports($data, array $context = []): bool
    {
        $reflectionClass = new \ReflectionClass($data);

        if($annotation = $this->reader->getClassAnnotation($reflectionClass, TmcSettingsResource::class)){
            $data = $this->settingsManager->save($data, $annotation->getRelationClass(), $annotation->getSettingsGroup());
        }

        return $this->decorated->supports($data, $context);
    }

    public function persist($data, array $context = [])
    {
        return $this->decorated->persist($data, $context);
    }

    public function remove($data, array $context = [])
    {
        return $this->decorated->remove($data, $context);
    }
}