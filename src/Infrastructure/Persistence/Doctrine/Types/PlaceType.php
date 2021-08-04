<?php


namespace App\Infrastructure\Persistence\Doctrine\Types;


use App\Domain\Model\Place;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class PlaceType extends Type
{
    const PLACE = 'place';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Place) {
            $value = sprintf('PLACE(%s %s %s %s)',
                        $value->getName(), $value->getLongitude(),
                        $value->getLatitude(), $value->getAddress());
        }

        return $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        list($name, $longitude, $latitude, $address) = sscanf($value, 'POINT(%s %s %s %s)');

        return new Place($name, $longitude, $latitude, $address);
    }

    /**
     * @inheritDoc
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return 'PLACE';
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return self::PLACE;
    }
}