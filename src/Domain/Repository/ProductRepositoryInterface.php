<?php


namespace App\Domain\Repository;


use App\Domain\Model\Product;

interface ProductRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $orderId
     * @return Product[]
     */
    public function findOrderProducts(string $orderId): array;
}