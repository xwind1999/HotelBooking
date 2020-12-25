<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Customer;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/booking", name="booking")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/BookingController.php',
        ]);
    }

    /**
     * @Route("/bookings", methods={"GET"})
     */
    public function getAllBookings(): JsonResponse
    {
        $bookings = $this->entityManager->getRepository(Booking::class)->findAll();
        return new JsonResponse($bookings);
    }

    /**
     * @Rest\Post("/new/booking")
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function newBooking(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $customerId = $data["customer_id"];
        $totalPrice = $data["total_price"];
        $bookTime = $data["book_time"];

        if (empty($customerId) || empty($totalPrice) || empty($bookTime)){
            throw new NotFoundHttpException('Missing argument');
        }
        $booking = new Booking();
        $booking->setTotalPrice($totalPrice)
            ->setCustomer($this->entityManager->getRepository(Customer::class)->find($customerId))
            ->setBookTime(new DateTime($bookTime));
        $this->entityManager->persist($booking);
        $this->entityManager->flush();
        return $this->json("Import booking successfully");
    }
}
