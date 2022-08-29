<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Entity\Eshop;

class EshopsRepository extends Repository
{
    public static function getEntityClassNames(): array
    {
        return [Eshop::class];
    }
}
