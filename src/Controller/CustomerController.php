<?php

namespace App\Controller;

use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Customer::class);
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
     * @Route("/customer/new", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function newCustomer(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $address = $data['address'];
        if (empty($name) || empty($address)){
            throw new NotFoundHttpException('Missing argument');
        }
        $customer = new Customer();
        $customer->setName($name)
            ->setAddress($address);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        return $this->json("Import customer successfully");
    }

    /**
     * @Route("/customers", methods={"GET"})
     * @return Response
     */
    public function getAllCustomers(): Response
    {
        $customers = $this->entityManager->getRepository(Customer::class)->findAll();
        return new JsonResponse($customers);
    }

    /**
     * @Rest\Get("/customer/{customerId}")
     * @param $customerId
     * @return JsonResponse
     */
    public function getCustomerById($customerId): JsonResponse
    {
        $customer = $this->entityManager->getRepository(Customer::class)->find($customerId);
        return new JsonResponse($customer);
    }



    /**
     * @Rest\Get("/search/customer")
     * @param Request $request
     * @return JsonResponse
     */
    public function getCustomById(Request $request): JsonResponse
    {
        $customers = $this->entityManager->getRepository(Customer::class)->findOneBy(["name"=>$request->query->get("name")]);
        return new JsonResponse($customers);
    }

    /**
     * @Rest\Put("/update/customer")
     * @param Request $request
     * @return JsonResponse
     */
    public function updateCustomerById(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $customerId = $content["id"];
        $customer = $this->entityManager->getRepository(Customer::class)->find($customerId);
        empty($content["name"]) ? true : $customer->setName($content["name"]);
        empty($content["address"]) ? true : $customer->setAddress($content["address"]);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        return new JsonResponse($customer);
    }

    /**
     * @Rest\Delete("delete/customer/{customerId}")
     * @param $customerId
     * @return JsonResponse
     */
    public function deleteCustomerById($customerId): JsonResponse
    {
        $customer = $this->repository->find($customerId);
        $this->entityManager->remove($customer);
        $this->entityManager->flush();
        return $this->json("Delete Customer Number".$customerId);
    }
}
