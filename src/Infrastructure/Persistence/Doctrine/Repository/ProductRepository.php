<?php


namespace App\Infrastructure\Persistence\Doctrine\Repository;


use App\Domain\Model\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @inheritDoc
     */
    public function findOrderProducts(string $orderId): array
    {
        $qb = $this->createQueryBuilder('p');
        return $qb
            ->where($qb->expr()->eq('p.order', ':order'))
            ->setParameter('order', $orderId)
            ->getQuery()
            ->getResult();
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