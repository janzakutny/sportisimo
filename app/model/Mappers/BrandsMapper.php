<?php

declare(strict_types=1);

namespace App\Model;

use Nextras\Orm\Mapper\Dbal\Conventions\IConventions;

class BrandsMapper extends Mapper
{
    protected function createConventions(): IConventions
    {
        $conventions = parent::createConventions();
        $conventions->setMapping('eshop', 'eshop_id');
        return $conventions;
    }
}
