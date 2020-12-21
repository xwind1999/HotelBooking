<?php

namespace App\Controller;

use App\Entity\Availability;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvailabilityController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/availability", name="availability")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AvailabilityController.php',
        ]);
    }

    /**
     * @Route("/availabilities", methods={"GET"})
     */
    public function getAllAvailabilities(): JsonResponse
    {
        $availabilities = $this->entityManager->getRepository(Availability::class)->findAll();
        return new JsonResponse($availabilities);
    }
}
