<?php

declare(strict_types=1);

namespace App\Model\Entity;

use DateTimeImmutable;
use Nextras\Orm\Relationships\ManyHasOne;

/**
 * @property int $id {primary}
 * @property string $name 
 * @property string|null $description
 * @property DateTimeImmutable $createdAt {default now}
 * 
 * @property ManyHasOne|Brand $brand {m:1 Brand::$products}
 */
class Product extends Entity
{
}
