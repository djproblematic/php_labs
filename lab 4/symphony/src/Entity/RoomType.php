<?php

namespace App\Entity;

use App\Repository\RoomTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomTypeRepository::class)]
class RoomType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Room>
     */
    #[ORM\OneToMany(targetEntity: Room::class, mappedBy: 'roomType')]
    private Collection $capacity;

    public function __construct()
    {
        $this->capacity = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getCapacity(): Collection
    {
        return $this->capacity;
    }

    public function addCapacity(Room $capacity): static
    {
        if (!$this->capacity->contains($capacity)) {
            $this->capacity->add($capacity);
            $capacity->setRoomType($this);
        }

        return $this;
    }

    public function removeCapacity(Room $capacity): static
    {
        if ($this->capacity->removeElement($capacity)) {
            // set the owning side to null (unless already changed)
            if ($capacity->getRoomType() === $this) {
                $capacity->setRoomType(null);
            }
        }

        return $this;
    }
}
