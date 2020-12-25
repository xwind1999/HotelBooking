<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\BookingRoom;
use App\Entity\Room;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingRoomController extends AbstractController
{
    private $entity_manager;

    public function __construct(EntityManagerInterface $entity_manager)
    {
        $this->entity_manager = $entity_manager;
    }
    /**
     * @Route("/booking/room", name="booking_room")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/BookingRoomController.php',
        ]);
    }

    /**
     * @Rest\Get("/bookingrooms")
     */
    public function getAllBookingRooms(): JsonResponse
    {
        $bookings = $this->entity_manager->getRepository(BookingRoom::class)->findAll();
        return new JsonResponse($bookings);
    }

    /**
     * @Rest\Post("/new/bookingroom")
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function newBookingRoom(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(),true);
        foreach ($content as $item) {
            $booking_room = new BookingRoom();
            $booking_room->setBookingId($this->entity_manager->getRepository(Booking::class)->find($item['booking_id_id']))
                ->setRoomId($this->entity_manager->getRepository(Room::class)->find($item['room_id_id']))
                ->setNumber($item['number'])
                ->setStartDate(new DateTime($item['start_date']))
                ->setEndDate(new DateTime($item['end_date']));
            $this->entity_manager->persist($booking_room);
        }
        $this->entity_manager->flush();
        return new JsonResponse("Import Booking room successfully");
    }


}
