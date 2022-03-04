<?php

namespace App\Controller;

use App\Entity\ClassifiedAd;
use App\Service\ClassTableInheritance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClassifiedAdController extends BaseController
{
    private $entityManager;
    private $cti;

    public function __construct(EntityManagerInterface $entityManager, ClassTableInheritance $cti)
    {
        $this->entityManager = $entityManager;
        $this->cti = $cti;
    }

    #[Route('/classified-ads', methods:['GET'])]
    public function getItems(Request $request, SerializerInterface $serializer)
    {
        $options = [
            'discr'     => '',
            'page'      => $request->query->get('page'),
            'isActive'  => null,
        ];

        $subclass = $this->cti->findSubclassByType(ClassifiedAd::class, $request->query->get('adtype'));

        $options['discr'] = !empty($class) ?$metaDataFactory->getMetaDataFor($subclass[0]) : null;
        
        $classifiedAds = $this->entityManager
            ->getRepository(ClassifiedAd::class)
            ->findWithOptions($options)
        ;

        $data = $serializer->serialize($classifiedAds, 'json', ['groups' => 'ad:list']);

        $response = $this->responseOk($data);

        return $response;
    }

    #[Route('/classified-ads/{adtype}', methods:['POST'])]
    public function createItem($adtype, Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $rawData = $request->getContent();
        
        if (!$rawData || !json_decode($rawData)) {
            $response = $this->responseBadRequest();
            return $response;
        }

        $subclass = $this->cti->findSubclassByType(ClassifiedAd::class, $adtype);

        if (!$subclass) {
            $response = $this->responseBadRequest('invalid ad type');
            return $response;
        }
        
        $relatedObject = $this->cti->relatedObject($subclass[0], $rawData);

        if (is_null($relatedObject)) {
            $response = $this->responseBadRequest('invalid car model');
            return $response;
        }

        $classifiedAd = $serializer->deserialize($rawData, $subclass[0], 'json');
        $classifiedAd->setModel($relatedObject);

        $violations = $validator->validate($classifiedAd);

        if (count($violations) > 0) {
            $errors = $serializer->serialize($violations, 'json');
            $response = $this->responseBadRequest($errors);
        } else {
            $this->entityManager->persist($classifiedAd);
            $this->entityManager->flush();

            $data = $serializer->serialize($classifiedAd, 'json', ['groups' => 'ad:show']);
            $response = $this->responseCreated($data);
        }

        return $response;
    }

    #[Route('/classified-ads/{id}', methods:['PUT', 'PATCH'])]
    public function updateItem($id, Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $rawData = $request->getContent();

        if (!json_decode($rawData)) {
            $response = $this->responseBadRequest();
            return $response;
        }

        $classifiedAd = $this->entityManager->getRepository(ClassifiedAd::class)->findOneById($id);

        if (!$classifiedAd) {
            $response = $this->responseNotFound('invalid classified ad');
            return $response;
        }

        $isProcessed = $this->cti->process($classifiedAd, $rawData);
        
        if ($isProcessed && count($validator->validate($classifiedAd)) == 0) {
            $this->entityManager->persist($classifiedAd);
            $this->entityManager->flush();

            $data = $serializer->serialize($classifiedAd, 'json', ['groups' => 'ad:show']);
            $response = $this->responseOk($data);
        } else {
            $errors = $serializer->serialize($validator->validate($classifiedAd), 'json');
            $response = $this->responseBadRequest($errors);
        }

        return $response;
    }

    #[Route('/classified-ads/{id}', methods:['DELETE'], requirements:['id' => '\d+'])]
    public function deleteItem($id, Request $request)
    {
        $classifiedAd = $this->entityManager->getRepository(ClassifiedAd::class)->findOneById($id);

        if (!$classifiedAd) {
            $response = $this->responseNotFound();
        } else {
            $this->entityManager->remove($classifiedAd);
            $this->entityManager->flush();

            $response = $this->responseNoContent();
        }

        return $response;
    }

    #[Route('/classified-ad-types', methods:['GET'])]
    public function getItemTypes(SerializerInterface $serializer)
    {
        $types = $this->cti->findSubclassTypes(ClassifiedAd::class);

        $data = $serializer->serialize($types, 'json');

        $response = $this->responseOk($data);

        return $response;
    }
}