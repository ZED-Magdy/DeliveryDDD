<?php


namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Model\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

class OfferRepository extends ServiceEntityRepository implements \App\Domain\Repository\OfferRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    /**
     * @inheritDoc
     */
    public function findOfferById(string $id): Offer|null
    {
        return parent::find($id);
    }

    /**
     * @inheritDoc
     */
    public function findOrderOffers(string $orderId): array
    {
        $qb = $this->createQueryBuilder('o');
        return $qb
            ->where($qb->expr()->eq('o.order', ':order'))
            ->setParameter('order', $orderId)
            ->getQuery()
            ->getResult();
    }
}