<?php

declare(strict_types=1);

namespace App\Model;

use Nextras\Orm\Mapper\Dbal\Conventions\IConventions;

class ProductsMapper extends Mapper
{
    protected function createConventions(): IConventions
    {
        $conventions = parent::createConventions();
        $conventions->setMapping('brand', 'brand_id');
        return $conventions;
    }
}
