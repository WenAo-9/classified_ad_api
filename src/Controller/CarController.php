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
        if ($term = $request->query->get('term')) {
            $term = strtolower($term);
            $carModel = $this->entityManager->getRepository(CarModel::class)->findValidCarByLabel($term);

            if (null != $carModel) {
                $data = $serializer->serialize($carModel, 'json', ['groups' => 'car:show']);
                $response = $this->responseOk($data);

                return $response;
                
            } else {
                $response = $this->responseForbidden('invalid car model');

                $carModel = [];
                $carModels = $this->entityManager->getRepository(CarModel::class)->findValidCars();
                $carIds = $this->search->searchClassByLabel($carModels, $term);
                
                if(null != $carIds) {
                    foreach ($carIds as $key =>$carId) {
                        $carModel[$key] = array_filter($carModels, function($car) use ($carId) {
                            return $car->getId() == $carId;
                        });
                    }

                    $data = $serializer->serialize($carModel, 'json', ['groups' => 'car:show']);
                    $response = $this->responseOk($data);
                }
            }

        } else {
            $carModels = $this->entityManager->getRepository(CarModel::class)->findValidCars();
            $data = $serializer->serialize($carModels, 'json', ['groups' => 'car:show']);
            $response = $this->responseOk($data);
        }

        return $response;
    }
}