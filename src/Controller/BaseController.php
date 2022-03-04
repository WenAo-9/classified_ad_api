<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseController extends AbstractController
{
    public function createResponse($code, $message, $data)
    {
        $content = [
            'message' => $message,
            'errors' => $code < 300 ? [] : $data,
            'data' => $code < 300 ? $data : [],
        ];
        
        $content = json_encode($content);

        $response = new JsonResponse();
        $response->setContent($content);
        $response->setStatusCode($code);

        return $response;
    }
    
}