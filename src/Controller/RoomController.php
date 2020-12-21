<?php

namespace App\Controller;

use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $rooms = $this->entityManager->getRepository(Room::class)->findAll();
        return new JsonResponse($rooms);
    }

    /**
     * @Route("/room/new", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function newRoom(Request $request):Response
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $description = $data['description'];
        if (empty($name) || empty($description)){
            throw new NotFoundHttpException('Missing argument');
        }
        $room = new Room();
        $room->setName($name)
            ->setDescription($description);
        $this->entityManager->persist($room);
        $this->entityManager->flush();
        return $this->json("Import room successfully");
    }
}
