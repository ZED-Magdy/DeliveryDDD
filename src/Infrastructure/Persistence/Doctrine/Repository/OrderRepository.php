<?php


namespace App\Infrastructure\Persistence\Doctrine\Repository;


use App\Domain\Model\Order;
use App\Domain\Repository\OrderRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository implements OrderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }
    /**
     * @inheritDoc
     */
    public function findClientOrders(string $clientId, int $orderStatus = Order::STATUS_DRAFT): array
    {
        $qb = $this->createQueryBuilder('o');
        return $qb
            ->where($qb->expr()->andX(
                $qb->expr()->eq('o.owner', ':owner'),
                $qb->expr()->eq('o.status', ':status')
            ))
            ->setParameter('owner', $clientId)
            ->setParameter('status', $orderStatus)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findDriverOrders(string $driverId, int $orderStatus): array
    {
        $qb = $this->createQueryBuilder('o');
        return $qb
            ->where($qb->expr()->andX(
                $qb->expr()->eq('o.driver', ':driver'),
                $qb->expr()->eq('o.status', ':status')
            ))
            ->setParameter('driver', $driverId)
            ->setParameter('status', $orderStatus)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findAvailableOrdersForOffers(): array
    {
        $qb = $this->createQueryBuilder('o');
        return $qb
            ->where(
                $qb->expr()->eq('o.status', ':status')
            )
            ->setParameter('status', Order::STATUS_PENDING)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findOrderById(string $orderId): Order|null
    {
        return parent::find($orderId);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveChanges(): void
    {
        $this->_em->flush();
    }
    public function add($entity): void
    {
        $this->_em->persist($entity);
    }
}