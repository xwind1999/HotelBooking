<?php


namespace App\Manager;


use App\Controller\RoomController;
use App\Entity\Availability;
use App\Entity\Booking;
use App\Entity\BookingRoom;
use App\Entity\Customer;
use App\Entity\PriceList;
use App\Entity\Room;
use App\Repository\AvailabilityRepository;
use App\Repository\BookingRepository;
use App\Repository\BookingRoomRepository;
use App\Repository\CustomerRepository;
use App\Repository\PriceListRepository;
use App\Repository\RoomRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RepositoryManager
{
    /**
     * @var AvailabilityRepository
     */
    private $availabilityRepository;
    /**
     * @var RoomRepository
     */
    private $roomRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @return AvailabilityRepository
     */
    public function getAvailabilityRepository(): AvailabilityRepository
    {
        return $this->availabilityRepository;
    }

    /**
     * @return BookingRoomRepository
     */
    public function getBookingRoomRepository(): BookingRoomRepository
    {
        return $this->bookingRoomRepository;
    }

    /**
     * @return CustomerRepository
     */
    public function getCustomerRepository(): CustomerRepository
    {
        return $this->customerRepository;
    }

    /**
     * @return PriceListRepository
     */
    public function getPriceListRepository(): PriceListRepository
    {
        return $this->priceListRepository;
    }

    /**
     * @return BookingRepository
     */
    public function getBookingRepository(): BookingRepository
    {
        return $this->bookingRepository;
    }
    /**
     * @var BookingRoomRepository
     */
    private $bookingRoomRepository;
    /**
     * @var CustomerRepository
     */
    private $customerRepository;
    /**
     * @var PriceListRepository
     */
    private $priceListRepository;
    /**
     * @var BookingRepository
     */
    private $bookingRepository;

    public function __construct(BookingRoomRepository $bookingRoomRepository,
                                CustomerRepository $customerRepository,
                                RoomRepository $roomRepository,
                                BookingRepository $bookingRepository,
                                AvailabilityRepository $availabilityRepository,
                                PriceListRepository $priceListRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->bookingRoomRepository = $bookingRoomRepository;
        $this->customerRepository = $customerRepository;
        $this->roomRepository = $roomRepository;
        $this->bookingRepository = $bookingRepository;
        $this->availabilityRepository = $availabilityRepository;
        $this->priceListRepository = $priceListRepository;
    }

    public function getAllBookingRooms(): array
    {
        return $this->bookingRoomRepository->findAll();
    }

    public function getAllCustomers(): array
    {
        return $this->customerRepository->findAll();
    }

    public function getAllPriceLists(): array
    {
        return $this->priceListRepository->findAll();
    }

    public function getAllRooms(): array
    {
        return $this->roomRepository->findAll();
    }

    public function getAllBookings(): array
    {
        return $this->bookingRepository->findAll();
    }

    public function getAllAvailabilities(): array
    {
        return $this->availabilityRepository->findAll();
    }

    public function newCustomer(Request $request)
    {
        $requestJson = $this->decodeRequest($request);
        $name = $requestJson['name'];
        $address = $requestJson['address'];
        if (empty($name) || empty($address)){
            throw new NotFoundHttpException('Missing argument');
        }
        $customer = new Customer();
        $customer->setName($name)
            ->setAddress($address);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
    }

    private function decodeRequest(Request $request)
    {
        return json_decode($request->getContent(),true);
    }

    public function findCustomerById($id): ?Customer
    {
        return $this->customerRepository->find($id);
    }

    public function findCustomer(Request $request): array
    {
        $requestJson = $this->decodeRequest($request);
        return $this->customerRepository->findBy(["name" => $requestJson["name"] ]);
    }

    public function updateCustomer(Request $request): JsonResponse
    {
        $requestJson = $this->decodeRequest($request);
        $customerId = $requestJson["id"];
        $customer = $this->customerRepository->find($customerId);
        empty($requestJson["name"]) ? true : $customer->setName($requestJson["name"]);
        empty($requestJson["address"]) ? true : $customer->setAddress($requestJson["address"]);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        return new JsonResponse($customer);
    }

    public function deleteCustomerById($customerId)
    {
        $customer = $this->customerRepository->find($customerId);
        $this->entityManager->remove($customer);
        $this->entityManager->flush();
    }

    public function findRoomById($id): ?Room
    {
        return $this->roomRepository->find($id);
    }

    public function bookingRoom(Request $request){
        $requestJson = $this->decodeRequest($request->getContent(), true);
        $userId = $requestJson["user_id"];
        $book_time = new DateTime($requestJson["book_time"]);
        $bookings = $requestJson["bookings"];
        $totalPrice = 0;
        // New Booking object
        $booking = new Booking();
        $booking->setCustomer(
            $this->findCustomerById($userId)
        )
            ->setBookTime($book_time);
        //booking_room list
        foreach ($bookings as $item) {
            $booking_room = new BookingRoom();
            $booking_room->setBookingId($booking)
                ->setRoomId($this->findRoomById($item['room_id_id']))
                ->setNumber($item['number'])
                ->setStartDate(new DateTime($item['start_date']))
                ->setEndDate(new DateTime($item['end_date']));
            //calculate the total price
            $totalPrice += $this->priceListRepository->findOneBy(array(
                    'room'=>$this->findRoomById($item['room_id_id']),
                    'date' => $book_time
                ))->getPrice() * $item['number'];
            $this->entityManager->persist($booking_room);
        }
        //Set Total Price
        $booking->setTotalPrice($totalPrice);
        $this->entityManager->persist($booking);
        $this->entityManager->flush();
    }

    public function newRoom(Request $request)
    {
        $responseJson = $this->decodeRequest($request);
        $name = $responseJson['name'];
        $description = $responseJson['description'];
        if (empty($name) || empty($description)){
            throw new NotFoundHttpException('Missing argument');
        }
        $room = new Room();
        $room->setName($name)
            ->setDescription($description);
        $this->entityManager->persist($room);
        $this->entityManager->flush();
    }

    public function updateRoom(Request $request): ?Room
    {
        $responseJson = $this->decodeRequest($request);
        $roomId = $responseJson["id"];
        $room = $this->findRoomById($roomId);
        empty($responseJson["name"]) ? true : $room->setName($responseJson["name"]);
        empty($responseJson["description"]) ? true : $room->setDescription($responseJson["description"]);
        $this->entityManager->persist($room);
        $this->entityManager->flush();
    }

    public function deleteRoom($id)
    {
        $room = $this->roomRepository->find($id);
        $this->entityManager->remove($room);
    }

    public function newAvailability(Request $request)
    {
        $requestJson = $this->decodeRequest($request);
        $date = new DateTime($requestJson['date']);
        $stock = $requestJson['stock'];
        $stopSale = $requestJson['stop_sale'];
        $roomId = $requestJson['room_id'];
        if (empty($date) || empty($stock) || empty($stop_sale) || empty($room_id)){
            throw new NotFoundHttpException('Missing argument');
        }
        $availability = new Availability();
        $availability->setDate($date)
            ->setStopSale($stopSale)
            ->setStock($stock)
            ->setRoom($this->findRoomById($roomId));
        $this->entityManager->persist($availability);
        $this->entityManager->flush();
    }

    public function updateAvailability(Request $request)
    {
        $requestJson = $this->decodeRequest($request);
        $availabilityId = $requestJson["id"];
        $availability = $this->availabilityRepository->find($availabilityId);
        empty($requestJson["date"]) ? true : $availability->setDate(new DateTime($requestJson["date"]));
        empty($requestJson["stock"]) ? true : $availability->setStock($requestJson["stock"]);
        empty($requestJson["stop_sale"]) ? true : $availability->setStopSale($requestJson["stop_sale"]);
        empty($requestJson["room_id"]) ? true : $availability->setRoom($this->findRoomById($requestJson["room_id"]));
        $this->entityManager->persist($availability);
        $this->entityManager->flush();

    }
}