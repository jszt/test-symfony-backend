<?php

namespace App\Entity;

use App\Repository\ActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ActionRepository::class)
 */
class Action
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "The actor cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     * @Groups({"rentals"})
     */
    private $actor;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "The type cannot be longer than {{ limit }} characters",
     *      allowEmptyString = false
     * )
     * @Groups({"rentals"})
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     * @Groups({"rentals"})
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity=Rental::class, inversedBy="actions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rental;

    public function __construct($actor, $type, $amount, $rental)
    {
        $this->type = $type;
        $this->actor = $actor;
        $this->amount = $amount;
        $this->rental = $rental;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActor(): ?string
    {
        return $this->actor;
    }

    public function setActor(string $actor): self
    {
        $this->actor = $actor;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getRental(): ?Rental
    {
        return $this->rental;
    }

    public function setRental(?Rental $rental): self
    {
        $this->rental = $rental;

        return $this;
    }
}
