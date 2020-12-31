<?php

namespace App\Controller;

use App\Manager\RepositoryManager;
use App\Repository\BookingRoomRepository;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingRoomController extends AbstractController
{
    /**
     * @var BookingRoomRepository
     */
    private $repositoryManager;

    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
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
     * @Rest\Get("/bookingrooms")get
     */
    public function getAllBookingRooms(): JsonResponse
    {
        return new JsonResponse($this->repositoryManager->getAllBookingRooms());
    }

    /**
     * @param Request $request
     * @Rest\Post("/complete/booking")
     * @return JsonResponse
     * @throws Exception
     */
    public function newCompleteBooking(Request $request): JsonResponse
    {
        $this->repositoryManager->bookingRoom($request);
        return $this->json("Insert Booking Room successfully");
    }
}
