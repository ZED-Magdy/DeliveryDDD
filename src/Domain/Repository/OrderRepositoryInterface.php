<?php


namespace App\Domain\Repository;


use App\Domain\Model\Order;

interface OrderRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $clientId
     * @param int $orderStatus
     * @return Order[]|array
     */
    public function findClientOrders(string $clientId, int $orderStatus): array;

    /**
     * @param string $driverId
     * @param int $orderStatus
     * @return Order[]|array
     */
    public function findDriverOrders(string $driverId, int $orderStatus): array;

    /**
     * @return Order[]|array
     */
    public function findAvailableOrdersForOffers(): array;

    /**
     * @param string $orderId
     * @return Order|null
     */
    public function findOrderById(string $orderId): Order|null;
}