<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="booking")
     */
    private $customer;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalPrice;

    /**
     * @ORM\OneToMany(targetEntity=BookingRoom::class, mappedBy="booking_id", orphanRemoval=true)
     */
    private $bookingRooms;

    /**
     * @ORM\Column(type="datetime")
     */
    private $bookTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    public function __construct()
    {
        $this->bookingRooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return Collection|BookingRoom[]
     */
    public function getBookingRooms(): Collection
    {
        return $this->bookingRooms;
    }

    public function addBookingRoom(BookingRoom $bookingRoom): self
    {
        if (!$this->bookingRooms->contains($bookingRoom)) {
            $this->bookingRooms[] = $bookingRoom;
            $bookingRoom->setBookingId($this);
        }

        return $this;
    }

    public function removeBookingRoom(BookingRoom $bookingRoom): self
    {
        if ($this->bookingRooms->removeElement($bookingRoom)) {
            // set the owning side to null (unless already changed)
            if ($bookingRoom->getBookingId() === $this) {
                $bookingRoom->setBookingId(null);
            }
        }

        return $this;
    }

    public function getBookTime(): ?\DateTimeInterface
    {
        return $this->bookTime;
    }

    public function setBookTime(\DateTimeInterface $bookTime): self
    {
        $this->bookTime = $bookTime;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
