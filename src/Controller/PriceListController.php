<?php

namespace App\Controller;

use App\Entity\PriceList;
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

class PriceListController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/price/list", name="price_list")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PriceListController.php',
        ]);
    }

    /**
     * @Route("/new/pricelist", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function newPriceList(Request $request):Response
    {
        $data = json_decode($request->getContent(), true);
        $room_id = json_encode($data['room_id']);
        $date = new DateTime($data['date']);
        $price = $data['price'];
        if (empty($room_id) || empty($date) || empty($price)){
            throw new NotFoundHttpException('Missing argument');
        }
        $priceList = new PriceList();
        $priceList->setRoom($this->entityManager->getRepository(Room::class)->find($room_id))
            ->setDate($date)
            ->setPrice($price);
        $this->entityManager->persist($priceList);
        $this->entityManager->flush();
        return $this->json("Import room successfully");
    }

    /**
     * @Rest\Put("/update/pricelist")
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function updatePriceList(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $priceListId = $content["id"];
        $priceList = $this->entityManager->getRepository(PriceList::class)->find($priceListId);
        empty($content["date"]) ? true : $priceList->setDate(new DateTime($content["date"]));
        empty($content["price"]) ? true : $priceList->setPrice($content["price"]);
        empty($content["room_id"]) ? true : $priceList->setRoom($this->entityManager->getRepository(Room::class)->find($roomId));
        $this->entityManager->persist($priceList);
        $this->entityManager->flush();
        return new JsonResponse($priceList);
    }

    /**
     * @Rest\Get("/pricelists")
     */
    public function getAllPriceLists(): JsonResponse
    {
        $priceLists = $this->entityManager->getRepository(PriceList::class)->findAll();
        return new JsonResponse($priceLists);
    }
}
