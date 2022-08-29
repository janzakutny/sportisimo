<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model\Entity\Brand;
use App\Model\Entity\Eshop;

interface IBrandFormFactory
{
    /**
     * @return BrandForm
     */
    public function create(Eshop $eshop, ?Brand $brand);
}
