<?php

namespace App\Controller;

use App\Manager\RepositoryManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{
    private $entityManager;
    /**
     * @var RepositoryManager
     */
    private $repositoryManager;

    public function __construct(RepositoryManager $repositoryManager)
    {
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * @Route("/room", name="room")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/RoomController.php',
        ]);
    }

    /**
     * @Route("/rooms", methods={"GET"})
     */
    public function getAllRooms(): Response
    {
        return new JsonResponse($this->repositoryManager->getAllRooms());
    }

    /**
     * @Route("/room/new", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function newRoom(Request $request):Response
    {
        $this->repositoryManager->newRoom($request);
        return $this->json("Import room successfully");
    }

    /**
     * @Rest\Put("/room/update")
     * @param Request $request
     * @return JsonResponse
     */
    public function updateRoom(Request $request): JsonResponse
    {
        $this->repositoryManager->updateRoom($request);
        return $this->json("Update room successfully");
    }

    /**
     * @Rest\Delete("delete/room/{id}")
     * @param $id
     * @return JsonResponse
     */
    public function deleteRoom($id): JsonResponse
    {
        $this->deleteRoom($id);
        return $this->json("Delete room number ".$id);
    }
}
