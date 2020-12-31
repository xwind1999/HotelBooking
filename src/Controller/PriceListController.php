<?php

namespace App\Controller;

use App\Entity\PriceList;
use App\Manager\RepositoryManager;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PriceListController extends AbstractController
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
     * @Rest\Get("/pricelist/{id}")
     * @param $id
     */
    public function getPriceListById($id): JsonResponse
    {
        return new JsonResponse($this->repositoryManager->findPriceListById($id));
    }

    /**
     * @Route("/new/pricelist", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function newPriceList(Request $request):Response
    {
       $this->repositoryManager->newPriceList($request);
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
        $this->repositoryManager->updatePriceList($request);
        return $this->json("Update price list successfully");
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
