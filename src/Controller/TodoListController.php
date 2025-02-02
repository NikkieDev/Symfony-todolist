<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LoggerInterface;

use App\Entity\Todolist;
use App\Service\ListManager;

class TodoListController
{
    private ListManager $listManager;
    private SerializerInterface $serializer;
    private LoggerInterface $logger;

    public function __construct(
        ListManager $listManager,
        SerializerInterface $serializer,
        LoggerInterface $logger
        )
    {
        $this->listManager = $listManager;
        $this->serializer = $serializer;
        $this->logger = $logger;
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
        
        $response = new Response($todoListSerialized, 200, ['Content-Type' => 'application/json']);
        return $response;
    }

    #[Route('/list/name/{id}', name: 'rename_list', methods: ['PUT'])]
    public function renameList(
        Request $request,
        int $id
    ): Response
    {
        $parameters = json_decode($request->getContent(), true);

        if (!$parameters) {
            throw new \Exception("Missing required parameters");
        }
        
        try {
            $todoList = $this->listManager->rename($id, $parameters['name']);
            $todoListSerialized = $this->serializer->serialize($todoList, 'json');
    
            $response = new Response($todoListSerialized, 200, ['Content-Type' => 'application/json']);
            return $response;
        } catch (NotFoundHttpException $e) {
            return new Response('unable to find item', 404);
        }
    }

    #[Route('/list/delete/{id}', name: 'delete_list', methods: ['DELETE'])]
    public function deleteList(
        Request $request,
        int $id
    ): Response
    {
        if (!$id) {
            throw new \Exception("no id provided");
        }
        
        $this->logger->info("Received delete request for list '$id'");

        try {
            $this->listManager->delete($id);
        } catch (NotFoundHttpException $e) {
            return new Response('unable to find item', 404);
        }

        return new Response('ok', 200);
    }
}