<?php

declare(strict_types=1);

namespace App\Model\Entity;

use Nextras\Orm\Relationships\OneHasMany;

/**
 * @property int $id {primary}
 * @property string $name
 * @property string $domain
 * 
 * @property OneHasMany|Brand[] $brands {1:m Brand::$eshop}
 * @property OneHasMany|User[] $users {1:m User::$eshop}
 */
class Eshop extends Entity
{
}
