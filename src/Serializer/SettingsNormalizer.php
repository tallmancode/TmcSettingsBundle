<?php
namespace TallmanCode\SettingsBundle\Serializer;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;
use TallmanCode\SettingsBundle\Annotation\TmcSettingsGroup;
use TallmanCode\SettingsBundle\Annotation\TmcSettingsOwner;
use TallmanCode\SettingsBundle\Exception\SettingsOwnerException;
use TallmanCode\SettingsBundle\Manager\SettingsManager;

class SettingsNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    private $decorated;
    private Reader $reader;
    private SettingsManager $settingsManager;

    public function __construct(NormalizerInterface $decorated, Reader $reader, SettingsManager $settingsManager)
    {
        if (!$decorated instanceof DenormalizerInterface) {
            throw new \InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
        }

        $this->decorated = $decorated;
        $this->reader = $reader;
        $this->settingsManager = $settingsManager;
    }

    /**
     * @throws \ReflectionException|\JsonException
     */
    public function supportsNormalization($data, $format = null): bool
    {
        if(is_object($data)){

            $annotation = $this->reader->getClassAnnotation(new \ReflectionClass($data), TmcSettingsOwner::class);
            if(null !== $annotation){
                $settingsProperties = $annotation->getTargetResources();

                if(is_array($settingsProperties)){
                    $data = $this->settingsManager->getCollection($data, $settingsProperties);

                }elseif(is_string($settingsProperties)){
                    $data = $this->settingsManager->get($data, $settingsProperties);
                }else{
                    throw new SettingsOwnerException(get_class($data));
                }
            }
            return $this->decorated->supportsNormalization($data, $format);
        }
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return $this->decorated->normalize($object, $format, $context);
    }

    public function setSerializer(SerializerInterface $serializer) : void
    {
        if($this->decorated instanceof SerializerAwareInterface) {
            $this->decorated->setSerializer($serializer);
        }
    }
}