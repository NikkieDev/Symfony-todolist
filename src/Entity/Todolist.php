<?php

namespace App\Entity;

use App\Repository\TodolistRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: TodolistRepository::class)]
class Todolist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[Orm\OneToMany(targetEntity: Item::class, mappedBy: 'todoList', cascade: ['persist', 'remove'], orphanRemoval: true)] // orphanRemoval -> removes item from db if removed from this collection
    private Collection $items;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        $this->items->add($item);
        $item->setTodoList($this); // set the 'todolist' property for 'item' to the current todolist

        return $this;
    }

    public function remoteItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            if ($item->getTodoListId() === $this->getId()) {
                $item->setTodoList(null);
            }
        }

        return $this;
    }
}
