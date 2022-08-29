<?php

declare(strict_types=1);

namespace App\Forms;

use App\Model\Entity\Product;
use App\Model\Entity\Eshop;

interface IProductFormFactory
{
    /**
     * @return ProductForm
     */
    public function create(Eshop $eshop, ?Product $brand);
}
