<?php


namespace App\Manager;


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
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class RepositoryManager
{
    const DATEFORMAT = 'd-m-Y';
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

    /**
     * @return array
     */
    public function getAllBookingRooms(): array
    {
        return $this->bookingRoomRepository->findAll();
    }

    /**
     * @return array
     */
    public function getAllCustomers(): array
    {
        return $this->customerRepository->findAll();
    }

    /**
     * @return array
     */
    public function getAllPriceLists(): array
    {
        return $this->priceListRepository->findAll();
    }

    /**
     * @return array
     */
    public function getAllRooms(): array
    {
        return $this->roomRepository->findAll();
    }

    /**
     * @return array
     */
    public function getAllBookings(): array
    {
        return $this->bookingRepository->findAll();
    }

    /**
     * @return array
     */
    public function getAllAvailabilities(): array
    {
        return $this->availabilityRepository->findAll();
    }

    /**
     * @param Request $request
     */
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

    /**
     * @param Request $request
     * @return mixed
     */
    private function decodeRequest(Request $request)
    {
        return json_decode($request->getContent(),true);
    }

    /**
     * @param $id
     * @return Customer|null
     */
    public function findCustomerById($id): ?Customer
    {
        return $this->customerRepository->find($id);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function findCustomer(Request $request): array
    {
        $requestJson = $this->decodeRequest($request);
        return $this->customerRepository->findBy(["name" => $requestJson["name"] ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * @param $customerId
     */
    public function deleteCustomerById($customerId)
    {
        $customer = $this->customerRepository->find($customerId);
        $this->entityManager->remove($customer);
        $this->entityManager->flush();
    }

    /**
     * @param $id
     * @return Room|null
     */
    public function findRoomById($id): ?Room
    {
        return $this->roomRepository->find($id);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function bookingRoom(Request $request){
        $requestJson = $this->decodeRequest($request);
        $userId = $requestJson["user_id"];
        $bookTime = new DateTime($requestJson["book_time"]);
        $bookings = $requestJson["bookings"];
        $totalPrice = 0;

        // Create a new Booking
        $booking = new Booking();
        $booking->setCustomer(
            $this->findCustomerById($userId)
        )
            ->setBookTime($bookTime)
            ->setStatus(0);

        // booking_room list
        foreach ($bookings as $item) {
            $dateOfBookings = $this->getSchedule(new DateTime($item['start_date']), new DateTime($item['end_date']));

            // Check if booking status is OK
            if($this->isAvailabilities($dateOfBookings, $item['number'], $item['room_id_id']))
            {

                // Add new Booking Room
                $bookingRoom = new BookingRoom();
                $bookingRoom->setBookingId($booking)
                    ->setRoomId($this->findRoomById($item['room_id_id']))
                    ->setNumber($item['number'])
                    ->setStatus(0)
                    ->setStartDate(new DateTime($item['start_date']))
                    ->setEndDate(new DateTime($item['end_date']));

                // Calculate the total price
                foreach ($dateOfBookings as $dateOfBooking) {
                    $totalPrice += $this->priceListRepository->findOneBy(array(
                            'room' => $this->findRoomById($item['room_id_id']),
                            'date' => new DateTime($dateOfBooking)
                        ))->getPrice() * $item['number'];
                    // Change Availability
//                    $this->changeAvailability($item['room_id_id'], $item['number'], new DateTime($dateOfBooking));
                }
                $this->entityManager->persist($bookingRoom);
            }
            else
            {
                $roomId = $item['room_id_id'];
                $quantity = $item['number'];
                $startDate = $item['start_date'];
                $endDate = $item['end_date'];
                $this->refuseBooking($roomId, $quantity, $startDate, $endDate);
            }
        }

        //Set Total Price
        $booking->setTotalPrice($totalPrice);
        $this->entityManager->persist($booking);
        $this->entityManager->flush();
    }

    /**
     * @param $roomId
     * @param $quantity
     * @param $startDate
     * @param $endDate
     */
    private function refuseBooking($roomId, $quantity, $startDate, $endDate)
    {
        echo("Booking not complete" . PHP_EOL);
        echo("Room : " . $this->roomRepository->find($roomId)->getName() . PHP_EOL);
        echo("Quantity : " . $quantity . PHP_EOL);
        echo("Start Date : " . $startDate . PHP_EOL);
        echo("End Date : " . $endDate.PHP_EOL);
    }

    /**
     * @param int $roomId
     * @param int $quantity
     * @param DateTime $time
     */
    public function changeAvailability(int $roomId, int $quantity, DateTime $time)
    {
        $availability = $this->availabilityRepository->findOneBy([
            "room" => $this->findRoomById($roomId),
            "date" => $time
        ]);
        $availability->setStock($availability->getStock() - $quantity);
        $this->entityManager->persist($availability);
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return array
     */
    public function getSchedule(DateTime $startDate, DateTime $endDate): array
    {
        $dates = [];
        $interval = date_diff($startDate, $endDate)->days;
        for ($i = 0; $i <= $interval ; $i++){
            $dates[] = date(self::DATEFORMAT, strtotime($startDate->format(self::DATEFORMAT) ."+".$i." day"));
        }
        return $dates;
    }

    /**
     * Check if booking condition is passed
     * @param array $dates
     * @param $quantity
     * @param $roomId
     * @return bool
     * @throws Exception
     */
    private function isAvailabilities(array $dates, $quantity, $roomId): bool
    {
        foreach ($dates as $date) {
            $availability = $this->availabilityRepository->findOneBy([
                "date"=>new DateTime($date),
                "room"=>$this->findRoomById($roomId)
            ]);
            if ($quantity > $availability->getStock() || !$availability->getStopSale())
            {
                return false;
            }
        }
        return true;
    }

    /**
     * @param Request $request
     */
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

    /**
     * @param Request $request
     * @return Room|null
     */
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

    /**
     * @param $id
     */
    public function deleteRoom($id)
    {
        $room = $this->roomRepository->find($id);
        $this->entityManager->remove($room);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function newAvailability(Request $request)
    {
        $requestJson = $this->decodeRequest($request);
        $date = new DateTime($requestJson['date']);
        $stock = $requestJson['stock'];
        $stopSale = $requestJson['stop_sale'];
        $roomId = $requestJson['room_id'];
        if (empty($date) || empty($stock) || empty($stopSale) || empty($roomId)){
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

    /**
     * @param Request $request
     * @throws Exception
     */
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

    /**
     * @param $id
     * @return PriceList|null
     */
    public function findPriceListById($id): ?PriceList
    {
        return $this->priceListRepository->find($id);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function newPriceList(Request $request)
    {
        $responseJson = $this->decodeRequest($request);
        $roomId = $responseJson['room_id'];
        $date = new DateTime($responseJson['date']);
        $price = $responseJson['price'];

        if (empty($roomId) || empty($date) || empty($price)){
            throw new NotFoundHttpException('Missing argument');
        }
        $priceList = new PriceList();
        $priceList->setRoom($this->findRoomById($roomId))
            ->setDate($date)
            ->setPrice($price);
        $this->entityManager->persist($priceList);
        $this->entityManager->flush();
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    public function updatePriceList(Request $request)
    {
        $responseJson = $this->decodeRequest($request);
        $priceListId = $responseJson["id"];
        /** @var PriceList $priceList */
        $priceList = $this->findRoomById($priceListId);
        empty($responseJson["date"]) ? true : $priceList->setDate(new DateTime($responseJson["date"]));
        empty($responseJson["price"]) ? true : $priceList->setPrice($responseJson["price"]);
        empty($responseJson["room_id"]) ? true : $priceList->setRoom($this->findRoomById($responseJson["room_id"]));
        $this->entityManager->persist($priceList);
        $this->entityManager->flush();
    }

    /**
     * Change the booking status
     * @param Request $request
     * @throws Exception
     */
    public function changeBookingStatus(Request $request)
    {
        $requestJson = $this->decodeRequest($request);
        $bookingId = $requestJson["booking_id"];
        $status = $requestJson["status"];

        // set status
        $this->bookingRepository->find($bookingId)->setStatus($status);
        $bookingRoomOfBooking = $this->bookingRoomOfBooking($bookingId);
        /** @var BookingRoom $bookingRoom */
        foreach ($bookingRoomOfBooking as $bookingRoom)
        {
            $dateOfBookings = $this->getSchedule($bookingRoom->getStartDate(), $bookingRoom->getEndDate());
            foreach ($dateOfBookings as $dateOfBooking) {
                // Change Availability
                $this->changeAvailability($bookingRoom->getRoomId()->getId(), $bookingRoom->getNumber(), new DateTime($dateOfBooking));
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @param int $bookingId
     * @return array
     */
    public function bookingRoomOfBooking(int $bookingId): array
    {
        return $this->bookingRoomRepository->findBy([
            "booking_id" => $bookingId
        ]);
    }
}