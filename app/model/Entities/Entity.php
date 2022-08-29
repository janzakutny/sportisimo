<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Nextras\Orm\Exception\InvalidArgumentException;

class Entity extends \Nextras\Orm\Entity\Entity
{
    public function assign($values, array $whitelist = null): void
    {
        if ($whitelist !== null) {
            $whitelist = array_flip($whitelist);
        }
        if (!is_array($values) && !($values instanceof \Traversable)) {
            $givenType = gettype($values) !== 'object'
                ? gettype($values)
                : 'instance of ' . $values::class;
            throw new InvalidArgumentException(
                'Argument $values in ' . static::class . "::assign must contain either array or instance of Traversable, $givenType given.",
            );
        }
        $metadata = $this->getMetadata();
        foreach ($values as $property => $value) {
            if (($whitelist === null || isset($whitelist[$property]))
                && $metadata->hasProperty($property)
            ) {
                $this->setValue($property, $value);
            }
        }
    }
}
