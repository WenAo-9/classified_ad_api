<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    public function responseBadRequest($message = 'invalid data')
    {
        return $this->createResponse($message, Response::HTTP_BAD_REQUEST);
    }

    public function responseCreated($data)
    {
        return $this->createResponse($data, Response::HTTP_CREATED);
    }

    public function responseOk($data)
    {
        return $this->createResponse($data, Response::HTTP_OK);
    }

    public function responseNotFound($message = 'ressource not found')
    {
        return $this->createResponse($message, Response::HTTP_NOT_FOUND);
    }

    public function responseNoContent($message = 'ressource deleted')
    {
        return $this->createResponse($message, Response::HTTP_NO_CONTENT);
    }

    public function responseForbidden($message = 'forbidden')
    {
        return $this->createResponse($message, Response::HTTP_FORBIDDEN);
    }


    public function createResponse($content, $code)
    {
        $response = new JsonResponse();
        $response->setContent($content);
        $response->setStatusCode($code);

        return $response;
    }
    
}