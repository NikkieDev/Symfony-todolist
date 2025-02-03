<?php

namespace App\Repository;

use App\Entity\Todolist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @extends ServiceEntityRepository<Todolist>
 */
class TodolistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Todolist::class);
    }

    public function findAndRename(int $id, string $name): Todolist
    {
        $todoList = $this->find($id);

        if (!$todoList) {
            throw new NotFoundHttpException("Todolist '$id' not found!");
        }

        $todoList->setName($name);
        $this->getEntityManager()->persist($todoList);
        $this->getEntityManager()->flush();
        
        return $todoList;
    }

    public function findAndDelete(int $id): ?string
    {
        $todoList = $this->find($id);

        if (!$todoList) {
            throw new NotFoundHttpException("Todolist '$id' not found!");
        }

        $this->createQueryBuilder('t')
            ->delete(Todolist::class, 't')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();

        return $todoList->getName();
    }
}
