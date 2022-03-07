<?php

namespace TallmanCode\SettingsBundle\ApiPlatform;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\Operation\PathSegmentNameGeneratorInterface;
use App\Controller\TestController;
use Doctrine\Common\Annotations\Reader;
use TallmanCode\SettingsBundle\Annotation\SettingsAnnotationReaderInterface;
use TallmanCode\SettingsBundle\Annotation\TmcSettingsOwner;
use TallmanCode\SettingsBundle\Annotation\TmcSettingsResource;

class SettingsResourceMetadataFactory implements ResourceMetadataFactoryInterface
{
    private ResourceMetadataFactoryInterface $decorated;
    private SettingsAnnotationReaderInterface $annotationReader;
    private PathSegmentNameGeneratorInterface $segmentNameGenerator;

    public function __construct(ResourceMetadataFactoryInterface $decorated, SettingsAnnotationReaderInterface $annotationReader, PathSegmentNameGeneratorInterface $segmentNameGenerator)
    {
        $this->decorated = $decorated;
        $this->annotationReader = $annotationReader;
        $this->segmentNameGenerator = $segmentNameGenerator;
    }

    public function create(string $resourceClass): ResourceMetadata
    {
        $resourceMetadata = $this->decorated->create($resourceClass);
        if (($annotation = $this->annotationReader->getAnnotationsForClass($resourceClass)) && $annotation instanceof TmcSettingsResource) {
            if ($annotation->getRelationClass()) {
                $collectionOperators = $this->generateRelatedCollectionOperator($resourceMetadata, $resourceClass);
            } else {
                $collectionOperators = $this->generateUnrelatedCollectionOperator($resourceMetadata, $resourceClass);
            }
            $resourceMetadata = $resourceMetadata->withCollectionOperations($collectionOperators);
        }

        return $resourceMetadata;
    }

    private function generateUnrelatedCollectionOperator($resourceMetadata, $resourceClass)
    {
        $collectionOperators = $resourceMetadata->getCollectionOperations();
        $collectionOperators['get'] = [
            "method" => "GET",
            "path" => '/' . $this->segmentNameGenerator->getSegmentName($resourceMetadata->getShortName()),
            "stateless" => null,
            "controller" => 'tmc_settings.controller.settings_get_action',
            "pagination_enabled" => false,
            "input_formats" => [
                "jsonld" => ["application/ld+json"]
            ],
            "output_formats" => [
                "jsonld" => ["application/ld+json"]
            ],
            "defaults" => [
                '_api_resource_class' => $resourceClass,
            ]
        ];

        return $collectionOperators;
    }

    private function generateRelatedCollectionOperator($resourceMetadata, $resourceClass)
    {
        $collectionOperators = $resourceMetadata->getCollectionOperations();
        $collectionOperators['get'] = [
            "method" => "GET",
            "path" => '/' . $this->segmentNameGenerator->getSegmentName($resourceMetadata->getShortName()) . '/{group}/{relationId}',
            "stateless" => null,
            "controller" => 'tmc_settings.controller.settings_get_action',
            "pagination_enabled" => false,
            "input_formats" => [
                "jsonld" => ["application/ld+json"]
            ],
            "output_formats" => [
                "jsonld" => ["application/ld+json"]
            ],
            "openapi_context" => [
                "parameters" => [
                    [
                        'name' => 'group',
                        'in' => 'path',
                        "description" => "searched text",
                        'required' => true,
                        'type' => 'string',
                    ],
                    [
                        'name' => 'relationId',
                        'in' => 'path',
                        "description" => "searched text",
                        'required' => true,
                        'type' => 'string',
                    ],
                ]
            ],
            "defaults" => [
                '_api_resource_class' => $resourceClass,
            ]
        ];

        return $collectionOperators;
    }
}