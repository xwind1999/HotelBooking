<?php

namespace App\Controller;

use App\Manager\RepositoryManager;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    private $repositoryManager;

    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
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
     * @Rest\Post("/changebookingstatus")
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function changeBookingStatus(Request $request): JsonResponse
    {
        $this->repositoryManager->changeBookingStatus($request);
        return $this->json("Change booking status successfully");
    }

    /**
     * @Route("/bookings", methods={"GET"})
     */
    public function getAllBookings(): JsonResponse
    {
        return new JsonResponse($this->repositoryManager->getAllBookings());
    }

}
