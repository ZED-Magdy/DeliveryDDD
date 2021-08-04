<?php


namespace App\Domain\Repository;


use App\Domain\Model\Product;

interface ProductRepositoryInterface
{
    /**
     * @param string $orderId
     * @return Product[]|array
     */
    public function findOrderProducts(string $orderId): array;
}