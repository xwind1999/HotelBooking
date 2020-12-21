<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoomRepository::class)
 */
class Room implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=PriceList::class, mappedBy="room")
     */
    private $priceLists;

    /**
     * @ORM\OneToMany(targetEntity=Availability::class, mappedBy="room")
     */
    private $availabilities;

    /**
     * @ORM\ManyToMany(targetEntity=Booking::class, mappedBy="room")
     */
    private $bookings;

    public function __construct()
    {
        $this->priceLists = new ArrayCollection();
        $this->availabilities = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|PriceList[]
     */
    public function getPriceLists(): Collection
    {
        return $this->priceLists;
    }

    public function addPriceList(PriceList $priceList): self
    {
        if (!$this->priceLists->contains($priceList)) {
            $this->priceLists[] = $priceList;
            $priceList->setRoom($this);
        }

        return $this;
    }

    public function removePriceList(PriceList $priceList): self
    {
        if ($this->priceLists->removeElement($priceList)) {
            // set the owning side to null (unless already changed)
            if ($priceList->getRoom() === $this) {
                $priceList->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Availability[]
     */
    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function addAvailability(Availability $availability): self
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities[] = $availability;
            $availability->setRoom($this);
        }

        return $this;
    }

    public function removeAvailability(Availability $availability): self
    {
        if ($this->availabilities->removeElement($availability)) {
            // set the owning side to null (unless already changed)
            if ($availability->getRoom() === $this) {
                $availability->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->addRoom($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            $booking->removeRoom($this);
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        // TODO: Implement jsonSerialize() method.
        return get_object_vars($this);
    }
}
