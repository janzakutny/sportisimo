<?php

declare(strict_types=1);

namespace App\Model;

use Nextras\Orm\Mapper\Dbal\Conventions\IConventions;

class UsersMapper extends Mapper
{
    protected function createConventions(): IConventions
    {
        $conventions = parent::createConventions();
        $conventions->setMapping('eshop', 'eshop_id');
        return $conventions;
    }
}
