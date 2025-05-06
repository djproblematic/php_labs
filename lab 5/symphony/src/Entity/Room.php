<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; 

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['room:read', 'room:write'])]  
    private ?string $number = null;

    #[ORM\ManyToOne(targetEntity: RoomType::class, inversedBy: 'rooms')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['room:read', 'room:write'])]  
    private ?RoomType $roomType = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['room:read', 'room:write'])] 
    private ?int $capacity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    #[Groups(['room:read', 'room:write'])]  
    private ?string $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getRoomType(): ?RoomType
    {
        return $this->roomType;
    }

    public function setRoomType(?RoomType $roomType): self
    {
        $this->roomType = $roomType;
        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;
        return $this;
    }
}
