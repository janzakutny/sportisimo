<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Entity\Brand;
use App\Model\Entity\Eshop;

class BrandsRepository extends Repository
{
    public static function getEntityClassNames(): array
    {
        return [Brand::class];
    }


    public function createBrand(Eshop $eshop, $values)
    {
        $brand = new Brand;
        $this->attach($brand);

        $brand->eshop = $eshop->id;
        $brand->name = $values->name;
        $brand->description = $values->description;

        return $this->persistAndFlush($brand);
    }


    public function editBrand(Brand $brand, $values)
    {
        $brand->name = $values->name;
        $brand->description = $values->description;

        return $this->persistAndFlush($brand);
    }
}
