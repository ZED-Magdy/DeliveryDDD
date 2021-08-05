<?php /** @noinspection PhpMultipleClassDeclarationsInspection */


namespace App\Application\CustomValidator;

use Attribute;
use Symfony\Component\Validator\Constraint;


/**
 * Constraint for the Unique Entity validator.
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 *
 * @author Zed Magdy <zedmagdy@gmail.com>
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class UniqueValueInEntity extends Constraint
{
    public $message = 'This {{value}} is already used.';
    public $entityClass;
    public $field;
    public function __construct($field, $entityClass, $options = null, array $groups = null, $payload = null)
    {
        $options['field'] = $field;
        $options['entityClass'] = $entityClass;
        parent::__construct($options, $groups, $payload);
        $this->field = $field;
        $this->entityClass = $entityClass;
    }

    public function getRequiredOptions()
    {
        return ['entityClass', 'field'];
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}