<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Objects\ItemStatus;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['item:read', 'todoList:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    #[Groups(['item:read', 'todoList:read'])]
    private ItemStatus $status = ItemStatus::New;

    #[ORM\Column(length: 64)]
    #[Groups(['item:read', 'todoList:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 512, nullable: true)]
    #[Groups(['item:read', 'todoList:read'])]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Todolist::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['item:read'])]
    private ?TodoList $todoList = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ItemStatus
    {
        return $this->status;
    }

    public function setStatus(ItemStatus $newStatus): self
    {
        $this->status = $newStatus;
        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTodoList(): ?TodoList
    {
        return $this->todoList;
    }

    public function setTodoList(?TodoList $todoList): self
    {
        $this->todoList = $todoList;
        return $this;
    }
}
