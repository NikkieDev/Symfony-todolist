<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LoggerInterface;

use App\Service\ItemManager;

class ItemController
{
    private ItemManager $itemManager;
    private SerializerInterface $serializer;
    private LoggerInterface $logger;

    public function __construct(
        ItemManager $itemManager,
        SerializerInterface $serializer,
        LoggerInterface $logger
    )
    {
        $this->itemManager = $itemManager;
        $this->serializer = $serializer;
        $this->logger = $logger;

        $this->logger->info("ItemController initiated");
    }

    #[Route('/item/create', name:'create_item', methods: ['POST'])]
    public function createItem(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);

        if (!$parameters) {
            throw new \Exception("Missing required parameters");
        }

        try {
            $item = $this->itemManager->create($parameters);

            $this->logger->info("Item '" . $item->getName() . "' received from itemManager");

            $itemSerialized = $this->serializer->serialize($item, 'json', ['groups' => ['todoList:read']]);
            $this->logger->info("Item serialized: " . $itemSerialized); // Issue serializing.

            return new Response($itemSerialized, 200, ['Content-Type' => 'application/json']);
        } catch (NotFoundHttpException $e) {
            $jsonResponse = ["message"=>"list not found"];
            return new Response(json_encode($jsonResponse), 404, ['Content-Type' => 'application/json']);
        }
    }
}