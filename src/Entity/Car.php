<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CarRepository::class)
 */
class Car
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     */
    private $price_per_day;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     */
    private $price_per_km;

    /**
     * @ORM\OneToMany(targetEntity=Rental::class, mappedBy="Car", orphanRemoval=true)
     */
    private $rentals;

    public function __construct()
    {
        $this->rentals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPricePerDay(): ?int
    {
        return $this->price_per_day;
    }

    public function setPricePerDay(int $price_per_day): self
    {
        $this->price_per_day = $price_per_day;

        return $this;
    }

    public function getPricePerKm(): ?int
    {
        return $this->price_per_km;
    }

    public function setPricePerKm(int $price_per_km): self
    {
        $this->price_per_km = $price_per_km;

        return $this;
    }

    /**
     * @return Collection|Rental[]
     */
    public function getRentals(): Collection
    {
        return $this->rentals;
    }

    public function addRental(Rental $rental): self
    {
        if (!$this->rentals->contains($rental)) {
            $this->rentals[] = $rental;
            $rental->setCar($this);
        }

        return $this;
    }

    public function removeRental(Rental $rental): self
    {
        if ($this->rentals->removeElement($rental)) {
            // set the owning side to null (unless already changed)
            if ($rental->getCar() === $this) {
                $rental->setCar(null);
            }
        }

        return $this;
    }
}
