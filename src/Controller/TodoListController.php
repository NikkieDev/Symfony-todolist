<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Todolist;
use App\Service\ListManager;

class TodoListController
{
    private ListManager $listManager;
    private SerializerInterface $serializer;

    public function __construct(
        ListManager $listManager,
        SerializerInterface $serializer
        )
    {
        $this->listManager = $listManager;
        $this->serializer = $serializer;
    }

    #[Route('/list/create', name:"create_list", methods: ['POST'])]
    public function createList(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);

        if (!$parameters) {
            throw new \Exception("Missing required parameters");
        }

        $todoList = $this->listManager->create($parameters);
        $todoListSerialized = $this->serializer->serialize($todoList, 'json');
        
        $response = new Response();

        $response->setContent($todoListSerialized);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(200);
        
        return $response;
    }

    
}