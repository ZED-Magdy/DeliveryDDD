<?php


namespace App\UI\Controller;


use App\Application\Request\OrderRequest;
use App\Application\Request\UserRequest;
use App\Application\Transformers\OrderTransformer;
use App\Application\Transformers\UserTransformer;
use App\Domain\Exception\AccountNotActivatedException;
use App\Domain\Model\Client;
use App\Domain\Model\Place;
use App\Domain\Repository\ClientRepositoryInterface;
use App\Domain\Repository\OrderRepositoryInterface;
use App\Infrastructure\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientsController extends AbstractController
{
    public function __construct(private UserTransformer $transformer,
                                private SerializerInterface $serializer,
                                private EntityManagerInterface $em,
                                private ClientRepositoryInterface $clientRepository,
                                private OrderRepositoryInterface $orderRepository,
                                private OrderTransformer $orderTransformer,
                                private ValidatorInterface $validator,
                                private UserPasswordHasherInterface $hasher
    )
    {
    }

    /**
     * @param UserRequest $request
     * @return JsonResponse
     */
    #[Route(path: '/clients', name: 'clients.register',methods: ["POST"])]
    public function registerAClient(UserRequest $request): JsonResponse
    {
        $errors = $this->validator->validate($request);
        if($errors->count() == 0)
        {
            $id = Uuid::uuid4()->toString();
            $client = Client::create($id, $request->email(), $request->password());
            //TODO: Add Domain Events in the User Domain Model so when User Created/Updated
            //The event will be fired and update the security user as well
            $securityUser = new User(Uuid::uuid4()->toString(), $request->email(), ["ROLE_CLIENT"]);
            $securityUser->setHashedPassword($this->hasher->hashPassword($securityUser, $request->password()));
            $this->em->persist($client);
            $this->em->persist($securityUser);
            $this->em->flush();
            $userResponse = $this->transformer->transform($client);
            $response = $this->serializer->serialize($userResponse, 'json');
            return new JsonResponse($response, json: true);
        }
        return $this->json($errors, 400);
    }

    #[Route(path: '/clients/{id}/orders', name: 'clients.orders.create',methods: ["POST"])]
    public function makeOrder(OrderRequest $request, string $id): JsonResponse
    {
        //Imagine we get the Authenticated Client not fetching it from db
        $client = $this->clientRepository->findClientById($id);
        try {
            $order = $client->makeOrder(Uuid::uuid4()->toString(),
                new Place(
                    $request->orderPlace()->name(),
                    $request->orderPlace()->longitude(),
                    $request->orderPlace()->latitude(),
                    $request->orderPlace()->address()
                ),
                new Place(
                    $request->dropPlace()->name(),
                    $request->dropPlace()->longitude(),
                    $request->dropPlace()->latitude(),
                    $request->dropPlace()->address()
                ),
                $request->note()
            );

            $this->em->persist($order);
            $this->em->flush();
            $orderResponse = $this->orderTransformer->transform($order);
            $response = $this->serializer->serialize($orderResponse, 'json');
            return new JsonResponse($response, json: true);
        } catch (AccountNotActivatedException $e) {
        }
    }

    #[Route(path: '/client/{id}/orders', name: "client.orders.list" ,methods: ['GET'])]
    public function getAllAvailableOrders(string $id)
    {
        $orders = $this->orderRepository->findClientOrders($id);
        $ordersResponse = $this->orderTransformer->transform($orders);
        $response = $this->serializer->serialize($ordersResponse, 'json');
        return new JsonResponse($response, json: true);
    }
}