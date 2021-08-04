<?php


namespace App\Application\Transformers;


use App\Application\Response\OrderResponse;
use App\Application\Response\PlaceResponse;
use App\Domain\Model\Order;
use Symfony\Component\Form\DataTransformerInterface;

class OrderTransformer implements DataTransformerInterface
{

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        if($value instanceof Order)
        {
            return $this->transformOne($value);
        }
        if(is_array($value))
        {
            $data = [];
            foreach ($value as $user)
            {
                array_push($data, $this->transformOne($user));
            }
            return $data;
        }
    }

    private function transformOne(Order $order): OrderResponse
    {
        $status = match ($order->getStatus()) {
            Order::STATUS_DRAFT => "DRAFT",
            Order::STATUS_PENDING => "PENDING",
            Order::STATUS_PROCESSING => "PROCESSING",
            Order::STATUS_CONNECTING => "CONNECTING",
            Order::STATUS_DELIVERED => "DELIVERED",
            Order::STATUS_FAILED => "FAILED",
            default => null,
        };
        return new OrderResponse(
                    $order->getId(),
                    new PlaceResponse(
                        $order->getOrderPlace()->getName(),
                        $order->getOrderPlace()->getLongitude(),
                        $order->getOrderPlace()->getLatitude(),
                        $order->getOrderPlace()->getAddress(),
                    ),
                    new PlaceResponse(
                        $order->getDropPlace()->getName(),
                        $order->getDropPlace()->getLongitude(),
                        $order->getDropPlace()->getLatitude(),
                        $order->getDropPlace()->getAddress(),
                    ),
                    $order->getNote() ?? "",
                    $status,
                    $order->getPrice() ?? "",
                    $order->getPublishedAt()?->format("DD-MM-YYYY HH:MM"),
                    $order->getOfferAcceptedAt()?->format("DD-MM-YYYY HH:MM"),
                    $order->getDriverArrivedAt()?->format("DD-MM-YYYY HH:MM"),
                    $order->getFinishedAt()?->format("DD-MM-YYYY HH:MM")
        );
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value)
    {
        // TODO: Implement reverseTransform() method.
    }
}