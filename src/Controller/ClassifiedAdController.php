<?php

namespace App\Controller;

use App\Entity\ClassifiedAd;
use App\Service\ClassTableInheritance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
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

        $data = $serializer->normalize($classifiedAds, null, ['groups' => 'ad:list']);

        $response = $this->createResponse(Response::HTTP_OK, 'ressources retrieved', $data);

        return $response;
    }

    #[Route('/classified-ads/{adtype}', methods:['POST'])]
    public function createItem($adtype, Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $rawData = $request->getContent();
        
        if (!$rawData || !json_decode($rawData)) {
            $response = $this->createResponse(Response::HTTP_BAD_REQUEST, 'invalid request');
            return $response;
        }

        $subclass = $this->cti->findSubclassByType(ClassifiedAd::class, $adtype);

        if (!$subclass) {
            $response = $this->createResponse(Response::HTTP_BAD_REQUEST, 'invalid ad type');
            return $response;
        }
        
        $relatedObject = $this->cti->relatedObject($subclass[0], $rawData);

        if (is_null($relatedObject)) {
            $response = $this->createResponse(Response::HTTP_BAD_REQUEST, 'invalid car model');
            return $response;
        }

        $classifiedAd = $serializer->deserialize($rawData, $subclass[0], 'json');
        $classifiedAd->setModel($relatedObject);

        $violations = $validator->validate($classifiedAd);

        if (count($violations) > 0) {
            $errors = $serializer->normalize($violations, null);
            $response = $this->createResponse(Response::HTTP_BAD_REQUEST, 'constraint violations',$errors);
        } else {
            $this->entityManager->persist($classifiedAd);
            $this->entityManager->flush();

            $data = $serializer->normalize($classifiedAd, null, ['groups' => 'ad:show']);
            $response = $this->createResponse(Response::HTTP_CREATED, 'ressource created',$data);
        }

        return $response;
    }

    #[Route('/classified-ads/{id}', methods:['PUT', 'PATCH'])]
    public function updateItem($id, Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $rawData = $request->getContent();

        if (!json_decode($rawData)) {
            $response = $this->createResponse(Response::HTTP_BAD_REQUEST, 'invalid request');
            return $response;
        }

        $classifiedAd = $this->entityManager->getRepository(ClassifiedAd::class)->findOneById($id);

        if (!$classifiedAd) {
            $response = $this->createResponse(Response::HTTP_NOT_FOUND, 'invalid classified ad');
            return $response;
        }

        $isProcessed = $this->cti->process($classifiedAd, $rawData);
        
        if ($isProcessed && count($validator->validate($classifiedAd)) == 0) {
            $this->entityManager->persist($classifiedAd);
            $this->entityManager->flush();

            $data = $serializer->normalize($classifiedAd, null, ['groups' => 'ad:show']);
            $response = $this->createResponse(Response::HTTP_OK, 'ressource updated',$data);
        } else {
            $errors = $serializer->normalize($validator->validate($classifiedAd), null);
            $response = $this->createResponse(Response::HTTP_BAD_REQUEST, 'invalid data provided', $errors);
        }

        return $response;
    }

    #[Route('/classified-ads/{id}', methods:['DELETE'], requirements:['id' => '\d+'])]
    public function deleteItem($id, Request $request)
    {
        $classifiedAd = $this->entityManager->getRepository(ClassifiedAd::class)->findOneById($id);

        if (!$classifiedAd) {
            $response = $this->createResponse(Response::HTTP_NOT_FOUND, 'ressource not found');
        } else {
            $this->entityManager->remove($classifiedAd);
            $this->entityManager->flush();

            $response = $this->createResponse(Response::HTTP_NO_CONTENT, 'ressource deleted');
        }

        return $response;
    }

    #[Route('/classified-ad-types', methods:['GET'])]
    public function getItemTypes(SerializerInterface $serializer)
    {
        $types = $this->cti->findSubclassTypes(ClassifiedAd::class);

        $data = $serializer->normalize($types, null);

        $response = $this->createResponse(Response::HTTP_OK, 'ressource retrieved',$data);

        return $response;
    }
}