<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    const MAX_EVENT_SUBSCRIBER = 5;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end_date;

    /**
     * @ORM\OneToMany(targetEntity=EventSubscriber::class, mappedBy="event")
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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): self
    {
        $this->end_date = $end_date;

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
            $eventSubscriber->setEvent($this);
        }

        return $this;
    }

    public function removeEventSubscriber(EventSubscriber $eventSubscriber): self
    {
        if ($this->eventSubscribers->removeElement($eventSubscriber)) {
            // set the owning side to null (unless already changed)
            if ($eventSubscriber->getEvent() === $this) {
                $eventSubscriber->setEvent(null);
            }
        }

        return $this;
    }
}
