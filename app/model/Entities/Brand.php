<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Nextras\Orm\Relationships\ManyHasOne;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * @property int $id {primary}
 * @property string $name
 * @property string|null $description
 * @property int $state {enum self::STATE_*} {default self::STATE_ACTIVE}
 * 
 * @property ManyHasOne|Eshop $eshop {m:1 Eshop::$brands}
 * 
 * @property OneHasMany|Product[] $products {1:m Product::$brand}
 */
class Brand extends Entity
{
    public const STATE_DELETED = 0;

    public const STATE_ACTIVE = 1;
}
