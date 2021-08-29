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
use App\Infrastructure\Security\AuthServiceInterface;
use App\Infrastructure\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ClientsController extends AbstractController
{
    public function __construct(private UserTransformer $transformer,
                                private SerializerInterface $serializer,
                                private OrderRepositoryInterface $orderRepository,
                                private ClientRepositoryInterface $clientRepository,
                                private OrderTransformer $orderTransformer,
                                private ValidatorInterface $validator,
                                private AuthServiceInterface $auth
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
            $client = Client::create(Uuid::uuid4()->toString(), $request->email(), $request->password());
            $this->clientRepository->add($client);
            $this->clientRepository->saveChanges();
            $userResponse = $this->transformer->transform($client);
            $response = $this->serializer->serialize($userResponse, 'json');
            return new JsonResponse($response, json: true);
        }
        return $this->json($errors, 400);
    }

    /**
     * @param OrderRequest $request
     * @param string $id
     * @return JsonResponse
     * @IsGranted("ROLE_CLIENT")
     */
    #[Route(path: '/clients/{id}/orders', name: 'clients.orders.create',methods: ["POST"])]
    public function makeOrder(OrderRequest $request, string $id): JsonResponse
    {
        if($this->auth->getAuthUser()->getId() != $id || !$this->auth->getAuthUser() instanceof Client)
        {
            return $this->json(['error' => 'forbidden'], 403);
        }
        try {
            $order = $this->auth->getAuthUser()->makeOrder(Uuid::uuid4()->toString(),
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

            $this->orderRepository->add($order);
            $this->orderRepository->saveChanges();
            $orderResponse = $this->orderTransformer->transform($order);
            $response = $this->serializer->serialize($orderResponse, 'json');
            return new JsonResponse($response, json: true);
        } catch (AccountNotActivatedException $e) {
        }
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    #[IsGranted(data: "ROLE_CLIENT", message: "Only clients can list their orders", statusCode: 403)]
    #[Route(path: '/client/{id}/orders', name: "client.orders.list" ,methods: ['GET'])]
    public function getAllAvailableOrders(string $id)
    {
        if($this->auth->getAuthUser()->getId() != $id)
        {
            return $this->json(['error' => 'forbidden'], 403);
        }
        $orders = $this->orderRepository->findClientOrders($id);
        $ordersResponse = $this->orderTransformer->transform($orders);
        $response = $this->serializer->serialize($ordersResponse, 'json');
        return new JsonResponse($response, json: true);
    }
}