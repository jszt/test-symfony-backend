<?php

namespace App\Entity;

use App\Repository\RentalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RentalRepository::class)
 */
class Rental
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Car::class, inversedBy="rentals")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $Car;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 10,
     *      maxMessage = "the start date cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */
    private $start_date;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 10,
     *      maxMessage = "The end date cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     */
    private $end_date;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     */
    private $distance;

    /**
     * @ORM\OneToMany(targetEntity=Action::class, mappedBy="rental", orphanRemoval=true)
     */
    private $actions;

    /**
     * @ORM\ManyToMany(targetEntity=Option::class, mappedBy="rentals")
     */
    private $options;

    public function __construct()
    {
        $this->actions = new ArrayCollection();
        $this->options = new ArrayCollection();
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

    public function getCar(): ?Car
    {
        return $this->Car;
    }

    public function setCar(?Car $Car): self
    {
        $this->Car = $Car;

        return $this;
    }

    public function getStartDate(): ?string
    {
        return $this->start_date;
    }

    public function setStartDate(string $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?string
    {
        return $this->end_date;
    }

    public function setEndDate(string $end_date): self
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(int $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * @return Collection|Action[]
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(Action $action): self
    {
        if (!$this->actions->contains($action)) {
            $this->actions[] = $action;
            $action->setRental($this);
        }

        return $this;
    }

    public function removeAction(Action $action): self
    {
        if ($this->actions->removeElement($action)) {
            // set the owning side to null (unless already changed)
            if ($action->getRental() === $this) {
                $action->setRental(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Option[]
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->addRental($this);
        }

        return $this;
    }

    public function removeOption(Option $option): self
    {
        if ($this->options->removeElement($option)) {
            $option->removeRental($this);
        }

        return $this;
    }
}
