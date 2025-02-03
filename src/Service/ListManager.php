<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Todolist;

class ListManager
{
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;

    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Todolist::class);
    }

    public function create($parameters): Todolist
    {
        $this->logger->info("Creating new list: " . $parameters['name']);
        $todoList = new Todolist();

        $todoList->setName($parameters['name']);
        $this->entityManager->persist($todoList);

        $this->entityManager->flush();

        return $todoList;
    }

    public function rename(int $id, string $name): Todolist
    {
        $this->logger->info("Renaming list id '$id' to '$name'");
     
        $todoList = $this->repository->findAndRename($id, $name);
        return $todoList;
    }

    public function delete(int $id): void
    {
        $name = $this->repository->findAndDelete($id);
        
        if ($name) {
            $this->logger->warning("Deleted list '$name'");
        }
    }

    public function getMostRecent(): array
    {
        $this->logger->info("Fetching most recent lists");
        $multipleLists = $this->repository->findMostRecent();

        return $multipleLists;
    }

    public function getPaginated(int $startingId): array
    {
        $this->logger->info("Fetching paginated, start: " . $startingId);
        $multipleLists = $this->repository->findPaginated($startingId);

        return $multipleLists;
    }
}