<?php

namespace App\Controller;

use App\Entity\CarModel;
use App\Service\CustomSearch;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CarController extends BaseController
{
    private $entityManager;
    private $search;

    public function __construct(EntityManagerInterface $entityManager, CustomSearch $search)
    {
        $this->entityManager = $entityManager;
        $this->search = $search;
    }

    #[Route('/car-models', methods:['GET'])]
    public function getItem(Request $request, SerializerInterface $serializer)
    {
        $options = [
            'page'      => $request->query->get('page'),
            'isActive'  => null,
            'term'      => $request->query->get('term')
        ];

        if ($term = $options['term']) {
            $term = strtolower($term);
            $carModel = $this->entityManager->getRepository(CarModel::class)->findValidCarByLabel($term);

            if (null != $carModel) {
                $data = $serializer->normalize($carModel, 'json', ['groups' => 'car:show']);
                $response = $this->createResponse(Response::HTTP_OK, 'ressource retrieved', $data);

                return $response;
                
            } else {
                $response = $this->createResponse(Response::HTTP_FORBIDDEN, 'invalid car model');

                $carModel = [];
                $carModels = $this->entityManager->getRepository(CarModel::class)->findValidCars($options);
                $carIds = $this->search->searchClassByLabel($carModels, $term);
                
                if(null != $carIds) {
                    foreach ($carIds as $key =>$carId) {
                        $carModel[$key] = array_filter($carModels, function($car) use ($carId) {
                            return $car->getId() == $carId;
                        });
                    }

                    $data = $serializer->normalize($carModel, 'json', ['groups' => 'car:show']);
                    $response = $this->createResponse(Response::HTTP_OK, 'ressource retrieved',$data);
                }
            }

        } else {
            $carModels = $this->entityManager->getRepository(CarModel::class)->findValidCars($options);
            $data = $serializer->normalize($carModels, 'json', ['groups' => 'car:show']);
            $response = $this->createResponse(Response::HTTP_OK, 'ressource retrieved', $data);
        }

        return $response;
    }
}