<?php


namespace App\Controller;


use App\Application\Request\OrderRequest;
use App\Application\Request\UserRequest;
use App\Application\Transformers\OrderTransformer;
use App\Application\Transformers\UserTransformer;
use App\Domain\Exception\AccountNotActivatedException;
use App\Domain\Model\Client;
use App\Domain\Model\Place;
use App\Domain\Repository\ClientRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ClientsController extends AbstractController
{
    public function __construct(private UserTransformer $transformer,
                                private SerializerInterface $serializer,
                                private EntityManagerInterface $em,
                                private ClientRepositoryInterface $clientRepository,
                                private OrderTransformer $orderTransformer
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
        $client = Client::create($request->email(), $request->password());
        $this->em->persist($client);
        $this->em->flush();
        $userResponse = $this->transformer->transform($client);
        $response = $this->serializer->serialize($userResponse, 'json');
        return new JsonResponse($response, json: true);
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
}