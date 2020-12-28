<?php

namespace App\Controller;

use App\Manager\RepositoryManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    private $entityManager;
    private $repositoryManager;

    public function __construct(EntityManagerInterface $entityManager, RepositoryManager $repositoryManager)
    {
        $this->entityManager = $entityManager;
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * @Route("/customer", name="customer")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CustomerController.php',
        ]);
    }

    /**
     * @Route("/new/customer", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function newCustomer(Request $request): Response
    {
        $this->repositoryManager->newCustomer($request);
        return $this->json("Import customer successfully");
    }

    /**
     * @Route("/customers", methods={"GET"})
     * @return Response
     */
    public function getAllCustomers(): Response
    {
        return new JsonResponse($this->repositoryManager->getAllCustomers());
    }

    /**
     * @Rest\Get("/customer/{customerId}")
     * @param $customerId
     * @return JsonResponse
     */
    public function getCustomerById($customerId): JsonResponse
    {
        return new JsonResponse($this->repositoryManager->findCustomerById($customerId));
    }



    /**
     * @Rest\Get("/search/customer")
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomByName(Request $request): JsonResponse
    {
        return new JsonResponse($this->repositoryManager->findCustomer($request));
    }

    /**
     * @Rest\Put("/update/customer")
     * @param Request $request
     * @return JsonResponse
     */
    public function updateCustomerById(Request $request): JsonResponse
    {
        return new JsonResponse($this->repositoryManager->updateCustomer($request));
    }

    /**
     * @Rest\Delete("delete/customer/{customerId}")
     * @param $customerId
     * @return JsonResponse
     */
    public function deleteCustomerById($customerId): JsonResponse
    {
        $this->deleteCustomerById($customerId);
        return $this->json("Delete Customer Number".$customerId);
    }
}
