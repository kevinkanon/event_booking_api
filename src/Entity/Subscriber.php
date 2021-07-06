<?php

namespace App\Entity;

use App\Repository\SubscriberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriberRepository::class)
 */
class Subscriber
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone_number;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=EventSubscriber::class, mappedBy="subscriber")
     */
    private $eventSubscribers;

    public function __construct()
    {
        $this->eventSubscribers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|EventSubscriber[]
     */
    public function getEventSubscribers(): Collection
    {
        return $this->eventSubscribers;
    }

    public function addEventSubscriber(EventSubscriber $eventSubscriber): self
    {
        if (!$this->eventSubscribers->contains($eventSubscriber)) {
            $this->eventSubscribers[] = $eventSubscriber;
            $eventSubscriber->setSubscriber($this);
        }

        return $this;
    }

    public function removeEventSubscriber(EventSubscriber $eventSubscriber): self
    {
        if ($this->eventSubscribers->removeElement($eventSubscriber)) {
            // set the owning side to null (unless already changed)
            if ($eventSubscriber->getSubscriber() === $this) {
                $eventSubscriber->setSubscriber(null);
            }
        }

        return $this;
    }
}
