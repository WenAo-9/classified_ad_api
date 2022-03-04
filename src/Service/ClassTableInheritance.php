<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class ClassTableInheritance
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * return mapped child from Class Table Inheritance
     * using metadata to retrieve discriminator value
     */
    public function findSubclassByType($mainClass, $type)
    {
        $metaDataFactory = $this->entityManager->getMetadataFactory();

        $subClasses = $metaDataFactory->getMetaDataFor($mainClass)->subClasses;

        $class = array_filter($subClasses, function($subClass) use ($metaDataFactory, $type) {
            return $metaDataFactory->getMetaDataFor($subClass)->discriminatorValue == $type;
        });

        return $class;
    }

    /**
     * return associated entity from mapped child
     * using metadata to define which attribute is inversed by our class
     */
    public function relatedObject($subclass, $rawData)
    {
        $data = json_decode($rawData);

        $metaDataFactory = $this->entityManager->getMetadataFactory();

        $associations = $metaDataFactory->getMetaDataFor($subclass)->getAssociationMappings();

        foreach ($associations as $name => $association) {
            if ($association['inversedBy'] == 'ads') {
                $target = $metaDataFactory->getMetaDataFor($subclass)->getAssociationTargetClass($name);
                return $this->entityManager->getRepository($target)->findOneById($data->model);
            }
        }
    }

    /**
     * retrieve setters to process entity in PUT context
     */
    public function process($class, $rawUpdated)
    {
        $data = json_decode($rawUpdated);

        foreach ($data as $key => $datum) {

            $key = ucfirst($key);

            if (method_exists($class, 'set'.$key) && (method_exists($class, 'get'.$key) || method_exists($class, 'is'.$key))) {
                $reflectionMethod = new \ReflectionMethod($class, 'set'.$key); 
                $reflectionMethod->invoke($class, $datum);
            }
        }

        return true;
    }

    /**
     * retrieve and return discriminator mapping
     */
    public function findSubclassTypes($mainClass)
    {
        $metaDataFactory = $this->entityManager->getMetadataFactory();
        
        $subclasses = $metaDataFactory->getMetaDataFor($mainClass)->subClasses;

        $types = [];

        foreach ($subclasses as $subclass) {
            $discr = $metaDataFactory->getMetaDataFor($subclass)->discriminatorValue;
            $types[$discr] = $subclass;
        }

        return $types;
    }
}