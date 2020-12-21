<?php

namespace App\Controller;

use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
}
