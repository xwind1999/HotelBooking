<?php

namespace App\Controller;

use App\Manager\RepositoryManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvailabilityController extends AbstractController
{
    /**
     * @var RepositoryManager
     */
    private $repositoryManager;

    public function __construct(RepositoryManager $repositoryManager, EntityManagerInterface $entityManager)
    {
        $this->repositoryManager = $repositoryManager;
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
        return new JsonResponse($this->repositoryManager->getAllAvailabilities());
    }

    /**
     * @Rest\Post("/new/availability")
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function newAvailability(Request $request): JsonResponse
    {
        $this->repositoryManager->newAvailability($request);
        return $this->json("Import availability successfully");
    }

    /**
     * @Rest\Put("update/availability")
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function updateAvailability(Request $request): JsonResponse
    {
        $this->repositoryManager->updateAvailability($request);
        return $this->json("Update availability successfully");
    }
}
