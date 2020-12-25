<?php

namespace App\Controller;

use App\Entity\Availability;
use App\Entity\Room;
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

    /**
     * @Rest\Post("/new/availability")
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function newAvailability(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $date = new DateTime($data['date']);
        $stock = $data['stock'];
        $stop_sale = $data['stop_sale'];
        $room_id = $data['room_id'];
        if (empty($date) || empty($stock) || empty($stop_sale) || empty($room_id)){
            throw new NotFoundHttpException('Missing argument');
        }
        $availability = new Availability();
        $availability->setDate($date)
            ->setStopSale($stop_sale)
            ->setStock($stock)
            ->setRoom($this->entityManager->getRepository(Room::class)->find($room_id));
        $this->entityManager->persist($availability);
        $this->entityManager->flush();
        return $this->json("Import availability successfully");
    }

    /**
     * @Rest\Put("update/availability")
     * @param Request $request
     * @throws Exception
     */
    public function updateAvailability(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $availabilityId = $content["id"];
        $availability = $this->entityManager->getRepository(Availability::class)->find($availabilityId);
        empty($content["date"]) ? true : $availability->setDate(new DateTime($content["date"]));
        empty($content["stock"]) ? true : $availability->setStock($content["stock"]);
        empty($content["stop_sale"]) ? true : $availability->setStopSale($content["stop_sale"]);
        empty($content["room_id"]) ? true : $availability->setRoom($this->entityManager->getRepository(Room::class)->find($content["room_id"]));
        $this->entityManager->persist($availability);
        $this->entityManager->flush();
        return new JsonResponse($availability);
    }
}
