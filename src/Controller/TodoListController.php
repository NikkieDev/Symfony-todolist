<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


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

    #[Route('/lists', name: 'recent_lists', methods: ['GET'])]
    public function getLists(Request $request): JsonResponse
    {
        $startingId = (int) $request->headers->get('x-next-starting-id', 0);
        $multipleTodoLists = [];

        try {
            if ($startingId == 0) {
                $multipleTodoLists = $this->listManager->getMostRecent();
            } else if ($startingId <= -1) {
                return new JsonResponse(null, 404);
            } else {
                $multipleTodoLists = $this->listManager->getPaginated($startingId);
            }
    
            $newLastId = count($multipleTodoLists) > 0 ? end($multipleTodoLists)->getId() : -1;
    
            $multipleTodoListsSerialized = $this->serializer->serialize($multipleTodoLists, 'json');
            $headers = [
                'Content-Type' => 'application/json'
            ];
    
            if ($newLastId > 0) {
                $headers['x-next-starting-id'] = $newLastId;
            }
    
            return new JsonResponse($multipleTodoListsSerialized, 200, $headers);
        } catch (NotFoundHttpException $e) {
            $jsonResponse = ['message'=>$e->getMessage()];
            return new JsonResponse($jsonResponse, 404, ['Content-Type' => 'application/json']);
        }
    }

    #[Route('/list/create', name:"create_list", methods: ['POST'])]
    public function createList(Request $request): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        if (!$parameters) {
            throw new \Exception("Missing required parameters");
        }

        $todoList = $this->listManager->create($parameters);
        $todoListSerialized = $this->serializer->serialize($todoList, 'json');
        
        $response = new JsonResponse($todoListSerialized, 200, ['Content-Type' => 'application/json']);
        return $response;
    }

    #[Route('/list/name/{id}', name: 'rename_list', methods: ['PUT'])]
    public function renameList(
        Request $request,
        int $id
    ): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        if (!$parameters) {
            throw new \Exception("Missing required parameters");
        }
        
        try {
            $todoList = $this->listManager->rename($id, $parameters['name']);
            $todoListSerialized = $this->serializer->serialize($todoList, 'json');
    
            $response = new JsonResponse($todoListSerialized, 200, ['Content-Type' => 'application/json']);
            return $response;
        } catch (NotFoundHttpException $e) {
            $jsonResponse = ['message'=>'unable to find item'];
            return new JsonResponse($jsonResponse, 404, ['Content-Type' => 'application/json']);
        }
    }

    #[Route('/list/delete/{id}', name: 'delete_list', methods: ['DELETE'])]
    public function deleteList(
        Request $request,
        int $id
    ): JsonResponse
    {
        if (!$id) {
            throw new \Exception("no id provided");
        }
        
        $this->logger->info("Received delete request for list '$id'");

        try {
            $this->listManager->delete($id);
        } catch (NotFoundHttpException $e) {
            $jsonResponse = ['message'=>'unable to find item'];
            return new JsonResponse($jsonResponse, 404, ['Content-Type' => 'application/json']);
        }

        return new JsonResponse('ok', 200);
    }
}